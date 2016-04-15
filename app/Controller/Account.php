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

use \PagSeguro\Checkout;
use \PagSeguro\CheckoutItem;
use \PagSeguro\Transaction;

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
        self::getApp()->display('account.login', self::loginAccount($request->getParsedBody()));
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
        unset(self::getApp()->getSession()->BRACP_ISLOGGEDIN, self::getApp()->getSession()->BRACP_ACCOUNTID);
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
        self::getApp()->display('account.register', self::registerAccount($request->getParsedBody()));
    }

    /**
     * Método para dados de registro da conta
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function registerByCode(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.register', self::registerAccount($request->getParsedBody(), $args['code']));
    }

    /**
     * Método para dados de registro da conta
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function registerResendCode(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.register', self::registerResend($args['account_id']));
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
        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.recover', self::recoverAccount($request->getParsedBody()));
    }

    /**
     * Método para recuperar a conta do usuário.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function recoverByCode(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Código de recuperação.
        $code = $args['code'];

        // Redireciona para a página principal
        self::getApp()->display('home', self::recoverAccount($request->getParsedBody(), $code));
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
        self::getApp()->display('account.change.password', self::passwordAccount($request->getParsedBody()));
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
        // Exibe as informações no template de cadastro.
        self::getApp()->display('account.change.mail', self::emailAccount($request->getParsedBody()));
    }

    /**
     * Método para exibição dos personagens e alterações.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function chars(ServerRequestInterface $request, ResponseInterface $response, $args)
    {

        $data = [];

        // Se é uma requisição tipo post, então
        //  tenta realizar a alteração dos personagens realizando o envio dos dados
        //  para o método.
        if($request->isPost())
            $data = array_merge($data, self::charsAccount($request->getParsedBody()));

        // Realiza a query para obter os personagens da conta logada.
        $chars = self::getApp()->getEm()
                        ->createQuery('
                            SELECT
                                chars
                            FROM
                                Model\Char chars
                            WHERE
                                chars.account_id = :account_id
                            ORDER BY
                                chars.char_num ASC
                        ')
                        ->setParameter('account_id', self::loggedUser()->getAccount_id())
                        ->getResult();

        $actions = 0;

        if(BRACP_ALLOW_RESET_APPEAR) $actions |= 1;
        if(BRACP_ALLOW_RESET_POSIT) $actions |= 2;
        if(BRACP_ALLOW_RESET_EQUIP) $actions |= 4;

        // Envia para tela os dados da conta logada.
        self::getApp()->display('account.chars', array_merge([
            'chars' => $chars,
            'actions' => $actions
        ], $data));
    }

    /**
     * Método para doações.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function donations(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados iniciais.
        $data = [];

        // Se houve envio de dados, então define os dados como o retorno
        //  de tratamento para o post.
        if($request->isPost())
            $data = self::donationAccount($request->getParsedBody());


        // Valor da multiplicação do bonus eletrônico.
        $multiply = DONATION_AMOUNT_MULTIPLY;

        // Obtém a promoção para as doações.
        $promotion = self::getApp()->getEm()
                                ->createQuery('
                                    SELECT
                                        promotion
                                    FROM
                                        Model\Promotion promotion
                                    WHERE
                                        promotion.canceled = false AND
                                        :curdate BETWEEN promotion.startDate AND promotion.endDate
                                ')
                                ->setParameter('curdate', date('Y-m-d H:i:s'))
                                ->getOneOrNullResult();

        // Promoção aumenta o multiply rate.
        if(!is_null($promotion))
            $multiply += intval($promotion->getBonusMultiply());

        // Todas as doações para o usuário atual.
        $donations = self::getApp()->getEm()
                                    ->createQuery('
                                        SELECT
                                            donation,
                                            promotion,
                                            login
                                        FROM
                                            Model\Donation donation
                                        LEFT JOIN
                                            donation.promotion promotion
                                        INNER JOIN
                                            donation.account login
                                        WHERE
                                            login.account_id = :account_id
                                        ORDER BY
                                            donation.id DESC
                                    ')
                                    ->setParameter('account_id', self::loggedUser()->getAccount_id())
                                    ->getResult();

        // Varre todas as doações antes de escrever em tela.
        foreach($donations as $donation)
        {
            // Se uma doação estiver com o código de checkout em branco
            //  irá então, cancelar a doação.
            if(empty($donation->getCheckoutCode()))
            {
                $donation->setStatus('CANCELADO');
                self::getApp()->getEm()->merge($donation);
                self::getApp()->getEm()->flush();
            }
        }

        // Verifica se existem promoções a serem exibidas em tela.
        // Se houver, seleciona as promoções com data:
        // 
        // 1. Acima do dia de hoje.
        // 2. Menor que o intervalo definido em tela.
        $promotions = [];
        if(DONATION_SHOW_NEXT_PROMO)
        {
            $promotions = self::getApp()->getEm()
                                        ->createQuery('
                                            SELECT
                                                promotion
                                            FROM
                                                Model\Promotion promotion
                                            WHERE
                                                promotion.startDate > :curdate AND
                                                promotion.startDate <= :nextdate AND
                                                promotion.canceled = false
                                            ORDER BY
                                                promotion.startDate ASC
                                        ')
                                        ->setParameter('curdate', date('Y-m-d'))
                                        ->setParameter('nextdate', date('Y-m-d', time() + (DONATION_INTERVAL_DAYS * 86400)))
                                        ->getResult();
        }

        // Template de doações carrega com os dados sendo informados.
        self::getApp()->display('account.donations', array_merge($data, [
            'promotion'     => $promotion,
            'multiply'      => $multiply,
            'donations'     => $donations,
            'promotions'    => $promotions
        ]));
    }

    /**
     * Método para doações.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function donationsNotify(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Somente aceita requisições do tipo POST.
        if(!$request->isPost())
            return;

        // Dados a serem tratados pela notificação da transação.
        $data = $request->getParsedBody();

        // Obtém os dados da notificação para poder trabalhar com a doação.
        $notification = Transaction::checkNotification($data['notificationCode']);

        // Obtém a doação salva no banco de dados para realizar as verificações
        //  pelo código da transação no site do pagseguro.
        $donation = self::getApp()->getEm()
                                ->createQuery('
                                    SELECT
                                        donation,
                                        promotion,
                                        login
                                    FROM
                                        Model\Donation donation
                                    LEFT JOIN
                                        donation.promotion promotion
                                    INNER JOIN
                                        donation.account login
                                    WHERE
                                        donation.reference = :reference AND
                                        donation.transactionCode = :transactionCode
                                ')
                                ->setParameter('reference', $notification->reference)
                                ->setParameter('transactionCode', $notification->code)
                                ->getOneOrNullResult();

        // Verifica se a notificação do pagseguro realmente existe no banco de dados
        //  Se existir, faz uma atualização da doação do jogador.
        if(!is_null($donation))
        {
            self::donationCheck($donation);
        }
    }

    /**
     * Método para doações.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function donationsCheck(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Todas as doações para o usuário atual.
        $donations = self::getApp()->getEm()
                                    ->createQuery('
                                        SELECT
                                            donation,
                                            promotion,
                                            login
                                        FROM
                                            Model\Donation donation
                                        LEFT JOIN
                                            donation.promotion promotion
                                        INNER JOIN
                                            donation.account login
                                        WHERE
                                            login.account_id = :account_id
                                        ORDER BY
                                            donation.id DESC
                                    ')
                                    ->setParameter('account_id', self::loggedUser()->getAccount_id())
                                    ->getResult();


        // Obtém os dados enviados pela requisição.
        $data = $request->getParsedBody();

        // Caso existam dados a serem processados.
        if(isset($data) && count($data) > 0)
        {
            // Obtém todas as doações enviadas via POST.
            $DonationID = $data['DonationID'];

            // Varre as doações procurando as doações a serem verificadas.
            foreach($donations as $donation)
            {
                // Se a doação estiver no array de verificação, então
                //  realiza uma requisição ao PagSeguro para 
                if(in_array($donation->getId(), $DonationID) && !empty($donation->getTransactionCode()))
                {
                    self::donationCheck($donation);
                }
            }
        }

        // Template de doações carrega com os dados sendo informados.
        self::getApp()->display('account.donations.table', [
            'donations' => $donations
        ], false);
    }

    /**
     * Obtém as requisições para tratamento dos dados de pagseguro.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function transaction(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Recebe os dados relacionados a transação informada.
        $data = $request->getParsedBody();

        // Verifica se estão definidos os dados de retorno para o ajax.
        if(isset($data['DonationID']) && isset($data['checkoutCode']))
        {
            // Obtém a doação gerada no banco de dados.
            $donation = self::getApp()->getEm()
                                    ->createQuery('
                                        SELECT
                                            donation,
                                            promo,
                                            login
                                        FROM
                                            Model\Donation donation
                                        LEFT JOIN
                                            donation.promotion promo
                                        INNER JOIN
                                            donation.account login
                                        WHERE
                                            donation.id = :id AND
                                            donation.checkoutCode = :checkoutCode AND
                                            donation.paymentDate IS NULL AND
                                            donation.cancelDate IS NULL AND
                                            donation.transactionCode IS NULL AND
                                            login.account_id = :account_id
                                    ')
                                    ->setParameter('id', $data['DonationID'])
                                    ->setParameter('checkoutCode', $data['checkoutCode'])
                                    ->setParameter('account_id', self::loggedUser()->getAccount_id())
                                    ->getOneOrNullResult();

            // Verifica se a doação realmente existe no banco de dados.
            if(!is_null($donation))
            {
                // Se for para realizar o cancelamento, então
                //  cancela os dados da transação.
                if(isset($data['cancel']) && $data['cancel'] == true)
                {
                    $donation->setStatus('CANCELADO');
                    $donation->setCancelDate(date('Y-m-d H:i:s'));
                }
                // Se for para adicionar código de transação, então salva o código de transação.
                else if(isset($data['transactionCode']))
                {
                    $donation->setTransactionCode($data['transactionCode']);
                }

                // Atualiza os dados da transação no banco de dados.
                self::getApp()->getEm()->merge($donation);
                self::getApp()->getEm()->flush();
            }
        } /* fim - else if(isset($data['DonationID']) && isset($data['checkoutCode'])) */

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
     * Realiza o reset de aparência do personagem.
     *
     * @param integer $account_id
     * @param integer $char_id
     *
     * @return boolean
     */
    public static function charResetAppear($account_id, $char_id)
    {
        return self::getApp()->getEm()
                            ->createQuery('
                                UPDATE
                                    Model\Char chars
                                SET
                                    chars.hair = 0,
                                    chars.hair_color = 0,
                                    chars.clothes_color = 0,
                                    chars.head_top = 0,
                                    chars.head_mid = 0,
                                    chars.head_bottom = 0,
                                    chars.robe = 0
                                WHERE
                                    chars.account_id = :account_id AND
                                    chars.char_id = :char_id AND
                                    chars.online = 0
                            ')
                            ->setParameter('account_id', $account_id)
                            ->setParameter('char_id', $char_id)
                            ->execute() > 0;
    }

    /**
     * Realiza o reset de posição do personagem.
     *
     * @param integer $account_id
     * @param integer $char_id
     *
     * @return boolean
     */
    public static function charResetPosit($account_id, $char_id)
    {
        return self::getApp()->getEm()
                            ->createQuery('
                                UPDATE
                                    Model\Char chars
                                SET
                                    chars.last_map = chars.save_map,
                                    chars.last_x = chars.save_x,
                                    chars.last_y = chars.save_y
                                WHERE
                                    chars.account_id = :account_id AND
                                    chars.char_id = :char_id AND
                                    chars.online = 0
                            ')
                            ->setParameter('account_id', $account_id)
                            ->setParameter('char_id', $char_id)
                            ->execute() > 0;
    }

    /**
     * Reseta os equipamentos do personagem.
     *
     * @param integer $account_id
     * @param integer $char_id
     *
     * @return boolean
     */
    public static function charResetEquip($account_id, $char_id)
    {
        // Obtém todos os itens do inventório que estão equipados para o personagem do jogador.
        $inventory = self::getApp()->getEm()
                                    ->createQuery('
                                        SELECT
                                            inventory,
                                            chars
                                        FROM
                                            Model\Inventory inventory
                                        INNER JOIN
                                            inventory.character chars
                                        WHERE
                                            chars.account_id = :account_id AND
                                            chars.char_id = :char_id AND
                                            chars.online = 0 AND
                                            inventory.equip = 1
                                    ')
                                    ->setParameter('account_id', $account_id)
                                    ->setParameter('char_id', $char_id)
                                    ->getResult();

        // Verifica se existem itens no inventário a serem resetados.
        // Se houver itens, então, desequipa e salva as alterações no banco de dados.
        if(($updated = count($inventory) > 0))
        {
            // Remove o item do inventário do jogador um a um.
            foreach($inventory as $entry)
            {
                $entry->setEquip(0);

                self::getApp()->getEm()->merge($entry);
                self::getApp()->getEm()->flush();
            }
        }

        // Retorna dependendo da condição se houve itens a atualizar ou não.
        return $updated;
    }

    /**
     * Método utilizado para resetar informações dos personagens.
     *
     * @param array $data
     *
     * @return array Mensagem de retorno.
     */
    public static function charsAccount($data)
    {
        // Escreve acesso de log aos dados de personagem:
        LogWriter::write("Atualizando informações de personagem com os dados recebidos...\n" .
            print_r($data, true));

        // Verifica se alguma opção do painel de controle está habilitada.
        // Se não estiver, envia mensagem de erro no retorno.
        if(BRACP_ALLOW_RESET_APPEAR || BRACP_ALLOW_RESET_POSIT || BRACP_ALLOW_RESET_EQUIP)
        {
            // Obtém a conta logada para realizar as alterações no personagem.
            $account_id = self::loggedUser()->getAccount_id();

            // Variavel temporaria para armazenar as informações dos personagens resetados.
            $appear = $posit = $equip = [];

            // Verifica se a configuração de alteração de aparência pode
            //  ser realizada.
            if(BRACP_ALLOW_RESET_APPEAR && isset($data['appear']) && count($data['appear']) > 0)
            {
                foreach($data['appear'] as $char_id)
                {
                    if(self::charResetAppear($account_id, $char_id))
                    {
                        $appear[] = $char_id;
                    }
                }
            }

            // Verifica se a posição pode ser alterada e se dados para
            //  resetar foram realizados com sucesso.
            if(BRACP_ALLOW_RESET_POSIT && isset($data['posit']) && count($data['posit']) > 0)
            {
                foreach($data['posit'] as $char_id)
                {
                    if(self::charResetPosit($account_id, $char_id))
                    {
                        $posit[] = $char_id;
                    }
                }
            }

            // Verifica as configuração para poder restar os dados de equipamentos
            //  e se foi realizado com sucesso.
            if(BRACP_ALLOW_RESET_EQUIP && isset($data['equip']) && count($data['equip']) > 0)
            {
                foreach($data['equip'] as $char_id)
                {
                    if(self::charResetEquip($account_id, $char_id))
                    {
                        $equip[] = $char_id;
                    }
                }
            }

            // Mensagens de retorno.
            $message = [];

            // Verifica se algum das informações foi resetado com sucesso para dar de informação
            //  na tela a mensagem.
            if(count($appear) > 0)
                $message[] = 'Visual resetado com sucesso para o(s) personagem(ns): <strong>'. implode(', ', $appear) .'</strong>.';
            if(count($posit) > 0)
                $message[] = 'Local resetado com sucesso para o(s) personagem(ns): <strong>'. implode(', ', $posit) .'</strong>.';
            if(count($equip) > 0)
                $message[] = 'Equipamento resetado com sucesso para o(s) personagem(ns): <strong>'. implode(', ', $equip) .'</strong>.';

            // Retorna mensagem de sucesso para as alterações.
            return ['char_message' => ['success' => ((count($message) > 0) ?
                                                            implode('<br>', $message) :
                                                            'Comando(s) executado(s) com sucesso. Nenhum personagem foi alterado.')]];
        }
        else
        {
            // Escreve acesso de log aos dados de personagem:
            LogWriter::write("Configurações para alteração de personagem estão desabilitadas.\n" .
                print_r($data, true), 1);

            // Caso nenhuma configuração esteja habilitada.
            return ['char_message' => ['error' => 'Impossível realizar ação solicitada.']];
        }
    }

    /**
     * Verifica a doação e atualiza seu status no banco de dados.
     *
     * @param integer $donationId
     *
     * @return boolean
     */
    public static function donationCheck(Donation $donation)
    {
        // Escreve acesso de log aos dados de personagem:
        LogWriter::write("Iniciando verificação de doação com transção via PagSeguro.\n\n".print_r($donation, true));

        // Obtém os dados de transação para o código informado.
        $transaction = Transaction::checkTransaction($donation->getTransactionCode());

        // 1: Aguardando Pgto.
        // 2: Pagamento em Analise
        if($donation->getStatus() != 'INICIADA' && ($transaction->status == 1 || $transaction->status == 2))
        {
            $donation->setStatus('INICIADA');
        }
        // 3: Paga
        // 4: Disponivel (Sem disputa)
        else if($donation->getStatus() != 'PAGO' && ($transaction->status == 3 || $transaction->status == 4))
        {
            // Escreve acesso de log aos dados de personagem:
            LogWriter::write("Atualizando informações de doação com estado pago.\n\n".print_r($donation, true));

            $donation->setStatus('PAGO');
            $donation->setPaymentDate(date('Y-m-d H:i:s'));

            // Se o jogador não marcou a opção para não receber os bônus
            //  a sua conta, então gerar a compensação para o jogador.
            if($donation->getReceiveBonus())
            {
                // Escreve acesso de log aos dados de personagem:
                LogWriter::write("Adicionado estado de compensação.\n\n".print_r($donation, true));

                // Cria o objeto de compensação no banco de dados
                //  para identificar que é nessário dar ao jogador in-game as informações.
                $compensate = new Compensate();
                $compensate->setDonation($donation);
                $compensate->setPending(true);
                $compensate->setDate(null);

                // Grava os dados de compensação no banco de dados.
                self::getApp()->getEm()->persist($compensate);
            }
        }
        // 5: Em disputa
        // 6: Devolvida
        // 7: Cancelado
        // 8: Charback debitado
        // 9: Em contestação
        else if($donation->getStatus() != 'CANCELADO'
            && ($transaction->status == 7 || $transaction->status == 8 || $transaction->status == 5 || $transaction->status == 6 || $transaction->status == 9))
        {
            // Escreve acesso de log aos dados de personagem:
            LogWriter::write("Alterando estado de doação para cancelado.\n\n".print_r($donation, true));

            // Se a doação antes estava paga, bloqueia a conta do jogador.
            if($donation->getStatus() == 'PAGO' && $donation->getCompensate())
            {
                // Se a doação já foi compensada, então, bloqueará a conta do jogador.
                if($donation->getCompensate())
                {
                    // Escreve acesso de log aos dados de personagem:
                    LogWriter::write("Doação já compensada, bloqueando conta do jogador.\n\n".print_r($donation, true));

                    $donation->getAccount()->setState(5);
                    self::getApp()->getEm()->merge($donation->getAccount());
                }
                else
                {
                    // Escreve acesso de log aos dados de personagem:
                    LogWriter::write("Removendo compensação do banco de dados.\n\n".print_r($donation, true));

                    // Obtém o objeto da compensação da doação.
                    $compensate = self::getApp()->getEm()
                                                ->createQuery('
                                                    SELECT
                                                        compensate,
                                                        donation
                                                    FROM
                                                        Model\Compensate compensate
                                                    INNER JOIN
                                                        compensate.donation donation
                                                    WHERE
                                                        compensate.pending = true AND
                                                        donation.id = :id
                                                ')
                                                ->setParameter('id', $donation->getId())
                                                ->getOneOrNullResult();

                    // Remove a compensação do banco de dados.
                    self::getApp()->getEm()->remove($compensate);
                }
            }

            // Marca a doação como cancelada e atualiza as informações de cancelamento.
            $donation->setStatus('CANCELADO');
            $donation->setCancelDate(date('Y-m-d H:i:s'));
        }

        // Atualiza a doação no banco de dados.
        self::getApp()->getEm()->merge($donation);
        self::getApp()->getEm()->flush();

        // Verifica se permite o envio de e-mails para notificar o cliente.
        // Envia o e-mail para o usuário informando o que aconteceu com a doação dele.
        if(BRACP_ALLOW_MAIL_SEND)
        {
            self::donationNotifyMail($donation);
        }
    }

    /**
     * Método utilizado para tratar o recebimento dos dados de doação para registrar
     *  e possívelmente iniciar a doação para o jogador.
     *
     * @static
     *
     * @return array
     */
    public static function donationAccount($data)
    {
        // Verificação recaptcha para saber se a requisição realizada
        //  é verdadeira e pode continuar.
        if(BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['g-recaptcha-response']))
            return ['donation_message' => ['error' => 'Código de verificação inválido. Verifique por favor.']];

        // Verifica se a doação foi aceita pelo jogador.
        if(!isset($data['acceptdonation']) or $data['acceptdonation'] <> 'on')
            return ['donation_message' => ['error' => 'Você deve aceitar os termos de doação antes de continuar.']];

        // Verifica se o valor digitado está em formato incorreto.
        if(floatval($data['donationValue']) <= 0)
            return ['donation_message' => ['error' => 'Valor para a doação incorreto. Verifique por favor.']];

        // Valor para doações.
        $donationValue = floatval($data['donationValue']);
        $bonusMultiply = DONATION_AMOUNT_MULTIPLY;

        // Obtém a promoção para as doações.
        $promotion = self::getApp()->getEm()
                                ->createQuery('
                                    SELECT
                                        promotion
                                    FROM
                                        Model\Promotion promotion
                                    WHERE
                                        promotion.canceled = false AND
                                        :curdate BETWEEN promotion.startDate AND promotion.endDate
                                ')
                                ->setParameter('curdate', date('Y-m-d H:i:s'))
                                ->getOneOrNullResult();

        // Se houver promoção adiciona a promoção ao valor de bônus.
        if(!is_null($promotion))
            $bonusMultiply += $promotion->getBonusMultiply();

        // Inicializa o objeto de doação.
        $donation = new Donation;
        $donation->setPromotion($promotion);
        $donation->setDate(date('Y-m-d'));
        $donation->setReference(strtoupper(hash('md5', microtime(true))));
        $donation->setDrive('PAGSEGURO');
        $donation->setAccount(self::loggedUser());
        $donation->setValue($donationValue);
        $donation->setBonus($donationValue * $bonusMultiply);
        $donation->setTotalValue($donationValue);
        $donation->setCheckoutCode(null);
        $donation->setTransactionCode(null);
        $donation->setReceiveBonus(!isset($data['donotreceivebonus']));

        // Se usa valores com ajuste de taxas, então aplica o calculo para ajustar as taxas.
        if(DONATION_AMOUNT_USE_RATE)
            $donation->setTotalValue(($donationValue/(1 - .0399)) + .4);

        // Salva a nova entrada no banco de dados.
        self::getApp()->getEm()->persist($donation);
        self::getApp()->getEm()->flush();

        // Verifica se pode enviar e-mails de acordo com a configuração do painel de controle
        //  para informar ao usuário que sua doação foi criada pelo endereço de e-mail.
        if(BRACP_ALLOW_MAIL_SEND)
        {
            // Envia o e-mail ao usuário informando que a doação foi criada no sistema
            // E está aguardando pagamento.
            self::donationNotifyMail($donation);
        }

        try
        {
            // Realiza a requisição para o pagseguro criar o checkoutcode.
            $checkout = new Checkout();
            $checkoutResponse = $checkout->setCurrency('BRL')
                                        ->addItem(new CheckoutItem( 'BONUS_ELETRONICO',
                                                 "Doação - Bônus Eletrônico ({$donation->getBonus()})",
                                                 sprintf('%.2f', $donation->getTotalValue()),
                                                 '1'))
                                        ->setReference($donation->getReference())
                                        ->addMetaKey('PLAYER_ID', $donation->getAccount()->getAccount_id())
                                        ->sendRequest();

            // Define o código de checkout para a doação.
            $donation->setCheckoutCode($checkoutResponse->code);

            // Atualiza os dados da doação no banco de dados.
            self::getApp()->getEm()->merge($donation);
            self::getApp()->getEm()->flush();

             // Retorna os dados de checkout para o painel de controle abrir o PagSeguro.
            return ['donation_message' => ['success' => 'Sua doação foi registrada em nosso sistema! Muito obrigado!'],
                    'checkoutCode' => $donation->getCheckoutCode(),
                    'donationId' => $donation->getId()];
        }
        catch(\Exception $ex)
        {
            return ['donation_message' =>
                        [
                            'error' => ((BRACP_DEVELOP_MODE) ? $ex->getMessage():'Ocorreu um erro durante o registro da sua doação.')
                        ]
                   ];
        }

    }

    /**
     * Envia um e-mail ao usuário informando que a doação foi atualizada.
     *
     * @param Donation $donation
     *
     * @return void
     */
    public static function donationNotifyMail(Donation $donation)
    {
        // Dados a serem enviados ao e-mail.
        $data = [
            'donation' => $donation,
        ];

        // Se o usuário não estiver logado, provavelmente é uma requisição do pag-seguro, então
        //  adiciona o nome de usuário da doação a requisição.
        if(!self::isLoggedIn())
            $data = array_merge(['userid' => $donation->getAccount()->getUserid()], $data);

        // Envia o e-mail ao usuário informando que a doação foi criada no sistema
        // E está aguardando pagamento.
        self::getApp()->sendMail(
            'Doação Atualizada',
            [$donation->getAccount()->getEmail()],
            'mail.donation.notify', $data);
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
            return ['email_message' => ['error' => 'Usuários administradores não podem realizar alteração de e-mail.']];

        // Verificação recaptcha para saber se a requisição realizada
        //  é verdadeira e pode continuar.
        if(BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['g-recaptcha-response']))
            return ['email_message' => ['error' => 'Código de verificação inválido. Verifique por favor.']];

        // Verifica se o email atual digitado é igual ao email atual.
        if(hash('md5', self::loggedUser()->getEmail()) !== hash('md5', $data['email']))
            return ['email_message' => ['error' => 'E-mail atual não confere com o digitado.']];

        // Verifica se o email novo digitado é igual ao email de confirmação.
        if(hash('md5', $data['email_new']) !== hash('md5', $data['email_conf']))
            return ['email_message' => ['error' => 'Os e-mails digitados não conferem.']];

        // Verifica se o email atual é igual ao email novo digitado.
        if(hash('md5', self::loggedUser()->getEmail()) === hash('md5', $data['email_new']))
            return ['email_message' => ['error' => 'O Novo endereço de e-mail não pode ser igual ao atual.']];

        // Verifica se foi possivel alterar o endereço de e-mail do usuário.
        if(self::changeMail(self::loggedUser()->getAccount_id(), $data['email_new']))
            return ['email_message' => ['success' => 'Seu endereço de e-mail foi alterado com sucesso.']];
        else
            // Ocorre quando o endereço de e-mail já está em uso.
            return ['email_message' => ['error' => 'Ocorreu um erro durante a alteração do seu endereço.']];
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
            return ['password_message' => ['error' => 'Usuários do tipo administrador não podem realizar alteração de senha.']];

        // Verificação recaptcha para saber se a requisição realizada
        //  é verdadeira e pode continuar.
        if(BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['g-recaptcha-response']))
            return ['password_message' => ['error' => 'Código de verificação inválido. Verifique por favor.']];

        // Obtém a senha atual do jogador para aplicação do md5 na comparação da senha.
        $user_pass = self::loggedUser()->getUser_pass();
        if(!BRACP_MD5_PASSWORD_HASH)
            $user_pass = hash('md5', $user_pass);

        // Verifica senha atual digitada.
        if(hash('md5', $data['user_pass']) !== $user_pass)
            return ['password_message' => ['error' => 'Senha atual digitada não confere.']];

        // Verifica novas senhas digitadas.
        if(hash('md5', $data['user_pass_new']) !== hash('md5', $data['user_pass_conf']))
            return ['password_message' => ['error' => 'Novas senhas digitadas não conferem.']];

        // Verifica se a senha nova é igual a anterior.
        if(hash('md5', $data['user_pass_new']) === $user_pass)
            return ['password_message' => ['error' => 'Sua nova senha não pode ser igual a senha anterior.']];

        // Senha alterada com sucesso.
        if(self::changePass(self::loggedUser()->getAccount_id(), $data['user_pass_new']))
            return ['password_message' => ['success' => 'Sua senha foi alterada com sucesso!']];
        else
            return ['password_message' => ['error' => 'Ocorreu um erro durante a alteração de sua senha.']];
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
                return ['recover_message' => ['error' => 'O Código de recuperação já foi utilizado ou é inválido.']];

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

                return ['recover_message' => ['success' => 'A Nova senha foi enviada para seu endereço de e-mail.']];
            }
            else
            {
                return ['recover_message' => ['error' => 'Não foi possível recuperar a senha de usuário.']];
            }
        }
        else
        {
            // Verificação recaptcha para saber se a requisição realizada
            //  é verdadeira e pode continuar.
            if(BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['g-recaptcha-response']))
                return ['recover_message' => ['error' => 'Código de verificação inválido. Verifique por favor.']];

            // Obtém a conta que está sendo solicitada a requisição para 
            //  recuperação de senha.
            $account = self::getApp()->getEm()
                                        ->getRepository('Model\Login')
                                        ->findOneBy(['userid' => $data['userid'], 'email' => $data['email']]);

            // Objeto da conta não encontrado.
            if(is_null($account))
                return ['recover_message' => ['error' => 'Combinação de usuário e e-mail não encontrados.']];

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
                        'href' => BRACP_URL . 'account/recover'
                    ]);

                // Informa que o código de recuperação foi enviado ao e-mail do usuário.
                return ['recover_message' => ['success' => 'Foi enviado um e-mail contendo os dados de recuperação. Verifique seu e-mail.']];
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
                return ['recover_message' => ['success' => 'Os dados de sua conta foram enviados ao seu e-mail.']];
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
        // Verificação recaptcha para saber se a requisição realizada
        //  é verdadeira e pode continuar.
        if(BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['g-recaptcha-response']))
            return ['login_message' => ['error' => 'Código de verificação inválido. Verifique por favor.']];

        // Obtém a senha que será utilizada para realizar login.
        $user_pass = ((BRACP_MD5_PASSWORD_HASH) ? hash('md5', $data['user_pass']) : $data['user_pass']);

        // Tenta obter a conta que fará login no painel de controle.
        $account = self::getApp()->getEm()
                                    ->getRepository('Model\Login')
                                    ->findOneBy(['userid' => $data['userid'], 'user_pass' => $user_pass]);

        // Se a conta retornada for igual a null, não foi encontrada
        //  Então, retorna mensagem de erro.
        if(is_null($account))
            return ['login_message' => ['error' => 'Combinação de usuário e senha incorretos.']];

        // Se a conta do usuário é inferior ao nivel mínimo permitido
        //  para login, então retorna mensagem de erro.
        if($account->getGroup_id() < BRACP_ALLOW_LOGIN_GMLEVEL || $account->getState() != 0)
            return ['login_message' => ['error' => 'Acesso negado. Você não pode realizar login.']];

        // Define os dados de sessão para o usuário.
        self::getApp()->getSession()->BRACP_ISLOGGEDIN = true;
        self::getApp()->getSession()->BRACP_ACCOUNTID = $account->getAccount_id();

        // Retorna mensagem de login realizado com sucesso.
        return ['login_message' => ['success' => 'Login realizado com sucesso. Aguarde...']];
    }

    /**
     * Método utilizado para re-envio do código de ativação da conta.
     *
     * @static
     *
     * @param integer $account_id
     *
     * @return array
     */
    public static function registerResend($account_id)
    {
        return ['register_message' => ['error' => '@Todo: Terminando o desenvolvimento dessa parte aqui. (>e_w_e)>']];
    }

    /**
     * Método utilizado para verificar os dados de post para poder gravar no banco de dados
     *  as informações para a nova conta criada.
     *
     * @static
     *
     * @return array
     */
    public static function registerAccount($data, $code = null)
    {
        // Definições confirmação de contas.
        if(BRACP_CONFIRM_ACCOUNT && !is_null($code))
        {
            // Verificação do banco de dados para saber se o código de recuperação foi
            //  enviado com sucesso.
            // -          2016-04-14, CHLFZ.
            // -> Verificar o state = 11, previne que contas bloqueadas (@block) consigam retornar ao jogo se caso possuirem um
            //    código de state = 5.
            // -> Dúvidas sobre os códigos de state: 'https://github.com/brAthena/brAthena/blob/master/src/login/login.c#L1317'
            //    state - 1 => Indica o código do switch case.
            $confirmation = self::getApp()->getEm()
                                        ->createQuery('
                                            SELECT
                                                confirmation, login
                                            FROM
                                                Model\Confirmation confirmation
                                            INNER JOIN
                                                recover.account login
                                            WHERE
                                                confirmation.code = :code AND
                                                confirmation.used = false AND
                                                login.state = 11 AND
                                                :CURDATETIME BETWEEN confirmation.date AND confirmation.expire
                                        ')
                                        ->setParameter('code', $code)
                                        ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                                        ->getOneOrNullResult();

            // Se não houver código de confirmação, então será informado mensagem de erro.
            if(is_null($confirmation))
                return ['register_message' => ['error' => 'O Código de confirmação já foi utilizado ou é inválido.']];

            // Define como utilizado e atualiza a conta do usuário como autorizada.
            $confirmation->setUsed(true);

            // Atualiza o registro como usado no banco de dados.
            self::getApp()->getEm()->merge($confirmation);
            self::getApp()->getEm()->flush();

            // Defina a conta de confirmação como OK.
            $confirmation->getAccount()->setState(0);

            // Atualiza a conta como ativa no banco de dados.
            self::getApp()->getEm()->merge($confirmation->getAccount());
            self::getApp()->getEm()->flush();

            // Envia o e-mail para usuário caso o painel de controle esteja com as configurações
            //  de envio ativas.
            self::getApp()->sendMail('Conta Confirmada', [$confirmation->getAccount()->getEmail()],
                                        'mail.create', ['userid' => $confirmation->getAccount()->getUserid()]);

            // Retorna mensagem que a conta foi criada com sucesso.
            return ['register_message' => ['success' => 'Você confirmou com sucesso sua conta. Você já pode realizar login.']];
        }
        else
        {
            // Verificação recaptcha para saber se a requisição realizada
            //  é verdadeira e pode continuar.
            if(BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['g-recaptcha-response']))
                return ['register_message' => ['error' => 'Código de verificação inválido. Verifique por favor.']];

            if(hash('md5', $data['user_pass']) !== hash('md5', $data['user_pass_conf']))
                return ['register_message' => ['error' => 'As senhas digitadas não conferem!']];

            // Verifica se os emails enviados são iguais.
            if(hash('md5', $data['email']) !== hash('md5', $data['email_conf']))
                return ['register_message' => ['error' => 'Os endereços de e-mail digitados não conferem!']];

            // Verifica se já existe usuário cadastrado para o userid indicado.
            if(self::checkUser($data['userid']) || (BRACP_MAIL_REGISTER_ONCE && self::checkMail($data['email'])))
                return ['register_message' => ['error' => 'Nome de usuário ou endereço de e-mail já está em uso.']];

            // Se a senha for hash md5, troca o valor para hash-md5.
            if(BRACP_MD5_PASSWORD_HASH)
               $data['user_pass'] = hash('md5', $data['user_pass']);

            // Cria o objeto da conta para ser salvo no banco de dados.
            $account = new Login;
            $account->setUserid($data['userid']);
            $account->setUser_pass($data['user_pass']);
            $account->setSex($data['sex']);
            $account->setEmail($data['email']);

            // 2016-04-12, CHLFZ: Adicionado para quando conta necessitar confirmação, criar com status 5 
            // 2016-04-14, CHLFZ: De acordo com 'https://github.com/brAthena/brAthena/blob/master/src/login/login.c#L1317'
            //                    o melhor estado para este tipo de confirmação é o 11. (result == -1, autorizado sendo state = 0)
            //                    Alterado de status 5 -> 11.
            if(BRACP_CONFIRM_ACCOUNT)
                $account->setState(11);

            // Salva os dados na tabela de usuário.
            self::getApp()->getEm()->persist($account);
            self::getApp()->getEm()->flush();

            // Se não for necessário a confirmação da conta do usuário, então, envia um e-mail de boas vindas
            // Se não, enfia um e-mail para confirmar a conta do usuario.
            if(!BRACP_CONFIRM_ACCOUNT)
            {
                // Envia o e-mail para usuário caso o painel de controle esteja com as configurações
                //  de envio ativas.
                self::getApp()->sendMail('Conta Registrada', [$account->getEmail()],
                                            'mail.create', ['userid' => $account->getUserid()]);

                // Retorna mensagem que a conta foi criada com sucesso.
                return ['register_message' => ['success' => 'Sua conta foi criada com sucesso! Você já pode realizar login.']];
            }
            else
            {
                // Cria a instância do objeto de confirmação para a conta.
                $confirmation = new Confirmation;
                $confirmation->setAccount($account);
                $confirmation->setCode(hash('md5', microtime(true)));
                $confirmation->setDate(date('Y-m-d H:i:s'));
                $confirmation->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
                $confirmation->setUsed(false);

                // Grava o código no banco de dados e envia o email.
                self::getApp()->getEm()->persist($confirmation);
                self::getApp()->getEm()->flush();

                // Envia o e-mail para usuário caso o painel de controle esteja com as configurações
                //  de envio ativas.
                self::getApp()->sendMail('Confirma seu Registro', [$account->getEmail()],
                                            'mail.create.code',
                                            [
                                                'userid' => $account->getUserid(),
                                                'code' => $confirmation->getCode(),
                                                'expire' => $confirmation->getExpire(),
                                                'href' => BRACP_URL . 'account/register'
                                            ]);

            }
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

