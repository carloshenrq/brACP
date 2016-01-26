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

        /*********************
        **********************
        **** GET - ROUTES ****
        **********************
        **********************/
        // Defines the route to '/' directory
        $app->get('/', function() {
            brACPSlim::getInstance()->display('home');
        });

        $app->get('/account/register', function() {
            brACPSlim::getInstance()->display('account.register', [], 0, null, null, !BRACP_ALLOW_CREATE_ACCOUNT);
        });

        $app->get('/account/login', function() {
            brACPSlim::getInstance()->display('account.login', [], 0, null, function() {
                return ['userid' => brACPSlim::getInstance()->getCookie('userid_rememberme')];
            });
        });

        $app->get('/account/recover(/:code)', function($code = null) {
            brACPSlim::getInstance()->recoverAccount($code);
        });

        $app->get('/account/change/password', function() {
            brACPSlim::getInstance()->display('account.change.password', [], 1, null, null);
        });

        $app->get('/account/change/mail', function() {
            brACPSlim::getInstance()->display('account.change.mail', [], 1, null, null, !BRACP_ALLOW_CHANGE_MAIL);
        });

        // Define o logout do usuário.
        $app->get('/account/logout', function() {
            brACPSlim::getInstance()->display('account.logout', [], 1, function() {
                brACPSlim::getInstance()->accountLoggout();
            });
        });

        // Rota para as doações de usuários.
        $app->get('/account/donations', function() {
            brACPSlim::getInstance()->donationDisplay();
        });

        $app->get('/account/chars', function() {
            brACPSlim::getInstance()->charsReset();
        });

        $app->get('/rankings/chars', function() {
            brACPSlim::getInstance()->display('rankings.chars', [], -1, null, function() {
                // Obtém todos os personagens do ranking.
                return [
                    'chars' => brACPSlim::getInstance()
                                ->getEntityManager()
                                ->createQuery('SELECT c FROM Model\Char c ORDER BY c.base_level DESC, c.job_level DESC, c.base_exp DESC, c.job_exp DESC')
                                ->setMaxResults(100)
                                ->getResult()
                ];
            });
        });

        $app->get('/admin/donations', function() {
            brACPSlim::getInstance()->display('admin.donations', [], 1, null, function() {
                return brACPSlim::getInstance()->adminDonations();
            }, !PAG_INSTALL, BRACP_ALLOW_ADMIN_GMLEVEL);
        });

        $app->get('/rankings/chars/economy', function() {
            brACPSlim::getInstance()->display('rankings.chars.economy', [], -1, null, function() {
                // Obtém todos os personagens do ranking.
                return [
                    'chars' => brACPSlim::getInstance()
                                ->getEntityManager()
                                ->createQuery('SELECT c FROM Model\Char c WHERE c.zeny > 0 ORDER BY c.zeny DESC')
                                ->setMaxResults(100)
                                ->getResult()
                ];
            }, !BRACP_ALLOW_RANKING_ZENY);
        });

        /*********************
        **********************
        **** POST - ROUTES ***
        **********************
        **********************/
        $app->post('/account/login', function() {
            brACPSlim::getInstance()->display('account.login', [], 0, null, function() {
                $app = brACPSlim::getInstance();

                $data = [];
                switch($app->accountLogin())
                {
                    case  1: $data =  ['message' => ['success' => 'Usuário logado com sucesso. Aguarde...']]; break;
                    case -1: $data =  ['message' => ['error' => 'Acesso negado! Você não possui permissões para realizar login.']]; break;
                    default:
                    case  0: $data =  ['message' => ['error' => 'Combinação de usuário e senha inválidos!']]; break;
                }

                // Se estiver marcado para lembrar o nome de usuário e senha.
                if(!empty($app->request()->post('remeberme')))
                    $app->setCookie('userid_rememberme', $app->request()->post('userid'));
                else
                    $app->deleteCookie('userid_rememberme');

                return array_merge($data, ['userid' => $app->request()->post('userid')]);
            });
        });

        $app->post('/account/recover', function() {
            brACPSlim::getInstance()->recoverAccount();
        });

        $app->post('/account/change/password', function(){
            brACPSlim::getInstance()->display('account.change.password', [], 1, null, function() {
                switch(brACPSlim::getInstance()->accountChangePassword())
                {
                    case  1: return ['message' => ['success' => 'Senha alterada com sucesso!']];
                    case -1: return ['message' => ['error' => 'Senha atual digitada não confere.']];
                    default:
                    case  0: return ['message' => ['error' => 'As novas senhas digitadas não conferem!']];
                }
            });
        });

        $app->post('/account/change/mail', function(){
            brACPSlim::getInstance()->display('account.change.mail', [], 1, null, function() {
                switch(brACPSlim::getInstance()->accountChangeMail())
                {
                    case  1: return ['message' => ['success' => 'Email alterado com sucesso!']];
                    case -1: return ['message' => ['error' => 'Email atual não confere com o digitado.']];
                    case -2: return ['message' => ['error' => 'Endereço de e-mail já cadastrado.']];
                    default:
                    case  0: return ['message' => ['error' => 'Novo endereço de email digitado não confere!']];
                }
            }, !BRACP_ALLOW_CHANGE_MAIL);
        });

        // Rota para registrar a conta do usuário.
        $app->post('/account/register', function() {
            brACPSlim::getInstance()->display('account.register', [], 0, null, function() {
                // Tenta realizar o registro via post.
                switch(brACPSlim::getInstance()->accountRegister())
                {
                    // Cadastro realizado com sucesso.
                    case  1: return ['message' => ['success' => 'Conta criada com sucesso. Você já pode realizar login agora.']];
                    // Senhas digitadas não conferem.
                    case -1: return ['message' => ['error' => 'As senhas digitadas não conferem!']];
                    // Emails digitados não conferem.
                    case -2: return ['message' => ['error' => 'Os e-mails digitados não conferem!']];
                    // Erro padrão.
                    default:
                    case  0: return ['message' => ['error' => 'Nome de usuário ou e-mail já cadastrado. Tente novamente!']];
                }
            }, !BRACP_ALLOW_CREATE_ACCOUNT);
        });

        // recebe o post de dados.
        $app->post('/account/donations', function() {
            brACPSlim::getInstance()->pagSeguroRequest();
        });

        // Somente atualiza os dados de doação na tabela.
        $app->post('/account/donations/transactions', function() {
            // Obtém o aplication.
            $app = brACPSlim::getInstance();

            // Encontra o objeto de doação para poder atualizar os dados no banco.
            $donation = $app->getEntityManager()
                            ->getRepository('Model\Donation')
                            ->findOneBy(['id' => $app->request()->post('donationId')]);

            // Caso a doação não seja encontrada no banco de dados.
            if(!is_null($donation))
            {
                // Define o código de transação para a doação.
                $donation->setTransactionCode($app->request()->post('transactionCode'));

                // Atualiza a doação com os dados de transação.
                $app->getEntityManager()->merge($donation);
                $app->getEntityManager()->flush();
            }
        });


        $app->post('/account/chars', function() {
            brACPSlim::getInstance()->charsReset();
        });

        // Caso o pagseguro esteja instalado, permite que receba
        //  as rotas de notificação do sistema.
        if(PAG_INSTALL)
        {
            /**
             * Caminho para receber o post do pagseguro.
             */
            $app->post('/pagseguro/notifications', function() {
                brACPSlim::getInstance()->pagSeguroRequest();
            });
        }

        /*********************
        **********************
        *** DELETE - ROUTES **
        **********************
        **********************/

        /*********************
        **********************
        **** PUT - ROUTES ****
        **********************
        **********************/

        /*********************
        **********************
        *** ERROR - ROUTES ***
        **********************
        **********************/
        $app->notFound(function() {
            brACPSlim::getInstance()->display('error.not.allowed');
        });

        // Atribuido rota para caso de error interno.
        $app->error(function(Exception $ex) {
            brACPSlim::getInstance()->display('error.not.allowed', [
                'exception' => $ex
            ]);
        });

        // Calls next middleware.
        $this->next->call();
    }
}
