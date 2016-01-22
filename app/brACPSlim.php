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
use Model\Compensate;

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
     * Método para gerenciamento dos personagens.
     */
    public function charsReset()
    {
        $this->display('account.chars', [], 1, null, function() {

            // Obtém a instância da aplicação.
            $app = brACPSlim::getInstance();

            // Inicializa as variaveis para marcar os personagens que foram resetados.
            $appear = $posit = $equip = [];

            // Se o reset de aparências estiver habilitado, então verifica os personagens
            //  para realizar o reset de aparência
            if(BRACP_ALLOW_RESET_APPEAR && !empty($app->request()->post('char_id_appear')))
            {
                // Cria a query de atualização para os personagens selecionados
                //  para resetar os dados.
                $query = $app->getEntityManager()
                                ->createQuery('
                                    UPDATE
                                        Model\Char c
                                    SET
                                        c.hair = 0, c.hair_color = 0,
                                        c.clothes_color = 0,
                                        c.head_top = 0, c.head_mid = 0,
                                        c.head_bottom = 0, c.robe = 0
                                    WHERE
                                        c.account_id = :account_id AND
                                        c.char_id = :char_id AND c.online = 0
                                ');

                // Varre todos os chars ids para resetar os dados.
                foreach($app->request()->post('char_id_appear') as $char_id)
                {
                    // Se conseguir resetar a aparência (se já não estiver resetado)
                    if($query->setParameter('char_id', $char_id)
                                ->setParameter('account_id', $app->acc->getAccount_id())
                                ->execute() > 0)
                        $appear[] = $char_id;
                }
            }

            // Verifica se pode resetar posição de personagem.
            if(BRACP_ALLOW_RESET_POSIT && !empty($app->request()->post('char_id_posit')))
            {
                // Cria a query de atualização para os personagens selecionados
                //  para resetar os dados.
                $query = $app->getEntityManager()
                                ->createQuery('
                                    UPDATE
                                        Model\Char c
                                    SET
                                        c.last_map = c.save_map,
                                        c.last_x = c.save_x,
                                        c.last_y = c.save_y
                                    WHERE
                                        c.account_id = :account_id AND
                                        c.char_id = :char_id AND
                                        c.online = 0
                                ');

                // Varre todos os chars ids para resetar os dados.
                foreach($app->request()->post('char_id_posit') as $char_id)
                {
                    // Se conseguir resetar a aparência (se já não estiver resetado)
                    if($query->setParameter('char_id', $char_id)
                                ->setParameter('account_id', $app->acc->getAccount_id())
                                ->execute() > 0)
                        $posit[] = $char_id;
                }
            }

            // Verifica se pode resetar posição de personagem.
            if(BRACP_ALLOW_RESET_EQUIP && !empty($app->request()->post('char_id_equip')))
            {
                // Varre os personagens selecionados
                //  para realizar o update.
                foreach($app->request()->post('char_id_equip') as $char_id)
                {
                    // Obtém o personagem.
                    $items = $app->getEntityManager()
                                     ->createQuery('
                                        SELECT
                                            i, c
                                        FROM
                                            Model\Inventory i
                                        LEFT JOIN
                                            i.character c
                                        WHERE
                                            c.account_id = :account_id and
                                            c.char_id = :char_id and
                                            i.equip = 1')
                                     ->setParameter('account_id', $app->acc->getAccount_id())
                                     ->setParameter('char_id', $char_id)
                                     ->getResult();

                    // Desaquipa todos os itens do jogador.
                    if(count($items) > 0)
                    {
                        // Varre os itens do inventário do jogador.
                        foreach($items as $item)
                        {
                            $item->setEquip(false);
                            $app->getEntityManager()->merge($item);
                        }

                        $app->getEntityManager()->flush();
                        $equip[] = $char_id;
                    }
                }
            }

            $chars = $app->getEntityManager()
                            ->getRepository('Model\Char')
                            ->findBy([
                                'account_id' => $app->acc->getAccount_id()
                            ]);

            return ['chars' => $chars,
                    'appear' => $appear,
                    'posit' => $posit,
                    'equip' => $equip,
                    'resetCount' => (BRACP_ALLOW_RESET_APPEAR + BRACP_ALLOW_RESET_POSIT + BRACP_ALLOW_RESET_EQUIP)];
        });
    }

    public function donationDisplay($donationStart = false, $donation = null, $checkoutCode = null)
    {
        // Verifica se foi enviado algum código de transação para ser atualizado pelo endereço
        //  do pagseguro.
        if(!is_null($this->request()->get('transactionCode')))
            $this->updateTransaction($this->request()->get('transactionCode'));

        // Cria a query para seleção da promoção no banco de dados.
        $query = $this->getEntityManager()
                        ->createQuery('SELECT p FROM Model\Promotion p WHERE :CURDATE BETWEEN p.startDate AND p.endDate');

        $query->setParameter('CURDATE', date('Y-m-d'));
        $result = $query->getResult();

        // Obtém o objeto da promoção e envia para o formulário.
        $promotion = ((count($result) > 0) ? $result[0]:null);

        // Obtém as 30 ultimas doações nos ultimos 60 dias para o usuário logado.
        $donations = $this->getEntityManager()
                        ->createQuery('SELECT d FROM Model\Donation d WHERE d.date > :PAST_DATE ORDER BY d.id DESC')
                        ->setParameter('PAST_DATE', date('Y-m-d',time() - (60*60*24*60)))
                        ->setMaxResults(30)
                        ->getResult();

        $promos = [];

        // Obtém todas as promoções que irão iniciar nos próximos dias.
        if(DONATION_SHOW_NEXT_PROMO && DONATION_INTERVAL_DAYS > 0)
            $promos = $this->getEntityManager()
                            ->createQuery('SELECT p FROM Model\Promotion p WHERE p.startDate > :TODAY_DATE AND p.startDate <= :NEXT_DATE ORDER BY p.startDate ASC')
                            ->setParameter('TODAY_DATE', date('Y-m-d', time()))
                            ->setParameter('NEXT_DATE', date('Y-m-d', time() + (60*60*24*DONATION_INTERVAL_DAYS)))
                            ->setMaxResults(10)
                            ->getResult();

        // Exibe a tela com as informações de promoção e os demais dados
        //  caso necessário.
        $this->display('account.donations',
            [
                'promotion' => $promotion,
                'amountBonus' => DONATION_AMOUNT_MULTIPLY,
                'amountPromo' => ((is_null($promotion)) ? 0 : $promotion->getBonusMultiply()),
                'donationStart' => $donationStart,
                'checkoutCode' => $checkoutCode,
                'donation' => $donation,
                'donations' => $donations,
                'promos' => $promos
            ],
            1, null, null, !PAG_INSTALL);
    }

    /**
     * Atualiza os dados da transação e faz todas as operações no banco de dados.
     *
     * @param string $transactionCode
     */
    public function updateTransaction($transactionCode)
    {
        // Retorna os dados de transação para o pagseguro para
        //  verificação e possivel compensação da doação.
        $transaction = PagSeguro\Transaction::checkTransaction($transactionCode);

        // Obtém a doação com o código de referência para a transação.
        $donation = $this->getEntityManager()
                            ->createQuery('SELECT d, p FROM Model\Donation d LEFT JOIN d.promotion p WHERE d.reference = :reference')
                            ->setParameter('reference', $transaction->reference)
                            ->getOneOrNullResult();

        // Estado antigo da transação. (O que ainda está salvo no banco de dados)
        $oldStatus = $donation->getStatus();
        $newStatus = (($transaction->status == 3 || $transaction->status == 4) ? 'PAGO' :
                        (($transaction->status == 1 || $transaction->status == 2) ? 'INICIADA' :
                        (($transaction->status == 7) ? 'CANCELADO' : 'ESTORNADO')));

        // Obtém a data do ultimo evento executado.
        $dateTime = date_create_from_format('Y-m-d\TH:i:s.uP', $transaction->lastEventDate)->format('Y-m-d H:i:s');

        // Define o novo status da doação.
        $donation->setStatus($newStatus);

        // Se houve pilantragem.
        if($oldStatus == 'PAGO' && $newStatus != 'PAGO')
        {
            // Estornou ou cancelou a doação, bloqueia a conta, pois a doação já foi
            //  creditada.
            $account = $this->getEntityManager()->getRepository('Model\Login')->findOneBy([
                'account_id' => $donation->getAccount_id()
            ]);

            // Bloqueia a conta do jogaodr pois foi retornado o pagamento.
            $account->setState(5);

            // Atualiza os dados do jogador bloqueando a conta.
            $this->getEntityManager()->merge($account);
            $this->getEntityManager()->flush();
        }
        else if($oldStatus == 'INICIADA' && $newStatus == 'PAGO')
        {
            // Se for para doação receber o bônus doado, então
            // Cria um registro na tabela de compensação.
            if($donation->getReceiveBonus())
            {
                // Foi pago o valor e pode creditar ao jogador.
                $compensate = new Compensate();
                $compensate->setDonation($donation);
                $compensate->setDate(date('Y-m-d'));
                $compensate->setPending(true);
                $compensate->setDate(null);

                // Grava no banco de dados que foi iniciado a compensação dos dados.
                $this->getEntityManager()->persist($compensate);
            }

            // Define como compensado.
            $donation->setCompensate(true);
            $donation->setPaymentDate($dateTime);
        }

        // Atualiza os dados da doação.
        $this->getEntityManager()->merge($donation);
        $this->getEntityManager()->flush();
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
        $donation->setDate(date('Y-m-d'));
        $donation->setReference(strtoupper(hash('md5', microtime(true))));
        $donation->setDrive('PAGSEGURO');
        $donation->setAccount_id($_SESSION['BRACP_ACCOUNTID']);
        $donation->setValue($this->request()->post('donation'));
        $donation->setBonus(floatval($donation->getValue()) * $bonusMutiply);

        // Se o doador for pagar a taxa, então realiza o calculo do valor final para
        //  gravar no banco de dados.
        if(DONATION_AMOUNT_USE_RATE)
            $donation->setTotalValue(floatval($donation->getValue() + 0.4) / ((100 - 3.99)/100));
        else
            $donation->setTotalValue($donation->getValue());

        $donation->setCheckoutCode(null);
        $donation->setTransactionCode(null);
        $donation->setReceiveBonus(is_null($this->request()->post('nobonus')));

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
     * Realiza a alteração de senha do usuário.
     */
    public function accountChangeMail()
    {
        // Não permite fazer a requisição caso esteja bloqueado para administradores
        //  alterarem o email.
        if(!BRACP_ALLOW_CHANGE_MAIL || $this->acc->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return -2;

        // Obtém o email digitado.
        $email = hash('md5', $this->request()->post('email'));
        $actual = hash('md5', $this->acc->getEmail());

        // Obtém os novos emails digitados digitadas e realiza a verificação de senha.
        $new_email = hash('md5', $this->request()->post('email_new'));
        $con_email = hash('md5', $this->request()->post('email_conf'));

        // Email atual não confere com o digitado.
        if($email !== $actual)
            return -1;

        // Senhas novas não conferem como digitado.
        if($new_email !== $con_email)
            return 0;

        // Obtém o email novo para continuar a alteração.
        $email_new = $this->request()->post('email_new');

        // Verifica se o e-mail já não está cadastrado no banco de dados.
        if(BRACP_MAIL_REGISTER_ONCE && $this->checkEmail($email_new))
            return -2;

        // Define a senha do usuário.
        $this->acc->setEmail($email_new);

        // Atualiza os dados no banco.
        $this->getEntityManager()->merge($this->acc);
        $this->getEntityManager()->flush();

        // Retorna sucesso para a execução.
        return 1;
    }


    /**
     * Realiza a alteração de senha do usuário.
     */
    public function accountChangePassword()
    {
        // Não permite fazer a requisição caso esteja bloqueado para administradores
        //  alterarem a senha.
        if(!BRACP_ALLOW_ADMIN_CHANGE_PASSWORD && $this->acc->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return -2;

        // Obtém a senha digitada.
        $pass = hash('md5', $this->request()->post('user_pass'));
        $actual = $this->acc->getUser_pass();

        // Se não estiver ligado o hash, aplica apenas para comparar a senha.
        if(!BRACP_MD5_PASSWORD_HASH)
            $actual = hash('md5', $actual);

        // Obtém as senhas digitadas e realiza a verificação de senha.
        $new_pass = hash('md5', $this->request()->post('user_pass_new'));
        $con_pass = hash('md5', $this->request()->post('user_pass_conf'));

        // Senha atual não confere com o digitado.
        if($pass !== $actual)
            return -1;

        // Senhas novas não conferem como digitado.
        if($new_pass !== $con_pass)
            return 0;

        // Define a nova senha para a conta.
        $user_pass = ((BRACP_MD5_PASSWORD_HASH) ? $new_pass : $this->request()->post('user_pass_new'));

        // Define a senha do usuário.
        $this->acc->setUser_pass($user_pass);

        // Atualiza os dados no banco.
        $this->getEntityManager()->merge($this->acc);
        $this->getEntityManager()->flush();

        // Retorna sucesso para a execução.
        return 1;
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

