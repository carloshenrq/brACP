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

namespace Controller;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Caller
{
    /**
     * @var \brACPApp
     */
    private $app;

    /**
     * Define as restrições de rota para cada action chamada.
     *
     * @var array
     */
    private $routeRestrictions;

    /**
     * Define as restrições de rota para os actions informados.
     *
     * @param array $routeRestrictions
     */
    public function __construct(\brACPApp $app, array $routeRestrictions)
    {
        $this->routeRestrictions = $routeRestrictions;
        $this->app = $app;
    }

    /**
     * Obtém a instância do brACPApp em execução.
     *
     * @return \brACPApp
     */
    protected function getApp()
    {
        return $this->app;
    }

    /**
     * Verifica se a action pode ser chamada.
     *
     * @param string $action
     *
     * @return boolean
     */
    public function canCall($action)
    {
        if(isset($this->routeRestrictions[$action]))
            return $this->routeRestrictions[$action]();

        return true;
    }

    /**
     * Método utilizado para tratamento de rotas.
     */
    public static function parseRoute(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Obtém o controller que está sendo chamado para a ação.
        $controller         = '\\Controller\\' . ucfirst(trim($args['controller']));
        // Obtém o tipo de requisição que está sendo realizada.
        $method             = strtoupper($request->getMethod());
        // Obtém a action que está sendo chamada.
        $action             = strtolower($args['action']);
        // Se houver sub-action, também a declara.
        $sub_action         = (isset($args['sub-action']) ? strtolower($args['sub-action']) : null);
        // Obtém todos os parametros enviados por GET.
        $get_params         = $request->getQueryParams();
        // Obtém todos os parametros enviados por POST/PUT/ETC...
        $data_params        = $request->getParsedBody();

        // Controi o nome do método a ser invocado.
        $callMethod         = $action . (!is_null($sub_action) ? '_' . $sub_action : '') . '_' . $method;

        // Cria uma nova instância do controller solicitado.
        $instance = new $controller(\brACPApp::getInstance());

        // Verifica se o método de chamada existe no controller.
        if(method_exists($instance, $callMethod) && $instance->canCall($callMethod))
            $instance->{$callMethod}($get_params, $data_params, $response);
        else
            return $response->withStatusCode(403);

        // Retorna a resposta para o browse.
        return $response;
    }
}

