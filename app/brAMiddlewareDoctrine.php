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

class brAMiddlewareDoctrine extends Slim\Middleware
{
    public function call()
    {
        $app = brACPSlim::getInstance();

        // Creates the EntityManager for the panel.
        $app->setEntityManager(EntityManager::create([
            'driver' => BRACP_SQL_DRIVER,
            'host' => BRACP_SQL_HOST,
            'user' => BRACP_SQL_USER,
            'password' => BRACP_SQL_PASS,
            'dbname' => BRACP_SQL_DBNAME,
        ], Setup::createAnnotationMetadataConfiguration([ BRACP_ENTITY_DIR ], BRACP_DEVELOP_MODE)));


        // Se o usuário estiver logado, realiza a aatualização dos dados de sessão
        //  e da classe.
        if($app->isLoggedIn())
        {
            // Obtém a conta do banco de dados.
            $app->acc = $app->reloadLogin($_SESSION['BRACP_ACCOUNTID']);

            // Se a conta não for encontrada, então, deleta a sessão de usuário.
            // -> Adicionado, se a conta estiver bloqueada, desloga o usuário.
            if(is_null($app->acc) || $app->acc->getState() != 0)
                brACPSlim::getInstance()->accountLoggout();
        }

        // Calls next middleware.
        $this->next->call();
    }
}
