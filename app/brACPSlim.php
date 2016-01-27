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
use Model\Recover;
use Model\EmailLog;

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
     * Gera uma senha aleatória.
     */
    public function randomString($length = BRACP_RECOVER_STRING_LENGTH, $string = BRACP_RECOVER_RANDOM_STRING)
    {
        $str = '';

        while(strlen($str) < $length)
            $str .= $string[rand(0, strlen($string))];

        return $str;
    }

    /**
     * Salva as novas configurações do painel de controle.
     */
    public function adminSaveConfig($config)
    {
        // Caminho para o arquivo de configuração do sistema.
        $configPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

        // Verifica se o arquivo de configuração existe no diretório raiz do sistema.
        if(is_file($configPath) && file_exists($configPath))
            rename($configPath, $configPath . '.bkp');

        // Abre o ponteiro de escrita para o arquivo.
        $fp = fopen($configPath, 'w');

        fwrite($fp, "<?php\n");
        fwrite($fp, "/**\n");
        fwrite($fp, " * Arquivo de configuração gerado às ".date('d/m/Y H:i:s').".\n");
        fwrite($fp, " */\n");
        fwrite($fp, "\n");

        // Cria o arquivo de configuração e escreve os dados no mesmo.
        foreach($config as $key => $value)
        {
            fwrite($fp, "DEFINE('{$key}', '" . addslashes($value) . "', false);\n");
        }

        fwrite($fp, "\n");
        fwrite($fp, "if(BRACP_DEVELOP_MODE)\n");
        fwrite($fp, "{\n");
        fwrite($fp, "\tDEFINE('PAG_URL', 'https://sandbox.pagseguro.uol.com.br', false);\n");
        fwrite($fp, "\tDEFINE('PAG_WS_URL', 'https://ws.sandbox.pagseguro.uol.com.br', false);\n");
        fwrite($fp, "\tDEFINE('PAG_STC_URL', 'https://stc.sandbox.pagseguro.uol.com.br', false);\n");
        fwrite($fp, "}\n");
        fwrite($fp, "else\n");
        fwrite($fp, "{\n");
        fwrite($fp, "\tDEFINE('PAG_URL', 'https://pagseguro.uol.com.br', false);\n");
        fwrite($fp, "\tDEFINE('PAG_WS_URL', 'https://ws.pagseguro.uol.com.br', false);\n");
        fwrite($fp, "\tDEFINE('PAG_STC_URL', 'https://stc.pagseguro.uol.com.br', false);\n");
        fwrite($fp, "}\n");

        // Libera o ponteiro de escrita para o arquivo.
        fclose($fp);

        return ['message' => ['success' => 'Configurações salvas com sucesso.']];
    }

    /**
     * Método para obter todas as doações e realizar a somatória dos dados.
     */
    public function adminDonations()
    {
        return [];
    }

    /**
     * Método utilizado para recuperar a conta dos usuários.
     */
    public function recoverAccount($code = null)
    {
        // Exibe o layout na tela.
        $this->display('account.recover', [], 0, null, function() use ($code) {
            // Instância da aplicação
            $app = brACPSlim::getInstance();

            // Dados a serem retornados na tela do usuário.
            $data = [];

            // Verifica se possui código enviado e se este tipo de requisição pode ser realizada.
            if(!is_null($code) && (BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE))
            {
                // Obtém os códigos de recuperação de acordo com as datas de recuperação
                //  e expirar.
                $recover = $app->getEntityManager()
                                ->createQuery('
                                    SELECT
                                        r, l
                                    FROM
                                        Model\Recover r
                                    INNER JOIN
                                        r.account l
                                    WHERE
                                        r.code = :code AND
                                        r.used = false AND
                                        :CURDATETIME BETWEEN r.date and r.expire
                                ')
                                ->setParameter('code', $code)
                                ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                                ->getOneOrNullResult();

                // Se o código de recuperação de contas foi encontrado.
                // Realiza as alterações de senha da conta.
                if(!is_null($recover))
                {
                    // Atualiza o código de recuperação para utilizado.
                    $recover->setUsed(true);

                    $app->getEntityManager()->merge($recover);
                    $app->getEntityManager()->flush();

                    // Obtém a nova senha a ser utilizada.
                    $password = $app->randomString();

                    // Atualiza a nova senha de usuário.
                    $app->changePassword($recover->getAccount()->getAccount_id(), $password);

                    // Envia o e-mail para o usuario com sua nova senha.
                    $app->sendMail('Senha Recuperada',
                            [$recover->getAccount()->getEmail()],
                            'mail.recover',
                            [
                                'userid' => $recover->getAccount()->getUserid(),
                                'password' => $password,
                                'ipAddress' => $app->request()->getIp(),
                            ]);

                    // Mensagem informativa para a senha alterada.
                    $data = ['message' => ['success' => 'Sua senha foi alterada com sucesso! Verifique seu endereço de e-mail contendo sua nova senha.']];
                }
                else
                {
                    // Define a mensagem de erro informando que o código de recuperação não foi encontrado.
                    $data = ['message' => ['error' => 'O Código de recuperação não foi encontrado ou já foi utilizado.']];
                }
            }
            // Caso o nome de usuário e endereço de e-mail estejam preenchidos, esta foi uma requisição
            //  para recuperar os dados da conta.
            else if(!empty($app->request()->post('userid')) && !empty($app->request()->post('email')))
            {
                // Tenta obter a conta para realizar a requisição
                //  de recuperação de conta.
                $account =  $app->getEntityManager()
                                ->getRepository('Model\Login')
                                ->findOneBy([
                                    'userid' => $app->request()->post('userid'),
                                    'email' => $app->request()->post('email')
                                ]);

                // Se encontrou a conta para retornar o email com os dados, então:
                if(!is_null($account))
                {
                    // Se o painel está usando hash md5 ou está configurado
                    //  para retornar recuperação por código, então irá criar
                    //  registro de recuperação e quando o usuário clicar no e-mail
                    //  será enviado para o lugar de senha resetada.
                    if(BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE)
                    {
                        // Verifica se existe algum código de de recuperação para
                        //  o usuário que está fazendo a requisição.
                        $recover = $this->getEntityManager()
                                        ->createQuery('
                                            SELECT
                                                r, l
                                            FROM
                                                Model\Recover r
                                            INNER JOIN
                                                r.account l
                                            WHERE
                                                l.account_id = :account_id AND
                                                r.used = false AND
                                                :CURDATETIME BETWEEN r.date and r.expire
                                        ')
                                        ->setParameter('account_id', $account->getAccount_id())
                                        ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                                        ->getOneOrNullResult();

                        // Se o registro de recuperação não for encontrado,
                        // Então, cria um novo registro, se não, utiliza o mesmo criado anteriormente.
                        if(is_null($recover))
                        {
                            // Cria o objeto de recuperação.
                            $recover = new Recover();
                            $recover->setAccount($account);
                            $recover->setCode(hash('md5', microtime(true)));
                            $recover->setDate(date('Y-m-d H:i:s'));
                            $recover->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
                            $recover->setUsed(false);

                            // Grava o código no banco de dados e envia o email.
                            $this->getEntityManager()->persist($recover);
                            $this->getEntityManager()->flush();
                        }

                        // Envia o e-mail para o usuário informando que a senha foi modificada.
                        $app->sendMail('Recuperação de Usuário',
                                [$account->getEmail()],
                                'mail.recover.code',
                                [
                                    'userid' => $account->getUserid(),
                                    'code' => $recover->getCode(),
                                    'date' => $recover->getDate(),
                                    'expire' => $recover->getExpire(),
                                    'ipAddress' => $app->request()->getIp(),
                                    'href' => BRACP_URL . BRACP_DIR_INSTALL_URL . 'account/recover'
                                ]);

                        // Define o status de mensagem que o email contendo dados
                        //  de recuperação da conta foi enviado com sucesso.
                        $data = ['message' => ['success' => 'Um e-mail contendo os dados de recuperação foi enviado ao seu e-mail.']];
                    }
                    else
                    {
                        // Envia o e-mail para o usuario com sua nova senha.
                        $app->sendMail('Notificação: Senha Recuperada',
                                [$account->getEmail()],
                                'mail.recover',
                                [
                                    'userid' => $account->getUserid(),
                                    'password' => $account->getUser_pass(),
                                    'ipAddress' => $app->request()->getIp(),
                                ]);

                        // Define o status de mensagem que o email contendo dados
                        //  de recuperação da conta foi enviado com sucesso.
                        $data = ['message' => ['success' => 'Verifique seu endereço de e-mail contendo sua senha.']];
                    }
                }
                else
                {
                    // Define o status de mensagem que o email contendo dados
                    //  de recuperação da conta foi enviado com sucesso.
                    $data = ['message' => ['error' => 'Combinação de usuário e e-mail não encontrados.']];
                }
            }

            return $data;
        }, !BRACP_ALLOW_RECOVER);
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
                // Varre todos os chars ids para resetar os dados.
                foreach($app->request()->post('char_id_appear') as $char_id)
                {
                    if($app->resetAppear($app->acc->getAccount_id(), $char_id))
                        $appear[] = $char_id;
                }
            }

            // Verifica se pode resetar posição de personagem.
            if(BRACP_ALLOW_RESET_POSIT && !empty($app->request()->post('char_id_posit')))
            {
                // Varre todos os chars ids para resetar os dados.
                foreach($app->request()->post('char_id_posit') as $char_id)
                {
                    // Se conseguir resetar a aparência (se já não estiver resetado)
                    if($app->resetPosition($app->acc->getAccount_id(), $char_id))
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
                    if($app->resetEquip($app->acc->getAccount_id(), $char_id))
                        $equip[] = $char_id;
                }
            }

            return ['chars' => $app->findChars($app->acc->getAccount_id()),
                    'appear' => $appear,
                    'posit' => $posit,
                    'equip' => $equip,
                    'resetCount' => (BRACP_ALLOW_RESET_APPEAR + BRACP_ALLOW_RESET_POSIT + BRACP_ALLOW_RESET_EQUIP)];
        });
    }

    /**
     * Exibe informações de doação.
     */
    public function donationDisplay($donationStart = false, $donation = null, $checkoutCode = null)
    {
        // Verifica se foi enviado algum código de transação para ser atualizado pelo endereço
        //  do pagseguro.
        if(!is_null($this->request()->get('transactionCode')))
            $this->updateTransaction($this->request()->get('transactionCode'));

        // Obtém o objeto da promoção e envia para o formulário.
        $promotion = $this->getEntityManager()
                            ->createQuery('
                                SELECT
                                    p
                                FROM
                                    Model\Promotion p
                                WHERE
                                    :CURDATE BETWEEN p.startDate AND p.endDate
                            ')
                            ->setParameter('CURDATE', date('Y-m-d'))
                            ->getOneOrNullResult();

        // Obtém as 30 ultimas doações nos ultimos 60 dias para o usuário logado.
        $donations = $this->getEntityManager()
                        ->createQuery('
                            SELECT
                                d, l
                            FROM
                                Model\Donation d
                            INNER JOIN
                                d.account l
                            WHERE
                                l.account_id = :account_id AND
                                d.date > :PAST_DATE
                            ORDER BY
                                d.id DESC
                        ')
                        ->setParameter('account_id', $this->acc->getAccount_id())
                        ->setParameter('PAST_DATE', date('Y-m-d',time() - (60*60*24*60)))
                        ->setMaxResults(30)
                        ->getResult();

        // Inicializa o vetor para as promoções.
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
                            ->createQuery('
                                SELECT
                                    d, p, l
                                FROM
                                    Model\Donation d
                                INNER JOIN
                                    d.account l
                                LEFT JOIN
                                    d.promotion p
                                WHERE
                                    d.reference = :reference')
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
            // Bloqueia a conta do jogaodr pois foi retornado o pagamento.
            $this->changeState($donation->getAccount()->getAccount_id(), 5);
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
            $result = $this->getEntityManager()
                            ->createQuery('SELECT p FROM Model\Promotion p WHERE p.id = :id AND :CURDATE BETWEEN p.startDate AND p.endDate')
                            ->setParameter('id', $this->request()->post('PromotionID'))
                            ->setParameter('CURDATE', date('Y-m-d'))
                            ->getOneOrNullResult();

            // Se houver dados de promoção para esta doação,
            //  define a promoção ativa.
            if(!is_null($result))
            {
                $donation->setPromotion($result);
                $bonusMutiply += $donation->getPromotion()->getBonusMultiply();
            }
        }

        // Define o código de referência interno para a transação.
        $donation->setDate(date('Y-m-d'));
        $donation->setReference(strtoupper(hash('md5', microtime(true))));
        $donation->setDrive('PAGSEGURO');
        $donation->setAccount($this->acc);
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
                                    ->addMetaKey('PLAYER_ID', $donation->getAccount()->getAccount_id())
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

        // Verifica se não foi possivel alterar o e-mail
        if(!$this->changeMail($this->acc->getAccount_id(), $email_new))
            return -2;

        // Atualiza a entidade.
        $this->getEntityManager()->refresh($this->acc);

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

        // Atualiza a senha do usuário.
        if(!$this->changePassword($this->acc->getAccount_id(), $this->request()->post('user_pass_new')))
            return -2;

        // Atualiza o objeto da conta.
        $this->getEntityManager()->refresh($this->acc);

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

        // Se estiver configurado para realizar a aplicação do md5 na senha
        //  então aplica o hash('md5', $acc->getUser_pass())
        if(BRACP_MD5_PASSWORD_HASH)
            $acc->setUser_pass(hash('md5', $acc->getUser_pass()));

        // Tenta criar a conta e retorna o resultado.
        return $this->createAccount($acc);
    }

    /**
     * Envia um e-mail para os destinários.
     *
     * @param string $subject
     * @param array $to
     * @param string $template
     * @param array $data
     */
    public function sendMail($subject, $to, $template, $data = [])
    {
        // Adicionado teste para envio de e-mail. Se não for permitido em configurador
        //  o envio dos emails então retorna false.
        if(!BRACP_ALLOW_MAIL_SEND)
            return false;

        // Transporte para o email.
        $transport = \Swift_SmtpTransport::newInstance(BRACP_MAIL_HOST, BRACP_MAIL_PORT)
                                            ->setUsername(BRACP_MAIL_USER)
                                            ->setPassword(BRACP_MAIL_PASS);
        // Mailer para envio dos dados.
        $mailer = \Swift_Mailer::newInstance($transport);

        // Mensagem para enviar.
        $message = \Swift_Message::newInstance($subject)
                                    ->setFrom([BRACP_MAIL_FROM => BRACP_MAIL_FROM_NAME])
                                    ->setTo($to)
                                    ->setBody($this->renderMail($template, $data), 'text/html');

        // Envia a mensagem.
        return $mailer->send($message) > 0;
    }

    /**
     * Altera o estado da conta.
     *
     * @param int $account_id
     * @param int $state
     */
    public function changeState($account_id, $state)
    {
        // Atualiza a senha do jogador.
        $stateChange = $this->getEntityManager()
                                ->createQuery('
                                    UPDATE
                                        Model\Login l
                                    SET
                                        l.state = :state
                                    WHERE
                                        l.account_id = :account_id
                                ')
                                ->setParameter('account_id', $account_id)
                                ->setParameter('state', $state)
                                ->execute() > 0;

        return $stateChange;
    }

    /**
     * Altera a senha de usuário para a senha indicada.
     *
     * @param int $account_id
     * @param string $password
     */
    public function changePassword($account_id, $password)
    {
        // Se estiver configurado para usar md5.
        if(BRACP_MD5_PASSWORD_HASH)
            $password = hash('md5', $password);

        // Atualiza a senha do jogador.
        $passwordChange = $this->getEntityManager()
                                ->createQuery('
                                    UPDATE
                                        Model\Login l
                                    SET
                                        l.user_pass = :user_pass
                                    WHERE
                                        l.account_id = :account_id
                                ')
                                ->setParameter('account_id', $account_id)
                                ->setParameter('user_pass', $password)
                                ->execute() > 0;

        // Senha alterada informa para o usuário que foi modificada.
        if(BRACP_ALLOW_MAIL_SEND && BRACP_NOTIFY_CHANGE_PASSWORD)
        {
            // Obtém a conta que será notificada.
            $acc = $this->getEntityManager()
                        ->getRepository('Model\Login')
                        ->findOneBy(['account_id' => $account_id]);

            // Envia o e-mail para o usuário informando que a senha foi modificada.
            $this->sendMail('Notificação: Alteração de Senha',
                    [$acc->getEmail()],
                    'mail.change.password',
                    [
                        'userid' => $acc->getUserid(),
                        'ipAddress' => $this->request()->getIp()
                    ]);
        }

        return $passwordChange;
    }

    /**
     * Altera o endereço de e-mail da conta indicada.
     *
     * @param int $account_id
     * @param string $email
     */
    public function changeMail($account_id, $email)
    {
        // Verifica se é apenas os endereços de emails não podem se repetir.
        //  Se não puder, retorna falso.
        if(BRACP_MAIL_REGISTER_ONCE && $this->checkEmail($email))
            return false;

        // Obtém a conta que será enviado o email.
        $acc = $this->getEntityManager()
                    ->getRepository('Model\Login')
                    ->findOneBy(['account_id' => $account_id]);

        // Obtém o email antigo.
        $oldMail = $acc->getEmail();

        // Realiza a alteração de e-mail na conta indicada.
        $mailChange =  $this->getEntityManager()
                            ->createQuery('
                                UPDATE
                                    Model\Login l
                                SET
                                    l.email = :email
                                WHERE
                                    l.account_id = :account_id
                            ')
                            ->setParameter('account_id', $account_id)
                            ->setParameter('email', $email)
                            ->execute() > 0;

        // Cria um log de alterações para o endereço de e-mail do usuário.
        $log = new EmailLog();
        $log->setAccount($acc);
        $log->setFrom($oldMail);
        $log->setTo($email);
        $log->setDate(date('Y-m-d H:i:s'));

        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush();

        // Notifica o usuário da alteração de email.
        if(BRACP_ALLOW_MAIL_SEND && BRACP_NOTIFY_CHANGE_MAIL)
        {
            // Envia um email para o endereço antigo informando a alteração.
            $this->sendMail('Notificação: Alteração de E-mail',
                    [$log->getFrom()],
                    'mail.change.mail',
                    [
                        'userid' => $acc->getUserid(),
                        'mailOld' => $log->getFrom(),
                        'mailNew' => $log->getTo(),
                        'ipAddress' => $this->request()->getIp()
                    ]);

            // Envia o e-mail para o novo endereço.
            $this->sendMail('Notificação: Alteração de E-mail',
                    [$log->getTo()],
                    'mail.change.mail',
                    [
                        'userid' => $acc->getUserid(),
                        'mailOld' => $log->getFrom(),
                        'mailNew' => $log->getTo(),
                        'ipAddress' => $this->request()->getIp()
                    ]);
        }

        return $mailChange;
    }

    /**
     * Encontra todos os personagens da conta indicada.
     *
     * @param int $account_id
     */
    public function findChars($account_id)
    {
        return $this->getEntityManager()
                    ->getRepository('Model\Char')
                    ->findBy(['account_id' => $account_id]);
    }

    /**
     * Reseta a aparência do personagem.
     *
     * @param int $account_id
     * @param int $char_id
     */
    public function resetAppear($account_id, $char_id)
    {
        // Se a configuração não permitir este reset retorna sem executar o resto do código.
        if(!BRACP_ALLOW_RESET_POSIT)
            return false;

        // Executa a query no banco de dados e retorna true se foi resetado.
        return $this->getEntityManager()
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
                    ')
                    ->setParameter('account_id', $account_id)
                    ->setParameter('char_id', $char_id)
                    ->execute() > 0;
    }

    /**
     * Reseta a posição do personagem.
     * 
     * @param int $account_id
     * @param int $char_id
     */
    public function resetPosition($account_id, $char_id)
    {
        // Se a configuração não permitir este reset retorna sem executar o resto do código.
        if(!BRACP_ALLOW_RESET_APPEAR)
            return false;

        // Executa a query no banco de dados e retorna true se foi resetado.
        return $this->getEntityManager()
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
                                c.char_id = :char_id AND
                                c.online = 0
                        ')
                        ->setParameter('account_id', $account_id)
                        ->setParameter('char_id', $char_id)
                        ->execute() > 0;
    }

    /**
     * Reseta os equipamentos do personagem.
     *
     * @param int $account_id
     * @param int $char_id
     */
    public function resetEquip($account_id, $char_id)
    {
        // Se a configuração não permitir este reset retorna sem executar o resto do código.
        if(!BRACP_ALLOW_RESET_EQUIP)
            return false;

        // Obtém todos os itens no inventário do jogador.
        $items = $this->getEntityManager()
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
                        ->setParameter('account_id', $account_id)
                        ->setParameter('char_id', $char_id)
                        ->getResult();

        // Se o jogador não possuir itens equipados, retorna sem atualizar nada.
        if(count($items) == 0)
            return false;

        foreach($items as $item)
        {
            $item->setEquip(false);
            $this->getEntityManager()->merge($item);
        }

        $this->getEntityManager()->flush();
        return true;
    }

    /**
     * Cria a conta no banco de dados.
     *
     * @param Login $acc
     *
     * @return boolean
     */
    public function createAccount(Login $acc)
    {
        // Verifica se o nome de usuario está disponivel para uso.
        //  - Caso e-mail esteja configurado para apenas um uso, faz o mesmo.
        if($this->checkUserId($acc->getUserid()) 
            || BRACP_MAIL_REGISTER_ONCE && $this->checkEmail($acc->getEmail()))
            return 0;

        // Grava o objeto no banco de dados.
        $this->getEntityManager()->persist($acc);
        $this->getEntityManager()->flush();

        // Se permitir o envio de e-mail, envia o e-mail para o usuário com as configurações
        //  necessárias para uma possivel ativação da conta.
        if(BRACP_ALLOW_MAIL_SEND)
        {
            // Envia o e-mail de criação da conta.
            $this->sendMail('Sua conta foi criada!', [$acc->getEmail()], 'mail.create', [
                'userid' => $acc->getUserid(),
                'ipAddress' => $this->request()->getIp()
            ]);
        }

        // Retorna que foi possivel criar a conta.
        return 1;
    }

    /**
     * Verifica a existência do usuário no banco de dados para realizar login.
     *
     * @param string $userid
     * @param string $user_pass
     *
     * @return mixed
     */
    public function checkUserAndPass($userid, $user_pass)
    {
        // Verifica os usuários cadastrados com o usuário e senha
        $user = $this->getEntityManager()->getRepository('Model\Login')->findOneBy([
            'userid' => $userid,
            'user_pass' => ((BRACP_MD5_PASSWORD_HASH) ? hash('md5', $user_pass):$user_pass),
            'state' => 0
        ]);

        return ((is_null($user)) ? false : $user);
    }

    /**
     * Verifica se o endereço de e-mail já está cadastrado no banco de dados.
     *
     * @param string $email
     *
     * @return bool
     */
    public function checkEmail($email)
    {
        // Se houver retorno na verificação, então existe email cadastrado.
        return !is_null($this->getEntityManager()
                                ->getRepository('Model\Login')
                                ->findOneBy(['email' => $email]));
    }

    /**
     * Verifica se o nome de usuário informado existe no banco de dados.
     *
     * @param string $userid
     *
     * @return bool
     */
    public function checkUserId($userid)
    {
        return !is_null($this->getEntityManager()
                                ->getRepository('Model\Login')
                                ->findOneBy(['userid' => $userid]));
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
     * Renderiza o template para o e-mail.
     * 
     * @param string $template
     * @param array $data
     */
    public function renderMail($template, $data)
    {
        return $this->renderTemplate($template, $data, -1, null, null, false, -1, false);
    }

    /**
     * Renderiza o template a ser exibido.
     */
    public function renderTemplate($template, $data = [], $access = -1, $callable = null, $callableData = null, $blocked = false, $gmlevel = -1, $ajaxFile = true)
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
        if($ajaxFile && $this->request()->isAjax())
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
            $data = array_merge($data, ['account_id' => $this->acc->getAccount_id(),
                                        'acc_gmlevel' => $this->acc->getGroup_id()]);

        // Chama o view para mostrar o template.
        return $this->view()->render($template . '.tpl', $data);
    }

    /**
     * Nome do template para exibir.
     *
     * @param string $template
     * @param array $data
     * @param int $access { 0: never logged. 1: ever logged. -1: always. }
     */
    public function display($template, $data = [], $access = -1, $callable = null, $callableData = null, $blocked = false, $gmlevel = -1)
    {
        echo $this->renderTemplate($template, $data, $access,
                                    $callable, $callableData, $blocked, $gmlevel);
    }

    /**
     * Carrega todas as variaveis de configuração do sistema.
     */
    public function loadConfig()
    {
        // Realiza a leitura de todas as variaveis de configuração do sistema.
        $const = get_defined_constants(1);
        $user_const = $const['user'];
        $bracp_const = [];

        // Varre todas as constantes definidas pelo usuário.
        foreach($user_const as $k => $v)
        {
            // Se encaixar na configuração do painel de controle, BRACP_, PAG_ ou DONATION_
            //  então adiciona como constante de configuração do sistema.
            if(preg_match('/^(BRACP_|PAG_(INSTALL|EMAIL|TOKEN)|DONATION_)/i', $k))
                $bracp_const[$k] = (($v == false) ? '0':$v);
        }

        // Retorna todas as configurações encontradas para o sistema.
        return $bracp_const;
    }
}

