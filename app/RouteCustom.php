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

/**
 * Classe usada para definição de rotas customizadas na aplicação,
 * É importante ressaltar que será utilizada para aplicação de mods, não sobre-escreva a original, será usada para atualizações
 *  das versões do painel de controle.
 */
class RouteCustom
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
        // Definição para as rotas customizadas do painel de controle.
        // Adicione suas rotas custons aqui:
        //
        // Exemplo:
        //      self::getApp()->get('/custom/route', function() {
        //          // Quando entrar no caminho /custom/route irá executar este conteudo.
        //      });

        // Chama o próximo middleware.
        return $next($request, $response);
    }
}
