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
use \Closure;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Caller
{
    // Usa informações da classe para carregar os mods.
    use \TMod;

    /**
     * @var \brACPApp
     */
    private $app;

    /**
     * Define as restrições de rota para os actions informados.
     *
     * @param array $routeRestrictions
     */
    public function __construct(\brACPApp $app, array $routeRestrictions)
    {
        // $this->routeRestrictions = $routeRestrictions;
        $this->app = $app;

        // Define as restrições de rota.
        $this->setDefaultRestrictions($routeRestrictions);

        // Carrega todos os mods para serem aplicados neste controller.
        $this->loadMods();
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
     * Valida a string informada contra a expressão regular.
     *
     * @param string $string
     * @param string $regexp
     *
     * @return boolean Caso verdadeiro, validou com sucesso.
     */
    protected function validate($string, $regexp)
    {
        return preg_match('/^' . $regexp . '$/', $string) == 1;
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
        $action             = (isset($args['action']) ? strtolower($args['action']) : 'index');
        // Se houver sub-action, também a declara.
        $sub_action         = (isset($args['sub-action']) ? strtolower($args['sub-action']) : null);
        // Obtém todos os parametros enviados por GET.
        $get_params         = $request->getQueryParams();
        // Obtém todos os parametros enviados por POST/PUT/ETC...
        $data_params        = $request->getParsedBody();

        // Controi o nome do método a ser invocado.
        $callMethod         = $action . (!is_null($sub_action) ? '_' . $sub_action : '') . '_' . $method;

        // Obtém o application que está sendo executado.
        $app = \brACPApp::getInstance();

        // Trata os dados estatisticos de ip que estão sendo utilizados.
        if(BRACP_LOG_IP_DETAILS)
            $app->logIpDetails();

        // Cria uma nova instância do controller solicitado.
        $instance = new $controller($app);

        try
        {
            // Realiza a chamada da rota informando os parametros.
            $response = $instance->{$callMethod}($get_params, $data_params, $response);

            // Verifica se está permitindo realizar a requisição de hosts externos
            if(BRACP_ALLOW_EXTERNAL_REQUEST)
                $response = $response->withHeader('Access-Control-Allow-Origin', '*')
                                    ->withHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, X-authentication, X-client')
                                    ->withHeader('GET, POST, PUT, DELETE, OPTIONS');

            return $response;
        }
        catch(\Exception $ex)
        {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }
}

