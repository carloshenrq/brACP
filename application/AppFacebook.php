<?php

/**
 * Classe para conexão e gerenciamento do facebook.
 */
class AppFacebook extends AppMiddleware
{
    /**
     * Informações para api do facebook. 
     * @var Facebook\Facebook
     */
    private $fbApi;

    /**
     * Inicializador para a classe de wrapper de facebook.
     */
    protected function init()
    {
        // Se o facebook estiver habilitado
        if(APP_FACEBOOK_ENABLED)
        {
            // Define o facebook api na aplicação.
            $this->getApp()->setFacebook($this);

            // Obtém informações de do api de facebook.
            $this->fbApi = new Facebook\Facebook([
                'app_id'                => APP_FACEBOOK_APP_ID,
                'app_secret'            => APP_FACEBOOK_APP_SECRET,
                'default_graph_version' => 'v2.8',
                'http_client_handler'   => 'curl',
            ]);
        }
    }

    /**
     * Obtém o api do facebook. 
     *
     * @return Facebook\Facebook
     */
    public function getApi()
    {
        return $this->fbApi;
    }

    /**
     * Obtém os dados de login para o token de acesso informado. 
     *
     * @param string $accessToken Dados de acesso token
     * @param string $fields Campos para obter na consulta.
     *
     * @return object Dados do facebook para a consulta
     */
    public function getLoginStatus($accessToken, $fields = 'name,email,id,gender,locale,verified,picture')
    {
        return $this->getApi()->get('/me?fields=' . $fields, $accessToken)->getGraphUser();
    }
}

