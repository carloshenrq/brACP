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
     * Define o array de novas funções com mods aplicados.
     *
     * @var array
     */
    private $funcModded;

    /**
     * Define o array para os atributos com mods aplicados.
     *
     * @var array
     */
    private $attrModded;

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
        $this->funcModded = [];
        $this->attrModded = [];

        // Carrega todos os mods para serem aplicados neste controller.
        $this->loadMods();
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
     * Verifica se a action pode ser chamada.
     *
     * @param string $action
     *
     * @return boolean
     */
    public function canCall($action)
    {
        // Verifica se a rota está com o mod aplicado,
        //  Se estiver, faz a chamada de restrições por rota com mod.
        if(isset($this->routeModded[$action]))
        {
            if(isset($this->routeModdedRestrictions[$action]))
            {
                $clFunc = Closure::bind($this->routeModdedRestrictions[$action], $this);
                return $clFunc();
            }

            return true;
        }
        // Se a rota não possuir mod aplicado, então
        // Verifica se o método existe no objeto atual.
        else if(method_exists($this, $action))
        {
            if(isset($this->routeRestrictions[$action]))
            {
                $clFunc = Closure::bind($this->routeRestrictions[$action], $this);
                return $clFunc();
            }

            return true;
        }

        return false;
    }

    /** 
     * Verifica se action informada está com mod aplicado.
     *
     * @param string $action
     *
     * @return boolean
     */
    public function isModdedRoute($action)
    {
        return isset($this->routeModded[$action]);
    }

    /**
     * Normalmente é chamado para mods aplicados.
     *
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        // Inicializa o objeto de chamada.
        $clFunc = null;

        // Verifica se a rota está com mod aplicado
        // Se possuir, então, realiza as chamadas necssárias para trazer a rota
        // A Execução.
        if(isset($this->isModdedRoute($name))
            $clFunc = Closure::bind($this->routeModded[$name], $this);
            // return $clFunc($get_params, $data_params, $response);
        else if(isset($this->funcModded[$name]))
            $clFunc = Closure::bind($this->funcModded[$name], $this);

        if(!is_null($clFunc) && is_callable($clFunc))
            return call_user_func_array($clFunc, $arguments);
        else
            throw new \Exception('Call to undefined method ' . get_class($this) . '::' . $name);
    }

    /**
     * Verifica se o atributo está definido como existente.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->attrModded[$name]);
    }

    /**
     * Remove a definição do atributo.
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if($this->__isset($name))
            unset($this->attrModded[$name]);
        else
            throw new \Exception('Undefined property ' . get_class($this) . '::$' . $name);
    }

    /**
     * Obtém acesso as propriedades com mods aplicados.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if($this->__isset($name))
            return $this->attrModded[$name];
        else
            throw new \Exception('Undefined property ' . get_class($this) . '::$' . $name);
    }

    /**
     * Define acesso as propriedades com mods.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        if($this->__isset($name))
            $this->attrModded[$name] = $value;
        else
            throw new \Exception('Undefined property ' . get_class($this) . '::$' . $name);
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

        // Verifica se é possível chamar o método informado
        // Se for possível, envia os parâmetros para tratamento.
        if($instance->canCall($callMethod))
        {
            // Caso a rota esteja com mod aplicado e seja um método padrão
            // Do Controller, então, faz a chamada do mod e não da padrão.
            if($instance->isModdedRoute($callMethod) && method_exists($instance, $callMethod))
                return $instance->__call($callMethod, [$get_params, $data_params, $response]);
            else
                return $instance->{$callMethod}($get_params, $data_params, $response);
        }
        else
            throw new \Slim\Exception\NotFoundException($request, $response);
    }
}

