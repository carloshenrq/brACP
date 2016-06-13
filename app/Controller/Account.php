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
     * Método para realizar a confirmação de contas recebido via post.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function confirmation(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos pelo post para confirmação de contas.
        $data = $request->getParsedBody();

        // Dados de retorno para informações de erro.
        $return = ['error_state' => 0, 'success_state' => false];

        // Se ambos estão definidos, a requisição é para re-envio dos dados de confirmação.
        if(isset($data['userid']) && isset($data['email']))
            $return['error_state']      = self::registerConfirmResend($data['userid'], $data['email']);
        // Se código está definido, a requisição é para confirmação da conta.
        else if(isset($data['code']))
            $return['error_state']      = self::registerConfirmCode($data['code']);

        // Define informaçõs de erro. (Caso exista)
        $return['success_state']    = $return['error_state'] == 0;

        // Responde com um objeto json informando o estado do cadastro.
        $response->withJson($return);
    }

    /**
     * Método para cadastrar uma nova conta.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function register(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos pelo post para criação de contas.
        $data = $request->getParsedBody();

        // Inicializa vetor de retorno.
        $return = ['error_state' => 0, 'success_state' => false];

        // Executa a tentativa de criar a conta do usuário no banco de dados.
        $i_create = self::registerAccount(
            $data['userid'], $data['user_pass'] , $data['user_pass_conf'],
            $data['email'] , $data['email_conf'], $data['sex'],
            false, 0
        );

        // Realiza os testes para saber o retorno do registro.
        if($i_create != 0)
            $return['error_state']      = $i_create;
        else
            $return['success_state']    = true;

        // Responde com um objeto json informando o estado do cadastro.
        $response->withJson($return);
    }

    /**
     * 
     */
    public static function accountChangePass($userid, $old_pass, $new_pass, $new_pass_conf)
    {
        // Senhas digitadas não são iguais.
        if(hash('md5', $new_pass) !== hash('md5', $new_pass_conf))
            return 1;

        // Se configurado para usar md5, então, aplica md5 para
        //  realizar os testes.
        if(BRACP_MD5_PASSWORD_HASH)
            $old_pass = hash('md5', $old_pass);

        $account = self::getApp()->getEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['userid' => $userid, 'user_pass' => $old_pass]);

        // Normalmente é senha incorreta para dar este status.
        // Não há problemas alterar senhas via recuperação de contas com state != 0
        if(is_null($account))
            return -1;

        // Realiza a alteração da senha do jogador
        //  e se configurado, notifica por e-mail.
        $i_change = self::accountSetPass($account->getAccount_id(), $new_pass);

        // Caso com erro, +1 ao erro retornado.
        return (($i_change > 0) ? (1 + $i_change) : 0);
    }

    /**
     * Aplica alteração de senhas na conta informada.
     *
     * @param int $account_id
     * @param string $password
     * @param boolean $admin (Padrão: false)
     *
     * @return int
     *      0: Senha alterada com sucesso.
     *      1: Conta não encontrada/Administrador não pode alterar senha
     *      2: Falha de restrição de pattern
     */
    public static function accountSetPass($account_id, $password, $admin = false)
    {
        // Retorna 2 para restrição de pattern
        if(!preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $password))
            return 2;

        // Realiza a busca da conta para poder realizar a alteração de senha.
        $account = self::getApp()->getEm()
                            ->getRepository('Model\Account')
                            ->findOneBy(['account_id' => $account_id]);

        // Não permite que a senha de administradores sejam alteradas
        // se o painel de controle não permitir. (Somente em modo administrador)
        if(is_null($account) || (!$admin && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL
                    && !BRACP_ALLOW_ADMIN_CHANGE_PASSWORD))
            return 1;

        if(BRACP_MD5_PASSWORD_HASH)
            $password = hash('md5', $password);

        // Salva a nova senha aplicada.
        $account->setUser_pass($password);
        self::getApp()->getEm()->merge($account);

        // Envia e-mail de notificação se não estiver em modo administrador.
        if(!$admin && BRACP_NOTIFY_CHANGE_PASSWORD)
            self::getApp()->sendMail('@@CHANGEPASS,MAIL(TITLE)',
                [$account->getEmail()],
                'mail.change.password', [
                'userid' => $account->getUserid()
            ]);

        // Status de sucesso.
        return 0;
    }

    /**
     * Método utilizado para recuperar dados das contas de usuário.
     *
     * @param string $userid
     * @param string $email
     *
     * @return int
     *  -1: Recuperação de contas desabilitado.
     *   0: Recuperação de contas realizado com sucesso.
     *   1: Dados de recuperação são inválidos.
     *   2: Falha na restrição de pattern
     */
    public static function registerRecover($userid, $email)
    {
        // -1: Recuperação de contas desabilitado.
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_ALLOW_RECOVER)
            return -1;

        // Faz validação de pattern dos campos.
        if(!preg_match('/^'.BRACP_REGEXP_USERNAME.'$/', $userid) ||
            !preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email))
            return 2;

        // Verifica se a conta digitada existe.
        $account = self::getApp()->getEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid, 'email' => $email]);

        // 1: Dados de recuperação são inválidos.
        // -> Contas do tipo administrador não podem ser recuperadas!
        if(is_null($account) || $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return 1;

        // Verifica se a recperação de senhas está ativa por código
        if(BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE)
        {
            // @Todo.: Geração do código de recuperação
        }
        else
        {
            // @Todo.: Sem recuperação de senha por código,
            //         envia a senha atual direto para o usuário.
        }

        return 0;
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
     *   ->  5: Falha na restrição de pattern
     */
    public static function registerAccount($userid, $user_pass, $user_pass_conf, $email,
                                            $email_conf, $sex, $admin = false, $group_id = 0)
    {
        if(!$admin && !BRACP_ALLOW_CREATE_ACCOUNT)
            return -1;

        if(!preg_match('/^'.BRACP_REGEXP_USERNAME.'$/', $userid) ||
            !preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $user_pass) ||
            !preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email) ||
            !preg_match('/^(M|F)$/', $sex))
            return 5;

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
                                    ->findOneBy(['email' => $email]);

        if(is_null($account))
            $account = self::getApp()->getEm()
                                    ->getRepository('Model\Login')
                                    ->findOneBy(['userid' => $userid]);

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
        // NOTA².: Modo administrador deve estar definido como falso. Se for verdadeiro, a conta não precisa ser confirmada.
        $account->setState(((!$admin && BRACP_ALLOW_MAIL_SEND && BRACP_CONFIRM_ACCOUNT) ? 11 : 0));

        self::getApp()->getEm()->persist($account);
        self::getApp()->getEm()->flush();

        if(BRACP_ALLOW_MAIL_SEND)
        {
            // Envia notificação de criação de contas.
            if($admin || !BRACP_CONFIRM_ACCOUNT)
                self::getApp()->sendMail('@@CREATE,MAIL(TITLE)',
                    [$account->getEmail()],
                    'mail.create', [
                    'userid' => $account->getUserid()
                ]);

            // Cria e envia o código de ativação do usuário, caso a configuração esteja habilitada.
            if(!$admin && BRACP_CONFIRM_ACCOUNT)
                self::registerConfirmSend($account->getAccount_id());
        }

        return 0;
    }

    /**
     * Realiza a confirmação da conta do usuário com o código que o usuário digitou.
     *
     * @param string $code
     *
     * @return int
     * -1: Configuração não permite confirmação de contas.
     *  0: Código gerado/re-enviado
     *  1: Código de ativação não encontrado.
     */
    public static function registerConfirmCode($code)
    {
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
            return -1;

        // O Código de ativação não é valido pela formatação do md5,
        //  então, ignora o código e nem verifica o banco de dados.
        if(!preg_match('/^([0-9a-f]{32})$/', $code))
            return 1;

        // Verifica se existe o código de confirmação para a conta informada
        $confirmation = self::getApp()->getEm()
                        ->createQuery('
                            SELECT
                                confirmation, login
                            FROM
                                Model\Confirmation confirmation
                            INNER JOIN
                                confirmation.account login
                            WHERE
                                confirmation.code = :code AND
                                confirmation.used = false AND
                                :CURDATETIME BETWEEN confirmation.date AND confirmation.expire
                        ')
                        ->setParameter('code', $code)
                        ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                        ->getOneOrNullResult();

        // Código de ativação não encontrado ou é inválido porque expirou ou já foi utilizado.
        if(is_null($confirmation))
            return 1;

        // Informa que o código de ativação foi utilizado e o estado da conta
        //  passa a ser 0 (ok)
        $confirmation->getAccount()->setState(0);
        $confirmation->setUsed(true);

        self::getApp()->getEm()->merge($confirmation->getAccount());
        self::getApp()->getEm()->merge($confirmation);
        self::getApp()->getEm()->flush();

        // Envia um e-mail para o usuário informando que a conta foi ativada
        //  com sucesso.
        self::getApp()->sendMail('@@RESEND,MAIL(TITLE_CONFIRMED)',
                                    [$confirmation->getAccount()->getEmail()],
                                    'mail.create.code.success',
                                    [
                                        'userid' => $confirmation->getAccount()->getUserid()
                                    ]);

        return 0;
    }

    /**
     * Reenvia o código de ativação para o usuário pelas informações
     *  de usuário e email indicado.
     *
     * @param string $userid
     * @param string $email
     *
     * @return int
     * -1: Configuração não permite confirmação de contas.
     *  0: Código gerado/re-enviado
     *  1: Conta informada não espera confirmação.
     */
    public static function registerConfirmResend($userid, $email)
    {
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
            return -1;

        // Realiza validação servidor dos patterns de usuário e senha
        //  digitados.
        if(!preg_match('/^'.BRACP_REGEXP_USERNAME.'$/', $userid) ||
            !preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email))
            return 1;

        // Obtém a conta informada.
        $account = self::getApp()->getEm()
                                ->getRepository('Model\Login')
                                ->findOneBy(['userid' => $userid, 'email' => $email, 'state' => 11]);

        // Dados não encontrados para confirmação de usuário.
        // state == 11, é uma conta aguardando confirmação.
        if(is_null($account))
            return 1;

        // Realiza o envio padrão com o código da conta informada.
        return self::registerConfirmSend($account->getAccount_id());
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
    public static function registerConfirmSend($account_id)
    {
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
            return -1;

        $account = self::getApp()->getEm()
                                ->getRepository('Model\Login')
                                ->findOneBy(['account_id' => $account_id, 'state' => 11]);

        // Dados não encontrados para confirmação de usuário.
        // state == 11, é uma conta aguardando confirmação.
        if(is_null($account))
            return 1;

        // Verifica se existe o código de confirmação para a conta informada
        $confirmation = self::getApp()->getEm()
                        ->createQuery('
                            SELECT
                                confirmation, login
                            FROM
                                Model\Confirmation confirmation
                            INNER JOIN
                                confirmation.account login
                            WHERE
                                login.account_id = :account_id AND
                                confirmation.used = false AND
                                :CURDATETIME BETWEEN confirmation.date AND confirmation.expire
                        ')
                        ->setParameter('account_id', $account->getAccount_id())
                        ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                        ->getOneOrNullResult();

        // Se não houver código de confirmação com os dados informados,
        //  então cria o registro no banco de dados.
        if(is_null($confirmation))
        {
            $confirmation = new Confirmation;
            $confirmation->setAccount($account);
            $confirmation->setCode(hash( 'md5', uniqid(rand() . microtime(true), true)));
            $confirmation->setDate(date('Y-m-d H:i:s'));
            $confirmation->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
            $confirmation->setUsed(false);

            self::getApp()->getEm()->persist($confirmation);
            self::getApp()->getEm()->flush();
        }

        // Envia o e-mail de confirmação para o usuário com o código
        //  de ativação e o link para ativação dos dados.
        // Envia o e-mail para usuário caso o painel de controle esteja com as configurações
        //  de envio ativas.
        self::getApp()->sendMail('@@RESEND,MAIL(TITLE_CONFIRM)',
                                    [$account->getEmail()],
                                    'mail.create.code',
                                    [
                                        'userid' => $account->getUserid(),
                                        'code' => $confirmation->getCode(),
                                        'expire' => $confirmation->getExpire(),
                                        'href' => BRACP_URL . 'account/register'
                                    ]);
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

