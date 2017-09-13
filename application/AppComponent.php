<?php

/**
 * Classe para os componentes do application.
 *
 * @abstract
 */
abstract class AppComponent
{
    /**
     * Application relacionado ao componente.
     *
     * @var App
     */
    private $app;

    /**
     * Métodos customizados. Que não estão acessiveis normalmente.
     * @var Array
     */
    protected $customMethods;

    /**
     * Atributos customizados. QUe não estão acessiveis normalmente.
     * @var Array
     */
    protected $customAttributes;

    /**
     * Função para ser executada sempre que for chamada.
     * @param callback
     */
    private $_pluginExecFunc;

    /**
     * Construtor para o componente.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->setApp($app);

        // Inicializa
        $this->customMethods = [];

        // No momento em que a classe é instanciada, carrega todos os plugins
        // para a mesma.
        $this->loadPlugins();
    }

    /**
     * Define o application para o componente em execução.
     *
     * @param App $app 
     *
     * @return App Retorna a aplicação que foi definida.
     */
    public function setApp(App $app)
    {
        return ($this->app = $app);
    }

    /**
     * Obtém a application para o componente em execução.
     *
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Método utilizado para carregar todos os plugins relacionados
     * a classe atual.
     */
    private function loadPlugins()
    {
        // Se não estiver permitido carregar os plugins,
        // Então, interrompe a execução dos métodos.
        if(!APP_PLUGIN_ALLOWED)
            return;

        // Obtém o caminho para a pasta dos arquivos de plugins.
        $pluginDir = APP_PLUGIN_DIR;
        $class = get_class($this);

        // Obtém todos os arquivos contidos na pasta de plugins e filtra para
        // o arquivo de classe atual.
        $scanFiles = array_filter(scandir(APP_PLUGIN_DIR), function($file) use ($class) {
            return preg_match('/^' . preg_quote(str_replace('\\', DIRECTORY_SEPARATOR, $class), '/') . '.*\.php$/i', $file) > 0;
        });

        // Nenhum arquivo encontrado para aplicação dos plugins.
        if(count($scanFiles) == 0)
            return;

        // Faz a leitura de todos os arquivos para incluir os métodos
        // por plugins.
        foreach($scanFiles as $scanFile)
        {
            $pluginDir = APP_PLUGIN_DIR . DIRECTORY_SEPARATOR;
            // Retorna um array com os dados do plugins
            $pluginData = (include $pluginDir . $scanFile);
            $pluginCanRun = true;

            // Verifica se o mod possui instalação e se a mesma já foi
            // Realizada.
            if(isset($pluginData['install']) && isset($pluginData['installData']))
            {
                $pluginCanRun = false;

                // Se o arquivo de instalação para o plugin não existir
                // Então é necessário executar a instalação e logo na sequência criar o arquivo.
                if(!file_exists($pluginDir . 'uninstall' . $scanFile))
                {
                    // Vincula o método de instalação para ser executado.
                    $installClosure = Closure::bind($pluginData['install'], $this);
                    if($installClosure() == true)
                    {
                        $uninstallInfo = base64_encode(serialize($installData));
                        $pluginCanRun = true;
                        file_put_contents($pluginDir . 'uninstall.' . $scanFile, $uninstallInfo);
                    }
                }
                else
                    $pluginCanRun = true;
            }

            // Se o plugin pode ser executado devido as etapas de instalação
            // Do mesmo.
            if($pluginCanRun)
            {
                // Adiciona métodos ao arquivo padrão.
                if(isset($pluginData['methods']))
                {
                    foreach($pluginData['methods'] as $key => $method)
                        $this->customMethods[$key] = $method;
                }

                // Adiciona atributos ao arquivo padrão.
                if(isset($pluginData['attributes']))
                {
                    foreach($pluginData['attributes'] as $attr => $value)
                        $this->customAttributes[$attr] = $value;
                }

                // Método para inicializar o pligin.
                if(isset($pluginData['init']))
                {
                    $modInit = Closure::bind($pluginData['init'], $this);
                    $modInit();
                }

                // Método para execução customizada do plugin.
                if(isset($pluginData['exec']))
                    $this->_pluginExecFunc = $pluginData['exec'];
            }
        }
    }

    /**
     * Define uma propriedade para a classe, caso esteja declarada.
     *
     * @param string $name Nome do atributo a ser declarado.
     * @param mixed $value Valor para o atributo.
     */
    public function __set($name, $value)
    {
        // Se o indice não existe quando informado nos dados custons,
        // Então invoca o erro informando que o atributo não existe.
        if(!array_key_exists($name, $this->customAttributes))
            throw new AppException('Undefined property: '.get_class($this).'::$'.$name);

        // Define o valor para a propriedade.
        $this->customAttributes[$name] = $value;
    }

    /**
     * Obtém um atributo de forma custom. Caso seja private/protected, recomendo
     * usar get/set para obtelos.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        // Se o indice não existe quando informado nos dados custons,
        // Então invoca o erro informando que o atributo não existe.
        if(!array_key_exists($name, $this->customAttributes))
            throw new AppException('Undefined property: '.get_class($this).'::$'.$name);

        // Retorna o valor para o atributo custom.
        return $this->customAttributes[$name];
    }

    /**
     * Executa a função customizada do plugin
     */
    protected function pluginExec()
    {
        // Se a funcionalidade de execução do plugin
        // não estiver carregada, não é necessária a execução.
        if(!APP_PLUGIN_ALLOWED || empty($this->_pluginExecFunc) || !is_callable($this->_pluginExecFunc))
            return null;

        $closure = Closure::bind($this->_pluginExecFunc, $this);
        return $closure();
    }

    /**
     * Método mágico para a chamada de métodos dos componentes.
     *
     * @param string $name
     * @param array $args
     *
     * @return mixed Retorno para o método chamado.
     */
    public function __call($name, $args)
    {
        // Se o método existe, faz a chamada dele mesmo
        // Diretamente pelo object
        if(method_exists($this, $name))
            return call_user_func_array([$this, $name], $args);

        // Se o método for um método customizado e carregado
        // pelos plugins, deve aparecer aqui...
        if(array_key_exists($name, $this->customMethods))
        {
            $closure = Closure::bind($this->customMethods[$name], $this);
            return call_user_func_array($closure, $args);
        }

        // Método não existe, nem de forma interna e nem
        // de forma customizada
        throw new AppException('<strong>Fatal error:</strong> Call to undefined method ' . get_class($this) . '::' . $name . '() in <strong>' .
            __FILE__ . '</strong> on line <strong>' . __LINE__ . '</strong>');
    }
}

