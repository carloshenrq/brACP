<?php

/**
 * Classe para fazer os tratamentos das rotas.
 */
class AppRoute extends AppMiddleware
{
    /**
     * Adiciona informações de rotas para passar os parametros necessários.
     * @var array
     */
    private $routes;

    /**
     * Inicializa os dados de rotas.
     */
    protected function init()
    {
        $this->routes = (include realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'routes.php'));
    }

    /**
     * Adiciona uma expressão regular para a rota caso necessite o uso de parametrô.
     *
     * @param string $regexp Expressão regular para a rota.
     * @param string $routes Informação com os parametrôs de rota.
     */
    public function addRouteWithParams($regexp, $routes)
    {
        $this->routes = array_merge($this->routes, [
            $regexp => $routes
        ]);
    }

    /**
     * @see AppMiddleware::__invoke()
     */
    public function __invoke($request, $response, $next)
    {
        $path = $request->getUri()->getPath();
        if(substr($path, 0, 1) !== '/')
            $path = '/' . $path;

        // Se a rota possuir algo pré-configurado para parametrôs
        // Então, substitui a rota e envia com a rota definida.
        if(count($this->routes) > 0)
            foreach($this->routes as $regexp => $route)
                if(preg_match($regexp, $path))
                {
                    $path = $route;
                    break;
                }

        // Adiciona um tratamento completo e geral para requisição.
        $this->getApp()->any($path, ['\\Controller\\AppController', 'route']);

        // Move para a próxima execução.
        return parent::__invoke($request, $response, $next);
    }
}

