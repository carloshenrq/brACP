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

            // Define a rota para a tela principal.
            self::getApp()->post('/server', ['Controller\Home', 'server']);

            // Mapeia o grupo account.
            self::getApp()->group('/account', function() {

                $this->get('/logout', ['Controller\Account', 'logout'])
                        ->add(['Controller\Account', '_login']);

                // Adiciona a rota de login para o usuário poder realizar login na conta.
                // -> É necessário não estar logado para realizar a ação de login.
                $this->post('/login', ['Controller\Account', 'login'])
                        ->add(['Controller\Account', '_logout']);

                // Verifica se a criação de contas está habilitada pela configuração
                //  do painel de controle.
                if(BRACP_ALLOW_CREATE_ACCOUNT)
                {
                    $this->post('/register', ['Controller\Account', 'register'])
                            ->add(['Controller\Account', '_logout']);
                }

                // Verifica se o envio de e-mails está ativo e se a confirmação de contas
                //  também está ativo por configuração.
                if(BRACP_ALLOW_MAIL_SEND && BRACP_CONFIRM_ACCOUNT)
                {
                    $this->post('/confirmation', ['Controller\Account', 'confirmation'])
                            ->add(['Controller\Account', '_logout']);
                }

                // Verifica se a recuperação de senhas está ativa.
                if(BRACP_ALLOW_MAIL_SEND && BRACP_ALLOW_RECOVER)
                {
                    $this->post('/recover', ['Controller\Account', 'recover'])
                            ->add(['Controller\Account', '_logout']);
                }

                $this->post('/password', ['Controller\Account', 'password'])
                            ->add(['Controller\Account', '_login']);

                // Alteração de email está ativo?
                if(BRACP_ALLOW_CHANGE_MAIL)
                {
                    $this->post('/email', ['Controller\Account', 'email'])
                                ->add(['Controller\Account', '_login']);
                }

                $this->get('/chars[/{type}]', ['Controller\Account', 'chars'])
                                ->add(['Controller\Account', '_login']);

                // Configuração para saber se é permitido o reset de personagem.
                if(BRACP_ALLOW_RESET_POSIT || BRACP_ALLOW_RESET_APPEAR || BRACP_ALLOW_RESET_EQUIP)
                {
                    $this->post('/char/reset/{type}', ['Controller\Account', 'charReset'])
                                    ->add(['Controller\Account', '_login']);
                }

            });

            // Verifica se doações estão habilitadas no painel de controle,
            // Apenas habilita as rotas de doação caso estejam configuradas como habilitadas.
            if(BRACP_DONATION_ENABLED)
            {
                self::getApp()->group('/donation', function() {

                    // Rota para fazer o checkout da doação.
                    $this->post('/checkout', ['Controller\Donation', 'checkout']);

                    // Permite rota de listagem das promoções apenas caso esteja habilitado em configuração.
                    if(BRACP_DONATION_SHOW_PROMO_LIST)
                        $this->get('/promotions', ['Controller\Donation', 'promoList']);

                });
            }

            // // Verifica se os rankings estão habilitados para serem exibidos.
            // if(BRACP_ALLOW_RANKING)
            // {
            //     // Abre o grupo de rotas para os rankings a serem exibidos.
            //     self::getApp()->group('/rankings', function() {
            //         // Rankings para personagens.
            //         $this->get('/chars', ['Controller\Ranking', 'chars']);
            //         $this->get('/chars/json', ['Controller\Ranking', 'charJson']);

            //         // Verifica se o ranking de zeny está habilitado a ser exibido.
            //         if(BRACP_ALLOW_RANKING_ZENY)
            //         {
            //             $this->get('/chars/economy', ['Controller\Ranking', 'economy']);
            //             $this->get('/chars/economy/json', ['Controller\Ranking', 'economyJson']);
            //         }
            //     });
            // }
        }

        // Chama o próximo middleware.
        return $next($request, $response);
    }
}
