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
        $index = BRACP_SRV_DEFAULT;

        if(isset(self::getApp()->getSession()->BRACP_SVR_SELECTED))
            $index = self::getApp()->getSession()->BRACP_SVR_SELECTED;

        // Obtém o status do cache de memória.
        $status = self::pingServer($index);

        // Define o status do server conectado a aplicação.
        self::getApp()->setServerStatus($status);

        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Realiza um ping no servidor atual.
     */
    public static function pingServer($index)
    {
        return Cache::get('BRACP_SRV_'.$index.'_STATUS_CACHE', function() {
            // Indice do servidor selcionado para realizar o ping nas portas
            //  para ver se realmente está funcionando.
            $index = BRACP_SRV_DEFAULT;

            if(isset(brACPApp::getInstance()->getSession()->BRACP_SVR_SELECTED))
                $index = brACPApp::getInstance()->getSession()->BRACP_SVR_SELECTED;

            // Obtém o status do servidor.
            $status = ServerPing::getCpEm()->createQuery('
                SELECT
                    status
                FROM
                    Model\ServerStatus status
                WHERE
                    status.index = :index AND
                    status.expire > :CURDATETIME
            ')
            ->setParameter('index', $index)
            ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
            ->getOneOrNullResult();

            // Indica que não possui status em cache para uso, então
            //  executa os pings no servidor.
            if(is_null($status))
            {
                // Cria o registro gerado para que não sejam realizados muitos pings no servidor
                //  quando for pingar, isso evita que muitos pings sejam enviados enquanto um está esperando
                //  para responder...
                $status = new ServerStatus;
                $status->setIndex($index);
                $status->setName(constant('BRACP_SRV_' . $index . '_NAME'));
                $status->setLogin(false);
                $status->setChar(false);
                $status->setMap(false);
                $status->setTime(date('Y-m-d H:i:s'));
                $status->setExpire(date('Y-m-d H:i:s', time() + BRACP_SRV_PING_DELAY));

                // Grava o registro zerado no banco de dados.
                ServerPing::getCpEm()->persist($status);
                ServerPing::getCpEm()->flush();

                // Executa os pings no servidor para obter os status.
                $loginStatus = ServerPing::ping(constant('BRACP_SRV_' . $index . '_LOGIN_IP'), constant('BRACP_SRV_' . $index . '_LOGIN_PORT'));
                $charStatus = ServerPing::ping(constant('BRACP_SRV_' . $index . '_CHAR_IP'), constant('BRACP_SRV_' . $index . '_CHAR_PORT'));
                $mapStatus = ServerPing::ping(constant('BRACP_SRV_' . $index . '_MAP_IP'), constant('BRACP_SRV_' . $index . '_MAP_PORT'));

                // Define os status reais no servidor.
                $status->setLogin($loginStatus);
                $status->setChar($charStatus);
                $status->setMap($mapStatus);

                // Salva o status real no banco de dados.
                ServerPing::getCpEm()->merge($status);
                ServerPing::getCpEm()->flush();
            }

            // Retorna o status via cache.
            return $status;
        });
    }

    /**
     * Executa um ping no servidor e porta indicados e retorna
     *  verdadeiro se executado com sucesso.
     *
     * @param string $ip Endereço IP para o ping.
     * @param int $port Porta para o ping.
     *
     * @return boolean
     */
    public static function ping($ip, $port)
    {
        try
        {
            $errno = $errstr = null;

            $fp = @fsockopen($ip, $port, $errno, $errstr, 2);
            $connect = is_resource($fp);
            if($fp) fclose($fp);

            return $connect;
        }
        catch(Exception $ex)
        {
            return false;
        }
    }
}
