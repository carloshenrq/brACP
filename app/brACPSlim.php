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
use Model\Donation;

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

    public function donationDisplay($donationStart = false, $donation = null, $checkoutCode = null)
    {
        // Cria a query para seleção da promoção no banco de dados.
        $query = $this->getEntityManager()
                        ->createQuery('SELECT p FROM Model\Promotion p WHERE :CURDATE BETWEEN p.startDate AND p.endDate');

        $query->setParameter('CURDATE', date('Y-m-d'));
        $result = $query->getResult();

        // Obtém o objeto da promoção e envia para o formulário.
        $promotion = ((count($result) > 0) ? $result[0]:null);

        // Exibe a tela com as informações de promoção e os demais dados
        //  caso necessário.
        $this->display('account.donations',
            [
                'promotion' => $promotion,
                'amountBonus' => DONATION_AMOUNT_MULTIPLY,
                'amountPromo' => ((is_null($promotion)) ? 0 : $promotion->getBonusMultiply()),
                'donationStart' => $donationStart,
                'checkoutCode' => $checkoutCode,
                'donation' => $donation
            ],
            1, null, null, !PAG_INSTALL);
    }

    /**
     * Recebe os dados de notificação do pagseguro para atribuir ao banco de dados.
     */
    public function pagSeguroRequest()
    {
        // Inicializa o objeto de doação.
        $donation = new Donation();

        // Multiplicador atual de bonus.
        $bonusMutiply = DONATION_AMOUNT_MULTIPLY;

        // Caso possua código de promoção
        if(!is_null($this->request()->post('PromotionID')))
        {
            // Realiza a query para obter os dados de promoção e caso existam
            //  se existir, define o objeto de promoção para a doação.
            $query = $this->getEntityManager()
                            ->createQuery('SELECT p FROM Model\Promotion p WHERE p.id = :id AND :CURDATE BETWEEN p.startDate AND p.endDate');
            $query->setParameter('id', $this->request()->post('PromotionID'))
                    ->setParameter('CURDATE', date('Y-m-d'));
            $result = $query->getResult();

            // Se houver dados de promoção para esta doação,
            //  define a promoção ativa.
            if(count($result) > 0)
            {
                $donation->setPromotion($result[0]);
                $bonusMutiply += $donation->getPromotion()->getBonusMultiply();
            }
        }

        // Define o código de referência interno para a transação.
        $donation->setReference(strtoupper(hash('md5', microtime(true))));
        $donation->setDrive('PAGSEGURO');
        $donation->setAccount_id($_SESSION['BRACP_ACCOUNTID']);
        $donation->setValue($this->request()->post('donation'));
        $donation->setBonus(floatval($donation->getValue()) * $bonusMutiply);
        $donation->setTotalValue(floatval($donation->getValue() + 0.4) / ((100 - 3.99)/100));
        $donation->setCheckoutCode(null);
        $donation->setTransactionCode(null);
        $donation->setReceiveBonus(!is_null($this->request()->post('nobonus')));

        // Grava o registro de doação do banco de dados.
        $this->getEntityManager()->persist($donation);
        $this->getEntityManager()->flush();

        // Faz a chamada da api do pagseguro para criar a transação de envio.
        $checkout = new PagSeguro\Checkout();
        $checkoutResponse = $checkout->setCurrency('BRL')
                                    ->addItem(new PagSeguro\CheckoutItem( 'BONUS_ELETRONICO',
                                             "Doação - Bônus Eletrônico ({$donation->getBonus()})",
                                             sprintf('%.2f', $donation->getTotalValue()),
                                             '1'))
                                    ->setReference($donation->getReference())
                                    ->addMetaKey('PLAYER_ID', $donation->getAccount_id())
                                    ->sendRequest();

        // Define o código de checkout para a doação.
        $donation->setCheckoutCode($checkoutResponse->code);

        $this->getEntityManager()->merge($donation);
        $this->getEntityManager()->flush();

        // Define os dados de checkout para a doação.
        $this->donationDisplay(true, $donation, $donation->getCheckoutCode());
    }

    /**
     * Remove os dados de sessão do navegador.
     */
    public function accountLoggout()
    {
        unset($_SESSION['BRACP_ISLOGGEDIN'], $_SESSION['BRACP_ACCOUNTID'], $_SESSION['BRACP_USERID']);
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
        $_SESSION['BRACP_ACCOUNTID'] = $acc->getAccount_id();
        $_SESSION['BRACP_USERID'] = $acc->getUserid();

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
    public function reloadLogin($account_id)
    {
        return $this->getEntityManager()->getRepository('Model\Login')->findOneBy(['account_id' => $account_id]);
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

            // Invoca a função enviada por parametro antes de invocar o template.
            if(!is_null($callable) && is_callable($callable))
                $callable();
        }

        // Atribui o nivel de gm e acesso.
        if($this->isLoggedIn() && !is_null($this->acc))
            $data = array_merge($data, ['acc_gmlevel' => $this->acc->getGroup_id()]);

        // Chama o view para mostrar o template.
        $this->view()->display($template . '.tpl', $data);
    }
}

