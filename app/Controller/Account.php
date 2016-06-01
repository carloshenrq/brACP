<?php
/**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2015  brAthena, CHLFZ
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Controller;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

use \Model\Login;
use \Model\Recover;
use \Model\EmailLog;
use \Model\Donation;
use \Model\Compensate;
use \Model\Confirmation;

use \Format;
use \Session;
use \LogWriter;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Account
{
    use \TApplication;

    /**
     * Obtém o usuário logado no sistema.
     * @var \Model\Login
     */
    private static $user = null;

    /**
     * Método para realizar login
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function login(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos para o teste.
        $data = $request->getParsedBody();

        // Dados de retorno.
        $return = ['stage' => 0, 'loginSuccess' => false, 'loginError' => false];

        // Obtém a senha que será utilizada para realizar login.
        $user_pass = ((BRACP_MD5_PASSWORD_HASH) ? hash('md5', $data['user_pass']) : $data['user_pass']);

        // Tenta obter a conta que fará login no painel de controle.
        $account = self::getApp()->getEm()
                                    ->getRepository('Model\Login')
                                    ->findOneBy(['userid' => $data['userid'], 'user_pass' => $user_pass]);

        // Se a conta digitada pelo usuário existe, então, adiciona a sessão de usuário.
        if(!is_null($account))
        {
            // Define os dados de sessão para o usuário.
            self::getApp()->getSession()->BRACP_ISLOGGEDIN = true;
            self::getApp()->getSession()->BRACP_ACCOUNTID = $account->getAccount_id();

            $return['stage'] = 1;
            $return['loginSuccess'] = true;
        }
        else
            $return['loginError'] = true;


        $response->withJson($return);
    }

    /**
     * Verifica se o usuário está logado no sistema.
     *
     * @return boolean
     */
    public static function isLoggedIn()
    {
        return isset(self::getApp()->getSession()->BRACP_ISLOGGEDIN)
                    and self::getApp()->getSession()->BRACP_ISLOGGEDIN == true;
    }

    /**
     * Obtém o usuário logado no sistema.
     *
     * @return \Model\Login
     */
    public static function loggedUser()
    {
        // Se não possui usuário em cache, obtém o usuário do banco
        //  e atribui ao cache.
        if(is_null(self::$user))
            self::$user = self::getApp()->getEm()
                                        ->getRepository('Model\Login')
                                        ->findOneBy(['account_id' => self::getApp()->getSession()->BRACP_ACCOUNTID]);
        // Retorna o usuário logado.
        return self::$user;
    }
}

