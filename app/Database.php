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

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Database extends brACPMiddleware
{
    /**
     * Middleware para definição das rotas.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     *
     * @return
     */
    public function __invoke($request, $response, $next)
    {
        try
        {
            $this->loadConnection();
        }
        catch(\Exception $ex)
        {
            $this->getApp()->display('error.405', [
                'exception' => $ex
            ]);
            return $response;
        }

        // Chama o próximo middleware.
        return parent::__invoke($request, $response, $next);
    }

    /**
     * Carrega a conexão com o banco de dados
     */
    private function loadConnection()
    {
        // Conexão com o painel de controle. (BRACP)
        if(is_null($this->getApp()->getEm('cp', false)))
        {
            // Define o entitymanager para o servidor.
            $cpEm = EntityManager::create([
                'driver'    => BRACP_SQL_CP_DRIVER,
                'host'      => BRACP_SQL_CP_HOST,
                'user'      => BRACP_SQL_CP_USER,
                'password'  => BRACP_SQL_CP_PASS,
                'dbname'    => BRACP_SQL_CP_DBNAME,
            ], Setup::createAnnotationMetadataConfiguration([ BRACP_ENTITY_DIR ], true));

            $this->getApp()->setEm($cpEm, 'cp');
        }

        // Conexão com o banco de dados do jogo. (item_db, mob_db, etc..)
        if(is_null($this->getApp()->getEm('db', false)))
        {
            // Define o entitymanager para o servidor.
            $dbEm = EntityManager::create([
                'driver'    => BRACP_SQL_DB_DRIVER,
                'host'      => BRACP_SQL_DB_HOST,
                'user'      => BRACP_SQL_DB_USER,
                'password'  => BRACP_SQL_DB_PASS,
                'dbname'    => BRACP_SQL_DB_DBNAME,
            ], Setup::createAnnotationMetadataConfiguration([ BRACP_ENTITY_DIR ], true));

            $this->getApp()->setEm($dbEm, 'db');
        }

        // Conexão com o banco de dados padrão para as contas (login)
        if(is_null($this->getApp()->getEm('SV' . BRACP_SRV_DEFAULT, false)))
        {
            // Define o entitymanager para o servidor.
            $dfEm = EntityManager::create([
                'driver'    => constant('BRACP_SRV_' . BRACP_SRV_DEFAULT . '_SQL_DRIVER'),
                'host'      => constant('BRACP_SRV_' . BRACP_SRV_DEFAULT . '_SQL_HOST'),
                'user'      => constant('BRACP_SRV_' . BRACP_SRV_DEFAULT . '_SQL_USER'),
                'password'  => constant('BRACP_SRV_' . BRACP_SRV_DEFAULT . '_SQL_PASS'),
                'dbname'    => constant('BRACP_SRV_' . BRACP_SRV_DEFAULT . '_SQL_DBNAME'),
            ], Setup::createAnnotationMetadataConfiguration([ BRACP_ENTITY_DIR ], true));

            $this->getApp()->setEm($dfEm, 'SV' . BRACP_SRV_DEFAULT);
        }

        // Caso o usuário tenha selecionado um banco de dados para ser utilizado (que não seja o default)
        // Abre a conexão com o outro servidor para realizar os selects.
        // (char, inventory, storage)
        if($this->getApp()->getSession()->BRACP_SVR_SELECTED !== BRACP_SRV_DEFAULT)
        {
            $index = $this->getApp()->getSession()->BRACP_SVR_SELECTED;

            if(is_null($this->getApp()->getEm('SV' . $index, false)))
            {
                // Define o entitymanager para o servidor.
                $svEm = EntityManager::create([
                    'driver'    => constant('BRACP_SRV_' . $index . '_SQL_DRIVER'),
                    'host'      => constant('BRACP_SRV_' . $index . '_SQL_HOST'),
                    'user'      => constant('BRACP_SRV_' . $index . '_SQL_USER'),
                    'password'  => constant('BRACP_SRV_' . $index . '_SQL_PASS'),
                    'dbname'    => constant('BRACP_SRV_' . $index . '_SQL_DBNAME'),
                ], Setup::createAnnotationMetadataConfiguration([ BRACP_ENTITY_DIR ], true));

                $this->getApp()->setEm($svEm, 'SV' . $index);
            }
        }
    }
}
