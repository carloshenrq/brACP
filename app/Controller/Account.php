<?php

namespace Controller;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

use \Model\Login;

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
        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.register',
                                    (($request->isPost()) ? self::registerAccount($request->getParsedBody()):[]));
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
           $data['user_pass'] = hash('md5', $data['user_pass'])

        try
        {
            // Cria o objeto da conta para ser salvo no banco de dados.
            $account = new Login;
            $account->setUserid($data['userid']);
            $account->setUser_pass($data['user_pass']);
            $account->setSex($data['sex']);
            $account->setEmail($data['email']);

            // Salva os dados na tabela de usuário.
            self::getApp()->getEm()->persist($account);
            self::getApp()->getEm()->flush();

            // @Todo: Código para envio dos e-mails.

            return ['message' => ['success' => 'Sua conta foi criada com sucesso! Você já pode realizar login.']];
        }
        catch(\Exception $ex)
        {
            return ['message' =>
                        ['error' =>
                            'Não foi possivel criar sua conta de usuário.' . ((BRACP_DEVELOP_MODE) ? '<br><br>' . $ex->getMessage():'')
                        ]
                   ];
        }
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

