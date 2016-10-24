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
     * Obtém todos os mods carregados em memória.
     *
     * @var array
     */
    private $modsLoaded;

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
        $this->routeModdedRestrictions = [];
        $this->funcModded = [];
        $this->attrModded = [];
        $this->modsLoaded = [];

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
        // Desabilita o uso de mods para o brACP?
        if(!BRACP_ALLOW_MODS)
            return;

        // Diretório e classe que irá buscar para os mods.
        $path2mod = __DIR__ . DIRECTORY_SEPARATOR . 'mods';
        $class2mod = basename(get_class($this));

        // Se o diretório de mods estiver criado, então incializa os testes para
        // Os mods de classe.
        if(is_dir($path2mod))
        {
            // Obtém todos os arquivos mods para o controller chamado.
            $modFiles = array_filter(scandir($path2mod), function($file) use ($class2mod) {
                return preg_match('/'.$class2mod.'.([^\.]+).mod.php$/i', $file);
            });
            sort($modFiles);

            foreach($modFiles as $modFile)
            {
                // Obtém o caminho do arquivo de mod.
                $mod2file = $path2mod . DIRECTORY_SEPARATOR . $modFile;
                $modHash = hash_file('sha512', $mod2file);

                // Verifica se o mod já foi aplicado anteriormente.
                if(in_array($modHash, $this->modsLoaded))
                    continue;

                // Obtém os dados do arquvio de mod.
                $modContent = (include_once $mod2file);

                // Necessário estar definido o nome do controller
                // E Este ser igual a quem está realizando a chamada!
                if(!isset($modContent['controller'])
                    || strncasecmp($class2mod, $modContent['controller'], strlen($class2mod))
                    || !isset($modContent['name'])
                    || !isset($modContent['version'])
                    || isset($this->modsLoaded[$modContent['name']]))
                    continue;

                // Realiza o teste de versão para saber se pode carregar o mod.
                if(!version_compare($modContent['version'], BRACP_VERSION, '>='))
                    continue;

                // Define se houve erros de aplicação com mod.
                $modApplyError = false;

                // Define a var para carregar os atributos do mod.
                $_modAttribute = [];

                // Verifica se existem atributos para serem carregados no mod.
                if(isset($modContent['attributes']))
                {
                    foreach($modContent['attributes'] as $attr => $default)
                    {
                        // Não pode sobre-escrever propriedades existentes ou mesmo
                        // Atributos com mods já aplicados.
                        if(property_exists($this, $attr) || isset($this->attrModded[$attr]))
                        {
                            $modApplyError = true;
                            break;
                        }

                        // Define o mod com seu valor padrão.
                        $_modAttribute[$attr] = $default;
                    }
                }

                // Define a var para carregar os novos métodos.
                $_modMethods = [];

                // Verifica se existem funções a serem aplicadas.
                if(!$modApplyError && isset($modContent['methods']))
                {
                    foreach($modContent['methods'] as $method => $callable)
                    {
                        // Não permite que métodos sejam sobre-escritos.
                        if(method_exists($this, $method) || isset($this->funcModded[$method]) || !is_callable($callable))
                        {
                            $modApplyError = true;
                            break;
                        }

                        // Define o metodo de execucao.
                        $_modMethods[$method] = $callable;
                    }
                }

                // Define a var para as rotas que serão aplicadas.
                $_modRoutes = [];

                // Verifica se existem rotas para o mod informado.
                if(!$modApplyError && isset($modContent['routes']))
                {
                    foreach($modContent['routes'] as $route => $callable)
                    {
                        // Aqui nós podemos sobreescrever rotas padrão
                        // Mas somente se elas não tiverem sido definidas anteriormente.
                        if(isset($this->routeModded[$route]) || !is_callable($callable))
                        {
                            $modApplyError = true;
                            break;
                        }

                        // Define a rota
                        $_modRoutes[$route] = $callable;
                    }
                }

                // Define a var para restrições de rota que serão aplicadas.
                $_modRouteRestrictions = [];

                // Define a função de restrição para as rotas informadas.
                if(!$modApplyError && isset($modContent['routes_restriction']))
                {
                    // Varre todas as restrições para as rotas informadas.
                    foreach($modContent['routes_restriction'] as $route => $restriction)
                    {
                        if(!(isset($_modRoutes[$route])
                            || $this->routeModded[$route]
                            || method_exists($this, $route)))
                        {
                            $modApplyError = true;
                            break;
                        }

                        $_modRouteRestrictions[$route] = $restriction;
                    }
                }

                // Caso exista erros durante a aplicação do mod, então,
                // Não permite que o mod seja carregado.
                if($modApplyError)
                    continue;

                // Informa que o mod foi carregado.
                $this->modsLoaded[$modContent['name']] = $modHash;

                $this->attrModded = array_merge($this->attrModded, $_modAttribute);
                $this->funcModded = array_merge($this->funcModded, $_modMethods);
                $this->routeModded = array_merge($this->routeModded, $_modRoutes);
                $this->routeModdedRestrictions = array_merge($this->routeModdedRestrictions, $_modRouteRestrictions);

                unset($_modAttribute, $_modMethods, $_modRoutes, $_modRouteRestrictions);
            }

        }

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
        if($this->isModdedRoute($name))
            $clFunc = Closure::bind($this->routeModded[$name], $this);
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

