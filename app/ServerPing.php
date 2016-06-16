<?php
/**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2015  brAthena, CHLFZ
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

use Model\ServerStatus;

class ServerPing
{
    use TApplication;

    /**
     * Middleware para definição das rotas.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     *
     * @return
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Indice do servidor selcionado para realizar o ping nas portas
        //  para ver se realmente está funcionando.
        $index = self::getApp()->getSession()->BRACP_SVR_SELECTED;

        // Verifica se existe um status de servidor que ainda não expirou
        //  no ping do servidor.
        $last_status = self::getCpEm()->createQuery('
            SELECT
                status
            FROM
                Model\ServerStatus status
            WHERE
                status.index = :index AND
                status.expire >= :CURDATETIME
        ')
        ->setParameter('index', $index)
        ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
        ->getOneOrNullResult();

        if(is_null($last_status))
        {
            // Realiza o ping no servidor de login.
            $login_address  = constant('BRACP_SRV_' . $index . '_LOGIN_IP');
            $login_port     = constant('BRACP_SRV_' . $index . '_LOGIN_PORT');
            $login_fp       = @fsockopen($login_address, $login_port);
            $login_result   = $login_fp !== false;
            if($login_result) fclose($login_fp);

            // Realiza o ping no servidor de personagens.
            $char_address   = constant('BRACP_SRV_' . $index . '_CHAR_IP');
            $char_port      = constant('BRACP_SRV_' . $index . '_CHAR_PORT');
            $char_fp        = @fsockopen($char_address, $char_port);
            $char_result    = $char_fp !== false;
            if($char_result) fclose($char_fp);

            // Realiza o ping no servidor de mapas.
            $map_address    = constant('BRACP_SRV_' . $index . '_MAP_IP');
            $map_port       = constant('BRACP_SRV_' . $index . '_MAP_PORT');
            $map_fp         = @fsockopen($map_address, $map_port);
            $map_result     = $map_fp !== false;
            if($map_result) fclose($map_fp);

            // Salva o status no banco de dados.
            $last_status = new ServerStatus;
            $last_status->setIndex($index);
            $last_status->setName(constant('BRACP_SRV_' . $index . '_NAME'));
            $last_status->setLogin($map_result);
            $last_status->setChar($char_result);
            $last_status->setMap($map_result);
            $last_status->setTime(date('Y-m-d H:i:s'));
            $last_status->setExpire(date('Y-m-d H:i:s', time() + BRACP_SRV_PING_DELAY));

            self::getCpEm()->persist($last_status);
            self::getCpEm()->flush();
        }

        // Define o status do server conectado a aplicação.
        self::getApp()->setServerStatus($last_status);

        // Chama o próximo middleware.
        return $next($request, $response);
    }
}
