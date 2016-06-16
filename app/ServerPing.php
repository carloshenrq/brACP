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

        // Define o status do server conectado a aplicação.
        self::getApp()->setServerStatus(null);

        // Chama o próximo middleware.
        return $next($request, $response);
    }

    private function ping($ip, $port)
    {
        $errno = $errstr = null;

        $fp = @fsockopen($ip, $port, $errno, $errstr, 60);
        $connect = $fp !== false;
        if($fp) fclose($fp);

        return $connect;
    }
}
