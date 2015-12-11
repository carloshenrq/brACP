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

use Model\Login;

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
            // Obtém o app para realizar os testes de instancia da classe.
            $app = brACPSlim::getInstance();
            $request = $app->request();

            // Instancia o objeto da conta e envia para validação.
            $acc = new Login;
            $acc->setUserid($request->put('userid'));
            $acc->setUser_pass($request->put('user_pass'));
            $acc->setSex($request->put('sex'));
            $acc->setEmail($request->put('email'));
            $acc->setBirthdate($request->put('birthdate'));

            // Se estiver configurado para realizar a aplicação do md5 na senha
            //  então aplica o hash('md5', $acc->getUser_pass())
            if(BRACP_MD5_PASSWORD_HASH)
                $acc->setUser_pass(hash('md5', $acc->getUser_pass()));

            $viewDisplayData = [ 'message' => [] ];

            // Define a mensagem de 
            if($app->createAccount($acc))
                $viewDisplayData['message']['success'] = 'Conta criada com sucesso! Você já pode realizar login agora.';
            else
                $viewDisplayData['message']['error'] = 'Nome de usuário já existe. Tente novamente.';

            // Exibe o layout 
            $app->view()->display('account.register.ajax.tpl', $viewDisplayData);
        });

        // Calls next middleware.
        $this->next->call();
    }
}
