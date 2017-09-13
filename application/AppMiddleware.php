<?php

/**
 * Classe para execução do middle relacionado ao application
 *
 * @abstract
 */
abstract class AppMiddleware extends AppComponent
{

    /**
     * Construtor para o middleware relacionado.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->init();
    }

    /**
     * Método de inicialização para o middleware.
     *
     * @return void
     */
    protected function init()
    {
        return;
    }

    /**
     * Invoca a execução relacionada ao middleware atual.
     * E Continua a execução do sistema. Sobreescrever aqui para
     * Definir o código de execução do middleware.
     *
     * @param object $request
     * @param object $response
     * @param object $next
     */
    public function __invoke($request, $response, $next)
    {
        return $next($request, $response);
    }

}

