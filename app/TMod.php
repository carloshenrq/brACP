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

/**
 * Trait para tratamento dos mods
 */
trait TMod
{
    /**
     * Variavel para os atributos carregados via mod.
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Variavél para os métodos carregados dinamicamente.
     *
     * @var array
     */
    private $methods = [];

    /**
     * Array para as rotas.
     *
     * @var array
     */
    private $routes = [];

    /** 
     * Array para as restrições de rota.
     *
     * @var array.
     */
    private $routes_restriction = [];

    /**
     * Array com as restrições para cada rota de forma padrão.
     *
     * @var array
     */
    private $default_restriction = [];

    /**
     * Método utilizado para carregar os mods da classe que está fazendo a chamada.
     *
     * @param bool $apply
     * @param string $modParsing
     *
     * @return mixed
     */
    protected function loadMods($modParsing = null, $type = null)
    {
        // Mods não estão habilitados para serem utilizados.
        if(!BRACP_ALLOW_MODS)
            return [];

        if(is_null($modParsing))
            $modParsing = get_class($this);

        // Encontra o mod que é para ser carregado.
        $modParsing = str_replace('\\', '.', $modParsing);
        $regexp = '/^' . preg_quote($modParsing) . '\.([^\.]+)\.mod\.(php|scss|js|tpl|json)$/i';

        // Obtém na pasta todos os arquivos de mods para verificar se é o que está sendo procurado.
        $modFiles = scandir(BRACP_MODS_DIR);
        $modLoaded = [];
        $modContent = [];

        // Varre os arquivos e deixa ser utilizado apenas os arquivos que foram carregados.
        foreach($modFiles as $modFile)
        {
            // Verifica a expressão contra o arquivo.
            if(!preg_match($regexp, $modFile))
                continue;

            // Obtém o conteúdo do arquivo.
            $modData = (include (BRACP_MODS_DIR . DIRECTORY_SEPARATOR .  $modFile));

            // Verifica o mod informado.
            if(!isset($modData['type']) || (!is_null($type) && $type != intval($modData['type'])))
                continue;

            // Mod é apenas para carregar os dados.
            if($modData['type'] != 0)
            {
                $modContent[] = $modData['content'];
                continue;
            }

            // Varre todos os atributos para vincular ao objeto.
            foreach($modData['attributes'] as $attribute => $defaultValue)
                $this->attributes[$attribute]           = $defaultValue;
        
            // Varre todos os métodos para vincular ao objeto.
            foreach($modData['methods'] as $methodName => $methodClosure)
                $this->methods[$methodName]             = $methodClosure;

            // Varre todas as rotas para vincular ao objeto.
            foreach($modData['routes'] as $routeName => $routeClosure)
                $this->routes[$routeName]               = $routeClosure;

            // Varre todas as restrictions para vincular ao objeto.
            foreach($modData['routes_restriction'] as $routeName => $restrictionClosure)
                $this->routes_restriction[$routeName]   = $restrictionClosure;
        }

        // Retorna o conteúdo do mod.
        return $modContent;
    }

    /**
     * Retorna o valor para um atributo definido.
     *
     * @param string $name
     *
     * @return mixed Sempre null para quando não existir. Caso contrario, valor do objeto.
     */
    public function __get($name)
    {
        if(!$this->__isset($name))
            return null;

        return $this->attributes[$name];
    }

    /**
     * Define o valor de um atributo.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if(!$this->__isset($name))
            return;

        $this->attributes[$name] = $value;
    }

    /**
     * Verifica se algum atributo está definido.
     *
     * @param string $name nome do atributo.
     *
     * @return bool Verdadeiro se definido.
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Remove um atributo definido na classe.
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if($this->__isset($name))
            $this->attributes[$name] = null;
    }

    /**
     * Faz a chamada dos métodos.
     *
     * @param string $name
     * @param array $args
     *
     * @return object
     */
    public function __call($name, $args)
    {
        // Verifica se o método invocado é uma rota com mod aplicado.
        if(isset($this->routes[$name]))
        {
            if(isset($this->routes_restriction[$name]))
            {
                $closure = Closure::bind($this->routes_restriction[$name], $this);
                if(!$closure())
                    throw new \Exception();
            }

            // Vincula a closure de rota e realiza a chamada.
            $closure = Closure::bind($this->routes[$name], $this);
            return call_user_func_array($closure, $args);
        }
        else if(isset($this->methods[$name]))
        {
            $closure = Closure::bind($this->methods[$name], $this);
            return call_user_func_array($closure, $args);
        }
        else if(method_exists($this, $name) && $this->canRunRoute($name))
        {
            $refl = new ReflectionMethod($this, $name);

            // Verifica se é private e invoca o método.
            if($refl->isPrivate())
            {
                $refl->setAccessible(true);
                $response = $refl->invokeArgs($this, $args);
                $refl->setAccessible(false);

                return $response;
            }
            else
                throw new \Exception();
        }
        else
            throw new \Exception();
    }

    /**
     * Define as restrições padrões para as rotas que serão chamadas.
     *
     * @param array $restrictions
     */
    protected function setDefaultRestrictions($restrictions)
    {
        $this->default_restriction = $restrictions;
    }

    /**
     * Verifica se a rota está restrita e não pode ser chamada.
     *
     * @param string $route
     *
     * @return bool Verdadeiro se a rota está restrita.
     */
    private function canRunRoute($route)
    {
        if(!isset($this->default_restriction[$route]))
            return true;

        $closure = Closure::bind($this->default_restriction[$route], $this);
        return $closure();
    }

}
