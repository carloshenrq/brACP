<?php

namespace Controller;

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
     * @static
     */
    public static function register()
    {
        // self::getApp()->display('account.register', [], 0, null, ['\Controller\Account', 'registerPost'], !BRACP_ALLOW_CREATE_ACCOUNT);
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
    private static function registerPost()
    {
        return [];
    }
}

