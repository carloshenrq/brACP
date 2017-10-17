<?php

namespace Controller;

/**
 * Controlador de rotas para 'Home'. Sempre deverá existir.
 */
class Install extends AppController
{
	/**
	 * Protege as rotas de instalação, apenas aceita requisições enquanto modo instalação.
	 */
	protected function init()
	{
		foreach($this->getAllRoutes() as $route)
		{
			$this->addRouteRestriction($route, function() {
				return (defined('APP_INSTALL_MODE') && constant('APP_INSTALL_MODE'));
			});
		}
	}

    /**
     * Rota padrão para todos os controllers.
     *
     * @param object $response
     *
     * @return object Objeto de resposta.
     */
    public function index_GET($response, $args)
    {
    	return $response->write('oi');
    }
}

