<?php

use GuzzleHttp\Client;

/**
 * Classe para fazer os tratamentos das rotas.
 */
class AppHttpClient extends AppMiddleware
{

    /**
     * Inicializador para realizar requisições externas.
     */
    protected function init()
    {
        $this->getApp()->setHttpClient($this);
        return;
    }

    /**
     * Verifica se a requisição atual é validada pelo reCaptcha.
     *
     * @param string $challengeResponse
     *
     * @return boolean Verdadeiro se a requisição for realizada com sucesso.
     */
    public function checkReCaptcha($challengeResponse)
    {
        // Cria a instância do client para realizar a requisição com o
        // Banco de dados.
        $client = $this->createClient();

        // Obtém a resposta da google com verificação
        // Do recaptcha.
        $googleResponse = json_decode($client->post('https://www.google.com/recaptcha/api/siteverify', [
            'form_params'   => [
                'secret'    => APP_RECAPTCHA_PRIV_KEY,
                'response'  => $challengeResponse,
            ]
        ])->getBody()->getContents());

        // Verifica se a requisição foi validado com sucesso.
        return ($googleResponse->success == 1);
    }

    /**
     * Cria o client de conexão do guzzle para requisições.
     *
     * @param string $uri
     * @param bool $verify
     * @param array $options
     *
     * @return GuzzleHttp\Client
     */
    public function createClient($uri = '', $verify = false, $options = [])
    {
        return new Client(array_merge([
            'base_uri'      => $uri,
            'verify'        => $verify,
        ], $options));
    }
}
