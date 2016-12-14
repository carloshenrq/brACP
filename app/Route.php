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

class Route extends brACPMiddleware
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
        // Se o painel de controle não estiver em manutenção, então define as rotas corretas para
        //  cada endereço.
        if(!BRACP_MAINTENCE)
        {
            // Se o arquivo cache não existe, então, realiza o cache global
            if(!file_exists( __DIR__ . '/../theme.cache'))
                Themes::cacheAll();

            $app = $this->getApp();

            // Redireciona a rota para o home-index
            $app->any('/', function($request, $response, $args) {
                return $response->withRedirect(BRACP_DIR_INSTALL_URL . 'home/index/');
            });

            // Adicionado mapa para todas as rotas 
            $app->any('/{controller}/', ['Controller\Caller', 'parseRoute']);

            // Adicionado mapa para todas as rotas 
            $app->any('/{controller}/{action}/[{sub_action}/]', ['Controller\Caller', 'parseRoute']);
        }

        // Chama o próximo middleware.
        return parent::__invoke($request, $response, $next);
    }
}
