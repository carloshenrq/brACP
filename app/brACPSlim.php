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
        if($acc === false)
            return false;

        // Define como usuário logado e o objeto da conta em memória.
        $_SESSION['BRACP_ISLOGGEDIN'] = 1;
        $_SESSION['BRACP_USERID'] = $acc->getUserid();
        $_SESSION['BRACP_ACC_OBJECT'] = json_encode($acc);

        // Retorna verdadeiro para o login.
        return true;
    }

    /**
     * Chama o método para gerenciar o registro de contas.
     *
     * @return boolean
     */
    public function accountRegister()
    {
        // Inicializa o objeto para criação de conta.
        $acc = new Login;
        $acc->setUserid($this->request()->put('userid'));
        $acc->setUser_pass($this->request()->put('user_pass'));
        $acc->setSex($this->request()->put('sex'));
        $acc->setEmail($this->request()->put('email'));
        $acc->setBirthdate($this->request()->put('birthdate'));

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
        if($this->checkUserId($acc->getUserid()))
            return false;

        $this->getEntityManager()->persist($acc);
        $this->getEntityManager()->flush();

        // @TODO: Disparar eventos para envio de email.

        return true;
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

    /**
     * Verifica se o nome de usuário informado existe no banco de dados.
     *
     * @param string $userid
     *
     * @return bool
     */
    private function checkUserId($userid)
    {
        return count($this->getEntityManager()->getRepository('Model\Login')->findBy([
            'userid' => $userid
        ])) > 0;
    }

    /**
     * @param $em Doctrine\ORM\EntityManager
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
}

