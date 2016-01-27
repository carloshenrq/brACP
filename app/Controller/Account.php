<?php

namespace Controller;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Account
{
    use \TApplication;

    /**
     * Método para dados de registro da conta
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function register(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // @Todo: Dados do cadastro.
        self::getApp()->display('account.register');
    }

    /**
     * Verifica se o usuário está logado no sistema.
     *
     * @return boolean
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['BRACP_ISLOGGEDIN']) and $_SESSION['BRACP_ISLOGGEDIN'] == true;
    }

    /**
     * Define se o usuário necessita entrar para realizar a ação.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     */
    public static function needLogin(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Define se o usuário necessita entrar para realizar a ação.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     */
    public static function needLoggout(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Método utilizado para verificar os dados de post para poder gravar no banco de dados
     *  as informações para a nova conta criada.
     *
     * @static
     * @access private
     *
     * @return array
     */
    private static function registerPost($post)
    {
        return [];
    }
}

