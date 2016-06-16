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

class Database
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
        try
        {
            // Define o entitymanager para o servidor.
            $em = EntityManager::create([
                'driver' => BRACP_SQL_DRIVER,
                'host' => BRACP_SQL_HOST,
                'user' => BRACP_SQL_USER,
                'password' => BRACP_SQL_PASS,
                'dbname' => BRACP_SQL_DBNAME,
            ], Setup::createAnnotationMetadataConfiguration([ BRACP_ENTITY_DIR ], BRACP_DEVELOP_MODE));

            // Realiza a conexão no banco de dados para ver se está tudo funcionando
            //  de forma correta e se não existirá surpresas de erros quanto a conexão.
            $em->getConnection()->connect();

            // Caso tudo ocorra normalmente, define o EntityManager.
            self::getApp()->setEm($em, 'cp');
        }
        catch(\Exception $ex)
        {
            self::getApp()->display('error.405', [
                'exception' => $ex
            ]);
            return $response;
        }

        // Chama o próximo middleware.
        return $next($request, $response);
    }
}
