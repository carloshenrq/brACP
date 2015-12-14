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

use Doctrine\ORM\EntityManager;
use Model\Login;

/**
 *
 */
class brACPSlim extends Slim\Slim
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public $acc;

    public function __construct($userSettings = [])
    {
        // Initialize session for this app.
        session_cache_limiter(false);
        session_start();

        // Loads the default settings for this app.
        parent::__construct($userSettings);

        // Add the template folder to smarty.
        $this->view()->setTemplatesDirectory(BRACP_TEMPLATE_DIR);

        // Add the new middleware to run.
        $this->add(new \Slim\Middleware\ContentTypes());
        $this->add(new \brAMiddlewareDoctrine());
        $this->add(new \brAMiddlewareRoutes());
    }

    /**
     * Remove os dados de sessão do navegador.
     */
    public function accountLoggout()
    {
        unset($_SESSION['BRACP_ISLOGGEDIN'], $_SESSION['BRACP_USERID'], $_SESSION['BRACP_ACC_OBJECT']);
    }

    /**
     * Verifica os dados para realizar login.
     */
    public function accountLogin()
    {
        // Verifica se a conta é existente.
        $acc = $this->checkUserAndPass($this->request()->post('userid'), $this->request()->post('user_pass'));

        // Se a combinação não existir retorna false.
        // Adicionado para caso a conta seja inferior ao nivel permitido para login, não
        //  permite que seja logado.
        if($acc === false)
            return 0;

        if($acc->getGroup_id() < BRACP_ALLOW_LOGIN_GMLEVEL)
            return -1;

        // Define como usuário logado e o objeto da conta em memória.
        $_SESSION['BRACP_ISLOGGEDIN'] = 1;
        $_SESSION['BRACP_USERID'] = $acc->getUserid();
        $_SESSION['BRACP_ACC_OBJECT'] = json_encode($acc);

        // Retorna verdadeiro para o login.
        return 1;
    }

    /**
     * Chama o método para gerenciar o registro de contas.
     *
     * @return boolean
     */
    public function accountRegister()
    {
        // Obtém o hash das senhas para comparar.
        $user_pass = hash('md5', $this->request()->post('user_pass'));
        $user_pass_conf = hash('md5', $this->request()->post('user_pass_conf'));
        // Obtém o hash dos emails para comparar.
        $email = hash('md5', $this->request()->post('email'));
        $email_conf = hash('md5', $this->request()->post('email_conf'));

        // Verificações para nem permitir o resto da execução do programa.
        if($user_pass !== $user_pass_conf)
            return -1;
        else if($email !== $email_conf)
            return -2;

        // Inicializa o objeto para criação de conta.
        $acc = new Login;
        $acc->setUserid($this->request()->post('userid'));
        $acc->setUser_pass($this->request()->post('user_pass'));
        $acc->setSex($this->request()->post('sex'));
        $acc->setEmail($this->request()->post('email'));
        $acc->setBirthdate($this->request()->post('birthdate'));

        // Se estiver configurado para realizar a aplicação do md5 na senha
        //  então aplica o hash('md5', $acc->getUser_pass())
        if(BRACP_MD5_PASSWORD_HASH)
            $acc->setUser_pass(hash('md5', $acc->getUser_pass()));

        // Tenta criar a conta e retorna o resultado.
        return $this->createAccount($acc);
    }

    /**
     * Cria a conta no banco de dados.
     *
     * @param Login $acc
     *
     * @return boolean
     */
    private function createAccount(Login $acc)
    {
        try
        {
            // Verifica se o nome de usuario está disponivel para uso.
            //  - Caso e-mail esteja configurado para apenas um uso, faz o mesmo.
            if($this->checkUserId($acc->getUserid()) || BRACP_MAIL_REGISTER_ONCE && $this->checkEmail($acc->getEmail()))
                return 0;

            // Grava o objeto no banco de dados.
            $this->getEntityManager()->persist($acc);
            $this->getEntityManager()->flush();

            // Se permitir o envio de e-mail, envia o e-mail para o usuário com as configurações
            //  necessárias para uma possivel ativação da conta.
            if(BRACP_ALLOW_MAIL_SEND)
            {
                // @TODO: Disparar eventos para envio de email.
            }

            // Retorna que foi possivel criar a conta.
            return 1;
        }
        catch(Exception $ex)
        {
            // Em caso de erro, envia erro default.
            return 0;
        }
    }

    /**
     * Verifica a existência do usuário no banco de dados para realizar login.
     *
     * @param string $userid
     * @param string $user_pass
     *
     * @return mixed
     */
    private function checkUserAndPass($userid, $user_pass)
    {
        try
        {
            // Verifica os usuários cadastrados com o usuário e senha
            $users = $this->getEntityManager()->getRepository('Model\Login')->findBy([
                'userid' => $userid,
                'user_pass' => ((BRACP_MD5_PASSWORD_HASH) ? hash('md5', $user_pass):$user_pass),
                'state' => 0
            ]);

            // Se não existir usuários, retorna false.
            if(!count($users))
                return false;

            // Retorna o primeiro usuário.
            return $users[0];
        }
        catch(Exception $ex)
        {
            return false;
        }
    }

    /**
     * Verifica se o endereço de e-mail já está cadastrado no banco de dados.
     *
     * @param string $email
     *
     * @return bool
     */
    private function checkEmail($email)
    {
        try
        {
            return count($this->getEntityManager()->getRepository('Model\Login')->findBy([
                'email' => $email
            ])) > 0;
        }
        catch(Exception $ex)
        {
            return false;
        }
    }

    /**
     * Verifica se o nome de usuário informado existe no banco de dados.
     *
     * @param string $userid
     *
     * @return bool
     */
    private function checkUserId($userid)
    {
        try
        {
            return count($this->getEntityManager()->getRepository('Model\Login')->findBy([
                'userid' => $userid
            ])) > 0;
        }
        catch(Exception $ex)
        {
            return true;
        }
    }

    /**
     * @param Model\Login
     */
    public function reloadLogin($userid)
    {
        return $this->getEntityManager()->getRepository('Model\Login')->findOneBy(['userid' => $userid]);
    }

    /**
     * @param Doctrine\ORM\EntityManager $em 
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @return boolean
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['BRACP_ISLOGGEDIN']) && $_SESSION['BRACP_ISLOGGEDIN'] == true;
    }

    /**
     * @param string $template
     * @param array $data
     * @param int $access { 0: never logged. 1: ever logged. -1: always. }
     */
    public function display($template, $data = [], $access = -1, $callable = null, $callableData = null, $blocked = false, $gmlevel = -1)
    {
        // Controle para saber se o acesso está tudo ok.
        $accessIsFine = true;

        // Verifica o tipo de acesso para mostrar o display do form.
        if($access != -1 || $blocked || BRACP_MAINTENCE)
        {
            if($blocked || BRACP_MAINTENCE || ($access == 1 && $this->isLoggedIn()
                                                 && ($this->acc->getState() != 0 || $this->acc->getGroup_id() < $gmlevel)))
            {
                $template = 'error.not.allowed';
                $accessIsFine = false;
            }
            else if($access == 0 && $this->isLoggedIn())
            {
                $template = 'account.error.logged';
                $accessIsFine = false;
            }
            else if($access == 1 && !$this->isLoggedIn())
            {
                $template = 'account.error.login';
                $accessIsFine = false;
            }
        }

        // Verifica se o tipo de requisição é ajax, se for, retorna o template
        //  para o ajax.
        if($this->request()->isAjax())
            $template .= '.ajax';

        // Caso o acesso ao form possa ser executado sem necessidade de chamar os callbacks,
        //  quando ocorre erro.
        if($accessIsFine)
        {
            // Invoca a função enviada por parametro antes de invocar o template.
            if(!is_null($callableData) && is_callable($callableData))
                $data = array_merge($data, $callableData());

            // Atribui o nivel de gm e acesso.
            if($this->isLoggedIn() && !is_null($this->acc))
                $data = array_merge($data, ['acc_gmlevel' => $this->acc->getGroup_id()]);

            // Invoca a função enviada por parametro antes de invocar o template.
            if(!is_null($callable) && is_callable($callable))
                $callable();
        }

        // Chama o view para mostrar o template.
        $this->view()->display($template . '.tpl', $data);
    }
}

