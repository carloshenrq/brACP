<?php

namespace Controller;

/**
 * Controlador de rotas para 'Home'. Sempre deverá existir.
 */
class Home extends AppController
{
    /**
     * Rota padrão para todos os controllers.
     *
     * @param object $response
     *
     * @return object Objeto de resposta.
     */
    public function index_GET($response, $args)
    {
        try
        {
            return $this->render($response, 'bracp.default.tpl');
        }
        catch(\Exception $ex)
        {
            echo $ex->getMessage();
        }
    }
}

