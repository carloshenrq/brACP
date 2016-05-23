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

class Route
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
        // Se o painel de controle não estiver em manutenção, então define as rotas corretas para
        //  cada endereço.
        if(!BRACP_MAINTENCE)
        {
            // Se o arquivo cache não existe, então, realiza o cache global
            if(!file_exists( __DIR__ . '/../theme.cache'))
                Themes::cacheAll();

            // Define a rota para a tela principal.
            self::getApp()->get('/', ['Controller\Home', 'index']);

            // // Adiciona a rota para alteração de temas.
            // self::getApp()->post('/theme', ['Controller\Home', 'theme']);

            // // Adiciona a rota para alteração de temas.
            // self::getApp()->post('/language', ['Controller\Home', 'language']);

            // // Adiciona a rota para doações.
            // self::getApp()->group('/donations', function() {

            //     // Verifica se o paypal está instalado se estiver permite as rotas para o paypal.
            //     if(PAYPAL_INSTALL)
            //     {
            //         $this->get('/paypal', ['Controller\Donation', 'paypal']);
            //         $this->post('/paypal/notify', ['Controller\Donation', 'paypalNotify']);
            //     }

            // });

            // // Mapeia o grupo account.
            // self::getApp()->group('/account', function() {
            //     // Rotas que não necessitam de login para entrar.

            //     // Verifica configuração se permite criação de nova conta.
            //     if(BRACP_ALLOW_CREATE_ACCOUNT)
            //     {
            //         $this->post('/register', ['Controller\Account', 'register'])
            //                 ->add(['Controller\Account', 'needLoggout']);

            //         // Caso esteja configurado para confirmar a conta criada no painel de controle
            //         // Que já exibe os dados de confirmação caso, esteja tudo ok.
            //         if(BRACP_ALLOW_MAIL_SEND && BRACP_CONFIRM_ACCOUNT)
            //         {
            //             // Registra os dados do jogador.
            //             $this->get('/register/{code}', ['Controller\Account', 'registerByCode'])
            //                     ->add(['Controller\Account', 'needLoggout']);

            //             // Re-envia o código de ativação do jogador.
            //             $this->post('/register/resend', ['Controller\Account', 'registerResendCode'])
            //                     ->add(['Controller\Account', 'needLoggout']);
            //         }
            //     }

            //     $this->post('/login', ['Controller\Account', 'login'])
            //                 ->add(['Controller\Account', 'needLoggout']);

            //     // Verifica configuração se permite recuperar uma conta.
            //     if(BRACP_ALLOW_MAIL_SEND && BRACP_ALLOW_RECOVER)
            //     {
            //         $this->post('/recover', ['Controller\Account', 'recover'])
            //                 ->add(['Controller\Account', 'needLoggout']);
            //         $this->get('/recover/{code}', ['Controller\Account', 'recoverByCode'])
            //                 ->add(['Controller\Account', 'needLoggout']);
            //     }

            //     // Rotas que necessitam de login para entrar.
            //     $this->map(['GET', 'POST'], '/change/password', ['Controller\Account', 'password'])
            //             ->add(['Controller\Account', 'needLogin']);

            //     $this->map(['GET', 'POST'], '/change/mail', ['Controller\Account', 'email'])
            //             ->add(['Controller\Account', 'needLogin']);

            //     $this->map(['GET', 'POST'], '/chars', ['Controller\Account', 'chars'])
            //             ->add(['Controller\Account', 'needLogin']);

            //     $this->get('/storage', ['Controller\Account', 'storage'])
            //             ->add(['Controller\Account', 'needLogin']);

            //     $this->get('/logout', ['Controller\Account', 'logout'])
            //             ->add(['Controller\Account', 'needLogin']);
            // });

            // Verifica se os rankings estão habilitados para serem exibidos.
            if(BRACP_ALLOW_RANKING)
            {
                // Abre o grupo de rotas para os rankings a serem exibidos.
                self::getApp()->group('/rankings', function() {
                    // Rankings para personagens.
                    $this->get('/chars', ['Controller\Ranking', 'chars']);
                    $this->get('/chars/json', ['Controller\Ranking', 'charJson']);

                    // Verifica se o ranking de zeny está habilitado a ser exibido.
                    if(BRACP_ALLOW_RANKING_ZENY)
                    {
                        $this->get('/chars/economy', ['Controller\Ranking', 'economy']);
                    }
                });
            }

            // /**
            //  * ROTAS ADMINISTRATIVAS, AQUI, SOMENTE ADMINISTRADORES PODEM ENTRAR.
            //  */
            // if(BRACP_ALLOW_ADMIN)
            // {
            //     self::getApp()->group('/admin', function() {
            //         $this->get('/update', ['Controller\Admin', 'update']);
            //         $this->get('/backup', ['Controller\Admin', 'backup']);
            //         $this->get('/theme', ['Controller\Admin', 'theme']);
            //         $this->get('/players', ['Controller\Admin', 'players']);
            //         $this->get('/donation', ['Controller\Admin', 'donation']);
            //         $this->get('/cache/flush', ['Controller\Admin', 'cacheFlush']);

            //         if(BRACP_ALLOW_MODS)
            //         {
            //             $this->get('/mods', ['Controller\Admin', 'mods']);
            //         }
            //     })
            //     ->add(['Controller\Account', 'needAdmin'])
            //     ->add(['Controller\Account', 'needLogin']);
            // }

        }

        // Chama o próximo middleware.
        return $next($request, $response);
    }
}
