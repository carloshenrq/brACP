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
     * Define as rotas custons e para o Controller.
     *
     * @var array
     */
    private $routeModded;

    /**
     * Restrições para as rotas custons relacionadas aos mods.
     *
     * @var array
     */
    private $routeModdedRestrictions;

    /**
     * Define as restrições de rota para os actions informados.
     *
     * @param array $routeRestrictions
     */
    public function __construct(\brACPApp $app, array $routeRestrictions)
    {
        $this->routeRestrictions = $routeRestrictions;
        $this->app = $app;
        $this->routeModded = [];
    }

    /**
     * Carrega todos os mods para o controller informado.
     *
     * @return void
     */
    private function loadMods()
    {
        // @Todo: Fazer algoritmo para carregar os mods aplicados ao controller.
        return;
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
     * Verifica se a rota informada está com mod aplicado
     */
    private function routeIsModded($action)
    {
        return isset($this->routeModded[$action]);
    }

    /**
     * Chama a rota com mod aplicada.
     *
     * @param string $account
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function routeModdedCall($action, $get, $post, $response)
    {
        $clFunc = \Closure::bind($this->routeModded[$action], $this);
        return $clFunc($get, $post, $response);
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
        // Se está definido a restrição por rotas
        // Chama a rota com os dados informados.
        if(isset($this->routeRestrictions[$action]))
        {
            $clFunc = \Closure::bind($this->routeRestrictions[$action], $this);
            return $clFunc();
        }

        return true;
    }

    /**
     * Verifica se a rota com mod pode ser chamada.
     *
     * @param string $action
     *
     * @return boolean
     */
    private function canCallMod($action)
    {
        // Se está definido para testar restrições das restrições de
        //  mods aplicados as rotas.
        if(isset($this->routeModdedRestrictions[$action]))
        {
            $clFunc = \Closure::bind($this->routeModdedRestrictions[$action], $this);
            return $clFunc();
        }

        return true;
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

        // Carrega os mods para o controler informado.
        $instance->loadMods();

        // Verifica se a rota está com mod aplicado para ser chamada primeiro.
        if($instance->routeIsModded($callMethod) && $instance->canCallMod($callMethod))
            return $instance->routeModdedCall($callMethod, $get_params, $data_params, $response);
        if(method_exists($instance, $callMethod) && $instance->canCall($callMethod))
            return $instance->{$callMethod}($get_params, $data_params, $response);
        else
            throw new \Slim\Exception\NotFoundException($request, $response);
    }
}

