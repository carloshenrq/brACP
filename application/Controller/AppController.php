<?php

namespace Controller;

/**
 * Classe padrão para os demais controllers.
 *
 */
class AppController extends \AppComponent
{
    /** 
     * Dados GET da requisição.
     * @var Array
     */
    protected $get;

    /**
     * Dados POST da requisição.
     * @var Array
     */
    protected $post;

    /**
     * Dados FILES da requisição.
     * @var object
     */
    protected $files;

    /**
     * Array que possui dados de restrições de rotas.
     * @var array
     */
    private $routeRestriction;

    /**
     * Define o repositorio de dados para o controller.
     *
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repository;

    /**
     * Construtor para a classe de controller.
     *
     * @final
     *
     * @param \App $app
     * @param array $get
     * @param array $post
     * @param array $files
     */
    public final function __construct(\App $app, $get = [], $post = [], $files = [])
    {
        parent::__construct($app);

        // Dados de get, post e arquivos.
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;

        // inicializa as restrições de rota
        $this->routeRestriction = [];

        // Verifica se existe dados para verificação
        // Do reCaptcha.
        if(APP_RECAPTCHA_ENABLED
            && isset($this->post['recaptcha_response']))
        {
            // Dados de resposta para o desafio do reCaptcha
            // Remove dos dados de post o desafio
            $challengeResponse = $this->post['recaptcha_response'];
            unset($this->post['recaptcha_response']);

            // Realiza a validação dos dados de reCaptcha.
            if(!$this->getApp()->getHttpClient()->checkReCaptcha($challengeResponse))
                throw new \AppException('Falha ao verificar os dados de reCaptcha!');
        }

        // Verifica se houve o envio de challenge com o uso
        // de dados de formulário utilizando o tag <keygen/>
        // -> Necessário o uso de openssl.
        if(extension_loaded('openssl') && isset($this->post['keygen']))
        {
            // Dados de informações com keygen.
            $keygen = $this->post['keygen'];

            // Caso o desafio do keygen não seja válido.
            if(!openssl_spki_verify($keygen) || !isset($this->getApp()->getSession()->APP_KEYGEN_CHALLENGE))
                throw new \AppException('Falha ao verificar informações do formulário.');

            // Obtém os dados de desafio da tag para validação do desafio.
            $hashChallenge = openssl_spki_export_challenge($keygen);
            $keygenChallenge = $this->getApp()->getSession()->APP_KEYGEN_CHALLENGE;

            // Verifica o desafio de chaves, caso não seja o mesmo desafio
            // do calculado anteriormente, então, lança a exception.
            if($hashChallenge !== hash('sha512', $keygenChallenge))
                throw new \AppException('Falha ao válidar informações de formulário.');
        }

        // Carregar as rotas custons.
        $this->init();
    }

    /**
     * Inicializa informações do controller.
     * @return void
     */
    protected function init()
    {
        return;
    }

    /**
     * Método utilizado para chamar uma rota.
     * Também faz as devidas verificações para rotas com plugins aplicados e restrições.
     *
     * @final
     *
     * @param string $route Rota a ser acessada.
     * @param object Objeto de resposta
     *
     * @return objeto de resposta.
     */
    public final function callRoute($route, $response, $args)
    {
        // Verifica se a rota pode ser chamada.
        // Caso não possa ser chamada, irá emitir um exception
        // Informando que a rota não pode ser encontrada.
        if($this->canCallRoute($route))
        {
            // Verifica se o método existe para ser executado.
            if(method_exists($this, $route))
            {
                // Cria a instancia de reflexão para o método.
                $rfl = new \ReflectionMethod($this, $route);

                // Se o método for privado, define ele acessivel no momento
                // E Depois faz a chamada.
                if(($isPrivate = $rfl->isPrivate()) == true)
                    $rfl->setAccessible(true);

                // Executa o método e obtém a resposta do servidor.
                $response = $rfl->invokeArgs($this, [$response, $args]);

                // Se o método era privado, então, devolve a
                // Propriedade ao mesmo.
                if($isPrivate)
                    $rfl->setAccessible(false);
            }
            else if(array_key_exists($route, $this->customMethods))
            {
                $closure = \Closure::bind($this->customMethods[$route], $this);
                $response = $closure($response, $args);
            }
            else
                throw new AppControllerNotFoundException($this);
        }
        else
            throw new AppControllerNotFoundException($this);

        return $response;
    }

    /**
     * @see AppSmarty::render()
     */
    public function render($response, $template, $data = [], $cache = false, $expire = APP_CACHE_TIMEOUT, $force = false)
    {
        return $response->write($this->getApp()->getView()->render($template, $data, $cache, $expire, $force));
    }

    /**
     * Método para verificar se uma rota pode ser chamada.
     *
     * @param string $route
     *
     * @return bool Retorna verdadeiro caso a rota possa ser chamada.
     */
    public final function canCallRoute($route)
    {
        // Se a rota não estiver na listagem de restrições
        // Então, ela pode ser chamada.
        if(!isset($this->routeRestriction[$route]))
            return true;

        // Vincula o closure de rotas ao objeto atual
        // E Faz a execução para teste se é possível acessar a rota.
        $closure = \Closure::bind($this->routeRestriction[$route], $this);
        return $closure();
    }

    /**
     * Adiciona uma restrição de rota.
     *
     * @param string $route Nome da rota a ser utilizada.
     * @param callable $callable Função a ser usada para testar as restrições.
     *
     * @return void
     */
    public final function addRouteRestriction($route, $callable)
    {
        if(!is_callable($callable))
            throw new \AppException('O Parametro $callable deve ser uma função.');
        
        // Define a restrição de rota.
        $this->routeRestriction[$route] = $callable;
    }

    /**
     * Define o repositório de dados para o controller.
     *
     * @final
     * 
     * @param \Doctrine\ORM\EntityRepository $repository
     */
    public final function setRepository(\Doctrine\ORM\EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Retorna o repository de dados para o controller.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public final function getRepository()
    {
        return $this->repository;
    }

    /**
     * Obtém todos os métodos que são rotas.
     *
     * @return array
     */
    public final function getAllRoutes()
    {
        $classMethods = get_class_methods(get_class($this));
        $routesInfo = [];

        foreach($classMethods as $method)
        {
            if(preg_match('/\_(GET|POST|PUT|PATCH|DELETE)$/', $method))
                $routesInfo[] = $method;
        }

        return $routesInfo;
    }

    /**
     * Faz o tratamento para chamada da rota ao controller
     * correto.
     *
     * @param object $request
     * @param object $response
     * @param array $args
     *
     * @return object Retorna a reposta e informações.
     */
    public static function route($request, $response, $args)
    {
        // Obtém informações da rota acessada pelo cliente.
        $route = $request->getAttribute('route')->getPattern();
        $routeParams = array_values(array_filter(explode('/', $route), function($value) {
            return !empty($value) && preg_match('/^([a-z0-9_]+)$/i', $value);
        }));

        // Verifica se o controller foi definido, caso não tenha sido, define como Home
        $controller = '\\Controller\\' . ucfirst(((isset($routeParams[0])) ? array_shift($routeParams) : 'home'));

        // Verifica o action a ser executado.
        $action = ((isset($routeParams[0])) ? implode('_', $routeParams) : 'index') . '_' . strtoupper($request->getMethod());

        // Após obter os parametros de rotas, obtém também todos os dados
        // Enviados pela requisição para poder enviar ao método caso
        // Necessário.
        $get        = $request->getQueryParams();   // dados do $_GET 
        $post       = $request->getParsedBody();    // dados de $_POST
        $files      = $request->getUploadedFiles(); // dados de $_FILES

        // Remove as definições de $_POST, $_GET, $_REQUEST e $_FILES
        unset($_POST, $_GET, $_REQUEST, $_FILES);

        // Obtém a aplicação que está fazendo a chamada.
        $app = \App::getInstance();

        try
        {
            // Faz a instância do controller a ser chamado.
            $obj = new $controller($app, $get, $post, $files);

            // Se o controller não for uma instancia de AppController,
            // Então, lança uma exception.
            if(!($obj instanceof \Controller\AppController))
                throw new \AppException();


            // Resposta em relação a solicitação.
            $response = $obj->callRoute($action, $response, (object)$args);

            // Retorna o tamanho da requisição para os dados.
            $app->getFirewall()->setResponseLength($response->getBody()->getSize());

            // Retorna a resposta do servidor.
            return $response;
        }
        catch(AppControllerException $appEx)
        {
            /**
             * Retorna os dados com resposta em json
             * Para quem realizou a chamada.
             */
            return $response->withJson([
                'error' => [
                    'code' => $appEx->getCode(),
                    'message' => $appEx->getMessage(),
                    'trace' => $appEx->getTraceAsString(),
                ]
            ]);
        }
        catch(AppControllerNotFoundException $notFound)
        {
            return $notFound->getController()->render($response, 'bracp.error.tpl');
        }
        catch(\Exception $ex)
        {
            return $response->write($app->getView()->render('bracp.error.tpl', [
                'ex'    => $ex
            ]));
        }
    }
}


