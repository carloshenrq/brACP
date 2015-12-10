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


class brAMiddlewareRoutes extends Slim\Middleware
{
    public function call()
    {
        $app = brACPSlim::getInstance();

        // Defines the route to '/' directory
        $app->get('/', function() {
            $app = brACPSlim::getInstance();
            $app->view()->display('home'.(($app->request()->isAjax()) ? '.ajax':'').'.tpl');
        });

        $app->get('/account/register', function() {
            $app = brACPSlim::getInstance();
            $app->view()->display('account.register'.(($app->request()->isAjax()) ? '.ajax':'').'.tpl');
        });

        // Rota para registrar a conta do usuário.
        $app->put('/account/register', function() {

            $app = brACPSlim::getInstance();
            // @TODO: Registro da conta no banco de dados.

        });

        // Calls next middleware.
        $this->next->call();
    }
}
