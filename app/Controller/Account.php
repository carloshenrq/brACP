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
     * Método para cadastrar uma nova conta.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function register(ServerRequestInterface $request, ResponseInterface $response, $args)
    {

    }

    /**
     * Método utilizado para criar uma conta com as informações dadas.
     *
     * @param string $userid
     * @param string $user_pass
     * @param string $user_pass_conf
     * @param string $email
     * @param string $email_conf
     * @param boolean Indica se foi criada em modo administrador. (Não testa configuração de criação de contas.)
     * @param int $group_id
     *
     * @return int
     *   -> -1: Criação de contas desabilitada no cadastro.
     *   ->  0: Conta criada com sucesso
     *   ->  1: Usuário em uso.
     *   ->  2: Senhas digitadas não conferem.
     *   ->  3: E-mails digitados não conferem.
     *   ->  4: A Criação deste tipo de conta somente é possivel em modo administrador.
     */
    public static function createAccount($userid, $user_pass, $user_pass_conf, $email,
                                            $email_conf, $admin = false, $group_id = 0)
    {
        if(!$admin && !BRACP_ALLOW_CREATE_ACCOUNT)
            return -1;

        if(hash('md5', $user_pass) !== hash('md5', $user_pass_conf))
            return 2;

        if(hash('md5', $email) !== hash('md5', $email_conf))
            return 3;

        if(!$admin && $group_id >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return 4;

        $account = null;
        if(BRACP_MAIL_REGISTER_ONCE)
            $account = self::getApp()->getEm()
                                    ->getRepository('Model\Login')
                                    ->findOneBy(['email' => $email);

        if(is_null($account))
            $account = self::getApp()->getEm()
                                    ->getRepository('Model\Login')
                                    ->findOneBy(['userid' => $userid);

        // Se a conta foi encontrada nos registros de email ou login
        //  então, retorna usuário em uso.
        if(!is_null($account))
            return 1;

        if(BRACP_MD5_PASSWORD_HASH)
            $user_pass = hash('md5', $user_pass);

        // Cria o registro da conta no banco de dados.
        $account = new Login;
        $account->setUserid($userid);
        $account->setUser_pass($user_pass);
        $account->setEmail($email);
        $account->setGroup_id($group_id);
        // NOTA.: NÃO USAR state=5 PARA CONTAS EM CONFIRMAÇÃO,
        //        O STATE=5 É DEFINIDO PARA O USUÁRIO
        //        QUANDO FOR UTILIZADO O COMANDO @BLOCK POR UM GM DENTRO DO JOGO.
        $account->setState(((BRACP_ALLOW_MAIL_SEND && BRACP_CONFIRM_ACCOUNT) ? 11 : 0));

        self::getApp()->getEm()->persist($account);
        self::getApp()->getEm()->flush();

        if(BRACP_ALLOW_MAIL_SEND)
        {
            // Envia notificação de criação de contas.
            self::getApp()->sendMail('@@CREATE,MAIL(TITLE)', [
                $account->getUserid() => $account->getEmail()
            ], 'mail.create', [
                'userid' => $account->getUserid()
            ]);

            // Cria e envia o código de ativação do usuário, caso a configuração esteja habilitada.
            if(BRACP_CONFIRM_ACCOUNT)
                self::createConfirmSend($account->getAccount_id());
        }

        return 0;
    }

    /**
     * Método para enviar o código de confirmação para a conta.
     * Se já existir um código de confirmação, ele será reenviado.
     * Se não existir, será riado um novo código e enviado ao jogador.
     *
     * -> Somente serão enviados os códigos de ativação para contas com state = 11
     *
     * @param integer $account_id
     *
     * @return int
     * -1: Configuração não permite confirmação de contas.
     *  0: Código gerado/re-enviado
     *  1: Conta informada não espera confirmação.
     */
    public static function createConfirmSend($account_id)
    {
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
            return -1;

        $account = self::getApp()->getEm()
                                ->getRepository('Model\Login')
                                ->findOneBy(['account_id' => $account_id, 'state' => 11);

        // Dados não encontrados para confirmação de usuário.
        // state == 11, é uma conta aguardando confirmação.
        if(is_null($account))
            return 1;

        // @Todo.: Enviar confirmação da conta para o usuário.

        return 0;
    }

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

        // Verifica os padrões para recepção dos parametros de usuário e senha verificando
        //  se os dados estão de acordo com os patterns informados.
        if(    !isset($data['userid'])
            || !isset($data['user_pass'])
            || !preg_match('/^'.BRACP_REGEXP_USERNAME.'$/', $data['userid'])
            || !preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $data['user_pass']))
        {
            // Informa que ocorreu erro durante o retorno.
            $return['loginError'] = true;
        }
        else
        {
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
        }

        // Retorna resposta do json para informar ao usuário os erros
        $response->withJson($return);
    }

    /**
     * Método para realizar logout
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function logout(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Apaga a sessão do usuário para ser deslogado.
        unset(self::getApp()->getSession()->BRACP_ISLOGGEDIN,
            self::getApp()->getSession()->BRACP_ACCOUNTID);

        self::getApp()->display('account.logout');
    }

    /**
     * Método middleware para testar se o usuário está logado na conta.
     * Quando este middleware é chamado, ele apenas permite que a rota seja executada até
     *  o final, quando o usuário estiver logado.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $request
     * @param object $next
     *
     * @return ResponseInterface
     */
    public static function _login(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Se o usuário estiver logado, então envia diretamente para continuar
        //  a execução da rota.
        if(self::isLoggedIn())
            return $next($request, $response);

        // Se o usuário não estiver logado, então o envia diretamente para
        //  a tela de mensagem de erro de necessário realizar o logout do sistema
        //  para continuar.
        self::getApp()->display('account.error.login');
        return $response;
    }

    /**
     * Método middleware para testar se o usuário não está logado na conta.
     * Quando este middleware é chamado, ele apenas permite que a rota seja executada até
     *  o final, quando o usuário não estiver logado.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $request
     * @param object $next
     *
     * @return ResponseInterface
     */
    public static function _logout(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Se o usuário não estiver logado, então envia diretamente para continuar
        //  a execução da rota.
        if(!self::isLoggedIn())
            return $next($request, $response);

        // Se o usuário estiver logado, então o envia diretamente para
        //  a tela de mensagem de erro de necessário realizar o logout do sistema
        //  para continuar.
        self::getApp()->display('account.error.logout');
        return $response;
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

