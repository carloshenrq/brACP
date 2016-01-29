<?php

namespace Controller;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

use \Model\Login;
use \Model\Recover;
use \Model\EmailLog;

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
        self::getApp()->display('account.login',
                                    (($request->isPost()) ? self::loginAccount($request->getParsedBody()):[]));
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
        unset($_SESSION['BRACP_ISLOGGEDIN'], $_SESSION['BRACP_ACCOUNTID']);
        self::getApp()->display('account.logout');
    }

    /**
     * Método para dados de registro da conta
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function register(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.register',
                                    (($request->isPost()) ? self::registerAccount($request->getParsedBody()):[]));
    }

    /**
     * Método para recuperar a conta do usuário.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function recover(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Obtém o código de recuperação.
        $code = ((isset($args['code'])) ? $args['code'] : null);

        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.recover',
                                    (($request->isPost() || !is_null($code)) ? self::recoverAccount($request->getParsedBody(), $code):[]));
    }

    /**
     * Método para alteração de senha do usuário.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function password(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.change.password', (($request->isPost()) ? self::passwordAccount($request->getParsedBody()):[])  );
    }

    /**
     * Método para alteração de email do usuário.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function email(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados a serem enviados a tela.
        $data = $mailChanges = [];
        if($request->isPost())
            $data = self::emailAccount($request->getParsedBody());

        // Se estiver configurado para realizar leitura
        if(BRACP_MAIL_SHOW_LOG)
        {
            // Obtém todas as ultimas alterações de e-mail.
            $mailChanges = self::getApp()->getEm()
                            ->createQuery('
                                SELECT
                                    log, login
                                FROM
                                    Model\EmailLog log
                                INNER JOIN
                                    log.account login
                                WHERE
                                    login.account_id = :account_id
                                ORDER BY
                                    log.id DESC
                            ')
                            ->setParameter('account_id', self::loggedUser()->getAccount_id())
                            ->setMaxResults(10)
                            ->getResult();
        }

        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.change.mail', array_merge($data,
                                                        ['mailChange' => $mailChanges]));
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
                                        ->findOneBy(['account_id' => $_SESSION['BRACP_ACCOUNTID']]);
        // Retorna o usuário logado.
        return self::$user;
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
        // Se usuário não logado, pede ao usuário para logar ao endereço.
        if(!self::isLoggedIn())
        {
            self::getApp()->display('account.error.login');
            return $response;
        }

        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Define se o usuário necessita sair para realizar a ação.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     */
    public static function needLoggout(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Verifica se o usuário está logado.
        if(self::isLoggedIn())
        {
            self::getApp()->display('account.error.logged');
            return $response;
        }

        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Define que o usuário precisa ser nivel administrador para usar.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     */
    public static function needAdmin(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Verifica se o usuário está logado e se é adminsitrador.
        if(!self::isLoggedIn() || self::loggedUser()->getGroup_id() < BRACP_ALLOW_ADMIN_GMLEVEL)
        {
            self::getApp()->display('error.not.allowed');
            return $response;
        }

        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Define que o usuário não pode ser nivel administrador para usar.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     */
    public static function notNeedAdmin(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        // Verifica se o usuário está logado e é nivel adminsitrador.
        if(!self::isLoggedIn() || self::loggedUser()->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
        {
            self::getApp()->display('error.not.allowed');
            return $response;
        }

        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Altera o endereço de e-mail da conta do jogador.
     *
     * @param int $account_id
     * @param string $email
     *
     * @return boolean
     */
    public static function changeMail($account_id, $email, $checkDelay = true)
    {
        // Se por configuração está habilitado a trocar de endereço de e-mail.
        // Se não estiver, retorna falso.
        // -> Verifica se o endereço de e-mail já está cadastrado.
        if(!BRACP_ALLOW_CHANGE_MAIL || BRACP_MAIL_REGISTER_ONCE && self::checkMail($email))
            return false;

        // Obtém a conta que fará a alteração de endereço de e-mail.
        $account = self::getApp()->getEm()->getRepository('Model\Login')->findOneBy(['account_id' => $account_id]);

        // Verifica se a conta enviada existe.
        if(is_null($account))
            return false;

        // Verifica se a conta é do tipo administrador. Se for, não permite que
        //  o e-mail seja alterado.
        if($account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return false;

        // Verifica se existe delay para alteração de endereço de e-mail e se houver
        //  verifica a data da ultima alteração de e-mail para não permitir que
        //  seja alterado o endereço dentro do delay informado.
        if($checkDelay && BRACP_CHANGE_MAIL_DELAY > 0)
        {
            // Conta os registros de log para não permitir alterações
            //  de e-mail dentro do delay informado.
            $count = self::getApp()->getEm()
                                    ->createQuery('
                                        SELECT
                                            count(log)
                                        FROM
                                            Model\EmailLog log
                                        INNER JOIN
                                            log.account login
                                        WHERE
                                            login.account_id = :account_id AND
                                            log.date >= :DELAYDATE
                                    ')
                                    ->setParameter('account_id', $account->getAccount_id())
                                    ->setParameter('DELAYDATE', date('Y-m-d H:i:s', time() - (BRACP_CHANGE_MAIL_DELAY*60)))
                                    ->getSingleScalarResult();

            // Caso existam resultados para obter os dados de delay.
            if($count > 0)
                return false;
        }

        // Obtém o e-mail antigo da conta para enviar a notificação.
        $oldEmail = $account->getEmail();

        // Atualiza o endereço de e-mail.
        $account->setEmail($email);

        // Salva a alteração no banco de dados.
        self::getApp()->getEm()->merge($account);
        self::getApp()->getEm()->flush();

        // Cria o log de alterações para mudanças de endereço de e-mail.
        $log = new EmailLog;
        $log->setAccount($account);
        $log->setFrom($oldEmail);
        $log->setTo($email);
        $log->setDate(date('Y-m-d H:i:s'));

        self::getApp()->getEm()->persist($log);
        self::getApp()->getEm()->flush();

        // Verifica se as notificações para envio de e-mail estão ativas.
        if(BRACP_NOTIFY_CHANGE_MAIL)
        {
            // Envia um email para o endereço antigo informando a alteração.
            self::getApp()->sendMail('Notificação: Alteração de E-mail',
                                        [$log->getFrom()],
                                        'mail.change.mail',
                                        [
                                            'userid' => $account->getUserid(),
                                            'mailOld' => $log->getFrom(),
                                            'mailNew' => $log->getTo(),
                                        ]);

            // Envia o e-mail para o novo endereço.
            self::getApp()->sendMail('Notificação: Alteração de E-mail',
                                        [$log->getTo()],
                                        'mail.change.mail',
                                        [
                                            'userid' => $account->getUserid(),
                                            'mailOld' => $log->getFrom(),
                                            'mailNew' => $log->getTo(),
                                        ]);
        }

        // Retorna verdadeiro para a alteração de endereço de e-mail.
        return true;
    }

    /**
     * Altera a senha do usuário e envia a notificação para o e-mail da conta.
     *
     * @param int $account_id
     * @param string $user_pass
     *
     * @return boolean
     */
    public static function changePass($account_id, $user_pass)
    {
        // Atualiza a senha do usuário.
        $changed = self::getApp()->getEm()
                    ->createQuery('
                        UPDATE
                            Model\Login login
                        SET
                            login.user_pass = :user_pass
                        WHERE
                            login.account_id = :account_id
                    ')
                    ->setParameter('account_id', $account_id)
                    ->setParameter('user_pass', ((BRACP_MD5_PASSWORD_HASH) ? hash('md5', $user_pass):$user_pass))
                    ->execute() > 0;

        // Verifica se a senha foi alterada e se é necessário o envio
        if($changed && BRACP_ALLOW_MAIL_SEND && BRACP_NOTIFY_CHANGE_PASSWORD)
        {
            // Obtém o objeto da conta para enviar a notificação por e-mail.
            $account = self::getApp()->getEm()
                                        ->getRepository('Model\Login')
                                        ->findOneBy(['account_id' => $account_id]);

            // Envia o e-mail com os dados de recuperação do usuário.
            self::getApp()->sendMail('Notificação: Alteração de Senha', [$account->getEmail()],
                'mail.change.password', [
                    'userid' => $account->getUserid()
                ]);
        }

        return $changed;
    }

    /**
     * Método utilizado para alterar o e-mail da conta.
     *
     * @static
     *
     * @return array
     */
    public static function emailAccount($data)
    {
        // Verifica se a conta é do tipo administrador e não deixa realizar a alteração de e-mail
        if(self::loggedUser()->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return ['message' => ['error' => 'Usuários administradores não podem realizar alteração de e-mail.']];

        // Verifica se o email atual digitado é igual ao email atual.
        if(hash('md5', self::loggedUser()->getEmail()) !== hash('md5', $data['email']))
            return ['message' => ['error' => 'E-mail atual não confere com o digitado.']];

        // Verifica se o email novo digitado é igual ao email de confirmação.
        if(hash('md5', $data['email_new']) !== hash('md5', $data['email_conf']))
            return ['message' => ['error' => 'Os e-mails digitados não conferem.']];

        // Verifica se o email atual é igual ao email novo digitado.
        if(hash('md5', self::loggedUser()->getEmail()) === hash('md5', $data['email_new']))
            return ['message' => ['error' => 'O Novo endereço de e-mail não pode ser igual ao atual.']];

        // Verifica se foi possivel alterar o endereço de e-mail do usuário.
        if(self::changeMail(self::loggedUser()->getAccount_id(), $data['email_new']))
            return ['message' => ['success' => 'Seu endereço de e-mail foi alterado com sucesso.']];
        else
            // Ocorre quando o endereço de e-mail já está em uso.
            return ['message' => ['error' => 'Ocorreu um erro durante a alteração do seu endereço.']];
    }

    /**
     * Método utilizado para alterar a senha da conta.
     *
     * @static
     *
     * @return array
     */
    public static function passwordAccount($data)
    {
        // Se administradores não podem atualizar senha, verifica nivel do usuário logado e
        //  retorna erro caso nivel administrador.
        if(!BRACP_ALLOW_ADMIN_CHANGE_PASSWORD && self::loggedUser()->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return ['message' => ['error' => 'Usuários do tipo administrador não podem realizar alteração de senha.']];

        // Obtém a senha atual do jogador para aplicação do md5 na comparação da senha.
        $user_pass = self::loggedUser()->getUser_pass();
        if(!BRACP_MD5_PASSWORD_HASH)
            $user_pass = hash('md5', $user_pass);

        // Verifica senha atual digitada.
        if(hash('md5', $data['user_pass']) !== $user_pass)
            return ['message' => ['error' => 'Senha atual digitada não confere.']];

        // Verifica novas senhas digitadas.
        if(hash('md5', $data['user_pass_new']) !== hash('md5', $data['user_pass_conf']))
            return ['message' => ['error' => 'Novas senhas digitadas não conferem.']];

        // Verifica se a senha nova é igual a anterior.
        if(hash('md5', $data['user_pass_new']) === $user_pass)
            return ['message' => ['error' => 'Sua nova senha não pode ser igual a senha anterior.']];

        // Senha alterada com sucesso.
        if(self::changePass(self::loggedUser()->getAccount_id(), $data['user_pass_new']))
            return ['message' => ['success' => 'Sua senha foi alterada com sucesso!']];
        else
            return ['message' => ['error' => 'Ocorreu um erro durante a alteração de sua senha.']];
    }

    /**
     * Método utilizado para recuperar a conta.
     *
     * @static
     *
     * @return array
     */
    public static function recoverAccount($data, $code = null)
    {
        // Se o código não foi enviado.
        if(!is_null($code) && (BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE))
        {
            // Verificação do banco de dados para saber se o código de recuperação foi
            //  enviado com sucesso.
            $recover = self::getApp()->getEm()
                                        ->createQuery('
                                            SELECT
                                                recover, login
                                            FROM
                                                Model\Recover recover
                                            INNER JOIN
                                                recover.account login
                                            WHERE
                                                recover.code = :code AND
                                                recover.used = false AND
                                                :CURDATETIME BETWEEN recover.date AND recover.expire
                                        ')
                                        ->setParameter('code', $code)
                                        ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                                        ->getOneOrNullResult();

            // Não foi encontrado código de recuperação para a conta.
            if(is_null($recover))
                return ['message' => ['error' => 'O Código de recuperação já foi utilizado ou é inválido.']];

            // Calcula a nova senha de usuário.
            $user_pass = self::getApp()->randomString(BRACP_RECOVER_STRING_LENGTH, BRACP_RECOVER_RANDOM_STRING);

            // Realiza a alteração da senha da conta.
            if(self::changePass($recover->getAccount()->getAccount_id(), $user_pass))
            {
                // Atualiza o código de recuperação marcando como utilizado e atualiza a tabela.
                $recover->setUsed(true);

                self::getApp()->getEm()->merge($recover);
                self::getApp()->getEm()->flush();

                // Envia o e-mail com os dados de recuperação do usuário.
                self::getApp()->sendMail('Redefinição de Senha', [$recover->getAccount()->getEmail()],
                    'mail.recover', [
                        'userid' => $recover->getAccount()->getUserid(),
                        'password' => $user_pass
                    ]);

                return ['message' => ['success' => 'A Nova senha foi enviada para seu endereço de e-mail.']];
            }
            else
            {
                return ['message' => ['error' => 'Não foi possível recuperar a senha de usuário.']];
            }
        }
        else
        {
            // Obtém a conta que está sendo solicitada a requisição para 
            //  recuperação de senha.
            $account = self::getApp()->getEm()
                                        ->getRepository('Model\Login')
                                        ->findOneBy(['userid' => $data['userid'], 'email' => $data['email']]);

            // Objeto da conta não encontrado.
            if(is_null($account))
                return ['message' => ['error' => 'Combinação de usuário e e-mail não encontrados.']];

            // Se o painel de controle estiver configurado para usar md5 ou recuperação de código
            //  via e-mail, então, inicializa os códigos.
            if(BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE)
            {
                // Verifica se algum código de recuperação já foi criado dentro do periodo
                //  deconfiguração.
                $recover = self::getApp()->getEm()
                                            ->createQuery('
                                                SELECT
                                                    recover, login
                                                FROM
                                                    Model\Recover recover
                                                INNER JOIN
                                                    recover.account login
                                                WHERE
                                                    login.account_id = :account_id AND
                                                    recover.used = false AND
                                                    :CURDATETIME BETWEEN recover.date AND recover.expire
                                            ')
                                            ->setParameter('account_id', $account->getAccount_id())
                                            ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                                            ->getOneOrNullResult();

                // Se não foi encontrado o objeto de recuperação, será criado um novo
                //  registro do banco de dados.
                if(is_null($recover))
                {
                    // Inicializa o objeto de recuperação da conta.
                    $recover = new Recover;
                    $recover->setAccount($account);
                    $recover->setCode(hash('md5', microtime(true)));
                    $recover->setDate(date('Y-m-d H:i:s'));
                    $recover->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
                    $recover->setUsed(false);

                    // Grava o código no banco de dados e envia o email.
                    self::getApp()->getEm()->persist($recover);
                    self::getApp()->getEm()->flush();
                }

                // Envia o e-mail para o usuário.
                self::getApp()->sendMail('Recuperação de Usuário', [$account->getEmail()],
                    'mail.recover.code', [
                        'userid' => $account->getUserid(),
                        'code' => $recover->getCode(),
                        'expire' => $recover->getExpire(),
                        'href' => BRACP_URL . BRACP_DIR_INSTALL_URL . 'account/recover'
                    ]);

                // Informa que o código de recuperação foi enviado ao e-mail do usuário.
                return ['message' => ['success' => 'Foi enviado um e-mail contendo os dados de recuperação. Verifique seu e-mail.']];
            }
            else
            {
                // Envia o e-mail com os dados de recuperação do usuário.
                self::getApp()->sendMail('Recuperação de Senha', [$account->getEmail()],
                    'mail.recover', [
                        'userid' => $account->getUserid(),
                        'password' => $account->getUser_pass()
                    ]);

                // Retorna informação que foi retornado os dados da conta.
                return ['message' => ['success' => 'Os dados de sua conta foram enviados ao seu e-mail.']];
            }
        }
    }

    /**
     * Método utilizado para realizar login na conta.
     *
     * @static
     *
     * @return array
     */
    public static function loginAccount($data)
    {
        // Obtém a senha que será utilizada para realizar login.
        $user_pass = ((BRACP_MD5_PASSWORD_HASH) ? hash('md5', $data['user_pass']) : $data['user_pass']);

        // Tenta obter a conta que fará login no painel de controle.
        $account = self::getApp()->getEm()
                                    ->getRepository('Model\Login')
                                    ->findOneBy(['userid' => $data['userid'], 'user_pass' => $user_pass]);

        // Se a conta retornada for igual a null, não foi encontrada
        //  Então, retorna mensagem de erro.
        if(is_null($account))
            return ['message' => ['error' => 'Combinação de usuário e senha incorretos.']];

        // Se a conta do usuário é inferior ao nivel mínimo permitido
        //  para login, então retorna mensagem de erro.
        if($account->getGroup_id() < BRACP_ALLOW_LOGIN_GMLEVEL || $account->getState() != 0)
            return ['message' => ['error' => 'Acesso negado. Você não pode realizar login.']];

        // Define os dados de sessão para o usuário.
        $_SESSION['BRACP_ISLOGGEDIN'] = true;
        $_SESSION['BRACP_ACCOUNTID'] = $account->getAccount_id();

        // Retorna mensagem de login realizado com sucesso.
        return ['message' => ['success' => 'Login realizado com sucesso. Aguarde...']];
    }

    /**
     * Método utilizado para verificar os dados de post para poder gravar no banco de dados
     *  as informações para a nova conta criada.
     *
     * @static
     *
     * @return array
     */
    public static function registerAccount($data)
    {
        if(hash('md5', $data['user_pass']) !== hash('md5', $data['user_pass_conf']))
            return ['message' => ['error' => 'As senhas digitadas não conferem!']];

        // Verifica se os emails enviados são iguais.
        if(hash('md5', $data['email']) !== hash('md5', $data['email_conf']))
            return ['message' => ['error' => 'Os endereços de e-mail digitados não conferem!']];

        // Verifica se já existe usuário cadastrado para o userid indicado.
        if(self::checkUser($data['userid']) || (BRACP_MAIL_REGISTER_ONCE && self::checkMail($data['email'])))
            return ['message' => ['error' => 'Nome de usuário ou endereço de e-mail já está em uso.']];

        // Se a senha for hash md5, troca o valor para hash-md5.
        if(BRACP_MD5_PASSWORD_HASH)
           $data['user_pass'] = hash('md5', $data['user_pass']);

        // Cria o objeto da conta para ser salvo no banco de dados.
        $account = new Login;
        $account->setUserid($data['userid']);
        $account->setUser_pass($data['user_pass']);
        $account->setSex($data['sex']);
        $account->setEmail($data['email']);

        // Salva os dados na tabela de usuário.
        self::getApp()->getEm()->persist($account);
        self::getApp()->getEm()->flush();

        // Envia o e-mail para usuário caso o painel de controle esteja com as configurações
        //  de envio ativas.
        self::getApp()->sendMail('Conta Registrada', [$account->getEmail()],
                                    'mail.create', ['userid' => $account->getUserid()]);

        // Retorna mensagem que a conta foi criada com sucesso.
        return ['message' => ['success' => 'Sua conta foi criada com sucesso! Você já pode realizar login.']];
    }

    /**
     * Verifica se existe o usuário indicado no banco de dados.
     *
     * @param string $userid
     *
     * @return boolean
     */
    public static function checkUser($userid)
    {
        // Verifica se existe algum usuário com o id indicado.
        return !is_null(self::getApp()
                                ->getEm()
                                ->getRepository('Model\Login')
                                ->findOneBy(['userid' => $userid]));
    }

    /**
     * Verifica se o email indicado já existe no banco de dados.
     *
     * @param string $email
     *
     * @return boolean
     */
    public static function checkMail($email)
    {
        return !is_null(self::getApp()
                                ->getEm()
                                ->getRepository('Model\Login')
                                ->findOneBy(['email' => $email]));
    }
}

