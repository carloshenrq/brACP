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
use \Model\Compensate;
use \Model\Confirmation;
use \Model\Char;

use \Format;
use \Session;
use \LogWriter;
use \Cache;
use \Language;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Account extends Caller
{
    /**
     * Obtém o usuário logado no sistema.
     * @var \Model\Login
     */
    private static $user = null;

    public function __construct(\brACPApp $app)
    {
        // Funções para teste de permissão.
        $needLogin = function() {
            return Account::isLoggedIn();
        };

        // Funções para teste de permissão.
        $needLogout = function() {
            return !Account::isLoggedIn();
        };

        parent::__construct($app, [
            // Rota de login é necessário estar deslogado para logar.
            // Caso contrario, irá retornar 404.
            'login_POST'            => $needLogout,
            'recover_POST'          => $needLogout,
            'register_POST'         => $needLogout,
            'confirmation_GET'      => $needLogout,
            'confirmation_POST'     => $needLogout,
            // Rota para logout, é necessário estar logado para
            //  para acessar, irá retornar 404.
            'logout_GET'            => $needLogin,
            'email_POST'            => $needLogin,
            'password_POST'         => $needLogin,
            'char_GET'              => $needLogin,
            'charList_GET'          => $needLogin,
        ]);
    }

    /**
     * Rota para os personagens, exibição dos personagens.
     *
     * @param array $get
     * @param array $post
     * @param array $response
     *
     * @return object
     */
    private function char_GET($get, $post, $response)
    {
        $this->getApp()
            ->display('account.chars', [
                'chars' => $this->getChars(),
            ]);
        return $response;
    }

    /**
     * Rota para obter informações de personagem para a conta informada.
     *
     * @param array $get
     * @param array $post
     * @param array $response
     *
     * @return object
     */
    private function charList_GET($get, $post, $response)
    {
        return $response->withJson($this->getChars());
    }

    /**
     * Obtém todos os personagens para uma determinada conta com detalhes.
     *
     * @param int $account_id 
     *
     * @return array
     */
    public function getChars($account_id = null)
    {
        if(is_null($account_id))
            $account_id = self::loggedUser()->getAccount_id();

        // Tenta obter a lista de personagem para a conta informada.
        // Nota¹: Depende do servidor selecionado.
        $_resultChars = $this->getApp()
                                ->getSvrEm()
                                ->createQuery('
                                    SELECT
                                        chars, inventory, guild
                                    FROM
                                        Model\Char chars
                                    LEFT JOIN
                                        chars.inventory inventory
                                    LEFT JOIN
                                        chars.guild guild
                                    WHERE
                                        chars.account_id = :account_id
                                ')
                                ->setParameter('account_id', $account_id)
                                ->getResult();

        $chars = [];
        // Exibe os detalhes para usuários somente se usuário estiver logado e: 
        // 1. Se os personagens forem da conta dele
        // 2. Se a conta que está logada é nivel administrador
        $details = (self::isLoggedIn() &&
                    ($account_id == self::loggedUser()->getAccount_id() ||
                    (BRACP_ALLOW_ADMIN && self::loggedUser()->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL))
                   );

        // Trata informações do personagem para serem retornadas.
        // -> Informa detalhes somente se:
        //  1. O personagem é o da conta informada
        //  2. A conta logada é nivel administrador. (O Painel de controle deve estar configurado para aceitar nivel administrador)
        foreach($_resultChars as $char)
            $chars[] = $this->parseCharData($char, $details);

        return $chars;
    }

    /**
     * Trata os dados do personagem e retorna.
     *
     * @param Char $char Personagem a ser devolvido as informações.
     * @param bool $details Detalhes como inventário do personagem.
     *
     * @return object
     */
    public function parseCharData(Char $char, $details = false)
    {
        $charData = [];

        // Informações normais dos personagens, todos podem saber sobre isso.
        $charData['account_id']     = $char->getAccount_id();
        $charData['char_id']        = $char->getChar_id();
        $charData['char_num']       = $char->getChar_num();
        $charData['name']           = $char->getName();
        $charData['class']          = $char->getClass();
        $charData['className']      = $this->getApp()->getFormat()->jobname($char->getClass());
        $charData['base_level']     = $char->getBase_level();
        $charData['job_level']      = $char->getJob_level();

        $charData['guild']          = null;
        if(!is_null($char->getGuild()))
        {
            $charData['guild']      = (object)[
                'id'        => $char->getGuild()->getId(),
                'name'      => $char->getGuild()->getName(),
                'level'     => $char->getGuild()->getGuild_lv(),
                'emblem'    => $char->getGuild()->getEmblem(),
            ];
        }

        // Se for definido os detalhes do char, irá retornar os zenys.
        // Se for consulta de ranking e estiver definido para obter os rankings e exibi-los
        // Também será obtido os zenys.
        if($details || (BRACP_ALLOW_RANKING_ZENY && BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY))
            $charData['zeny']       = $char->getZeny();
        
        // Se for definido os detalhes do char, irá retornar o status online do
        // Boneco.
        if($details || BRACP_ALLOW_SHOW_CHAR_STATUS)
            $charData['online']     = $char->getOnline();
        
        // Se for configurado para obter os detalhes do jogador.
        // Status, Inventário e etc...
        if($details)
        {
            // Obtém os dados de status do jogador.
            $charData['status'] = (object)[
                'str'   => $char->getStr(),
                'agi'   => $char->getAgi(),
                'vit'   => $char->getVit(),
                'int'   => $char->getInt(),
                'dex'   => $char->getDex(),
                'luk'   => $char->getLuk(),
                'hp'    => $char->getHp(),
                // Informações de status do hp
                'max_hp'=> $char->getMax_hp(),
                'prc_hp'=> ceil(($char->getHp()/$char->getMax_hp())*100), // Calculo de porcentagem de hp
                'sp'    => $char->getSp(),
                'max_sp'=> $char->getMax_sp(),
                'prc_sp'=> ceil(($char->getSp()/$char->getMax_sp())*100), // Calculo de porcentagem de sp
            ];

            // Obtém informações de localização do jogador.
            $charData['location'] = (object)[
                'save'      => (object)[
                    'map'   => $char->getSave_map(),
                    'x'     => $char->getSave_x(),
                    'y'     => $char->getSave_y(),
                ],
                'last'      => (object)[
                    'map'   => $char->getLast_map(),
                    'x'     => $char->getLast_x(),
                    'y'     => $char->getLast_x(),
                ]
            ];
        }

        return (object)$charData;
    }

    /**
     * Método utilizado para realizar os resets de personagem.
     *
     * @param int $type (1: Reset de Posição, 2: Reset de Aparência, 4: Reset de Equip)
     * @param int $char_id Código do personagem que será resetado.
     * @param int $account_id (default: null) Se null, obtém o da conta logada.
     *
     * @return int
     *   0: Resetada foi aplicado com sucesso.
     *   1: Usuário não está logado.
     *   2: Personagem não encontrado. (Online, Inexistente, não vinculado)
     *   3: Erro em uma das operações de reset.
     */
    public function charReset($type, $char_id, $account_id = null)
    {
        // Se não foi enviado o parametro da conta, então
        // Tenta obter os dados do usuário logado.
        if(is_null($account_id))
        {
            if(!self::isLoggedIn()) // Não está logado -> Error.
                return 1;

            // Obtém o código da conta que está logada.
            $account_id = self::loggedUser()->getAccount_id();
        }

        // Obtém o personagem para a conta informada.
        // Personagem online e vinculado a conta informada somente.
        $char = $this->getApp()
                    ->getSvrEm()
                    ->getRepository('Model\Char')
                    ->findOneBy([
                        'account_id'    => $account_id,
                        'char_id'       => $char_id,
                        'online'        => 0
                    ]);
        
        // Somente irá retornar isso caso:
        // 1. Personagem está online. (Para resetar, nao pode estar online)
        // 2. Personagem inexistente. (Apagou e ainda estava em cache [?])
        // 3. Personagem não vinculado a conta. (Hm... Tentando zoar o CP?)
        if(is_null($char))
            return 2;

        // Dados de reset para o personagem sempre verdadeiro.
        $resetInfo = [true, true, true];

        // Envia os dados de reset para os métodos informados.
        if(($type&1) == 1) $resetInfo[0] = $this->charResetPosit($char);
        if(($type&2) == 2) $resetInfo[1] = $this->charResetAppear($char);
        if(($type&4) == 4) $resetInfo[2] = $this->charResetEquip($char);

        // Apaga o cache dos personagems
        Cache::delete('BRACP_CHARS_' . $account_id);

        // Reseta informações do personagem.
        return (array_search(false, $resetInfo) === false ? 0 : 3);
    }

    /**
     * Método utilizado para resetar a posição do personagem informado.
     *
     * @param Char $char Personagem a ser resetado.
     *
     * @return bool Verdadeiro se resetado com sucesso.
     */
    public function charResetPosit(Char $char)
    {
        try
        {
            // Define as opções de reset para personagem.
            $char->setLast_map($char->getSave_map());
            $char->setLast_x($char->getSave_x());
            $char->setLast_y($char->getSave_y());

            // Salva as alterações no personagem.
            $this->getApp()->getSvrEm()->merge($char);
            $this->getApp()->getSvrEm()->flush();

            // Retorna verdadeiro na alteração.
            return true;
        }
        catch(\Exception $ex)
        {
            $this->getApp()->logException($ex);
            return false;
        }
    }

    /**
     * Método utilizado para realizar o reset de personagem.
     *
     * @param Model\Char $char Personagem a ser resetado.
     *
     * @return bool Verdadeiro se resetado com sucesso.
     */
    public function charResetAppear(Char $char)
    {
        try
        {
            // Reseta aparência do personagem.
            $char->setHair(0);
            $char->setClothes_color(0);
            $char->setBody(0);
            $char->setWeapon(0);
            $char->setShield(0);
            $char->setHead_top(0);
            $char->setHead_mid(0);
            $char->setHead_bottom(0);
            $char->setRobe(0);

            // Salva as alterações no personagem.
            $this->getApp()->getSvrEm()->merge($char);
            $this->getApp()->getSvrEm()->flush();

            return true;
        }
        catch(\Exception $ex)
        {
            $this->getApp()->logException($ex);
            return false;
        }
    }

    /**
     * Método utilizado para resetar o equipamento do jogador.
     *
     * @param Model\Char $char
     *
     * @return bool Verdadeiro se equipamentor resetado com sucesso.
     */
    public function charResetEquip(Char $char)
    {
        try
        {
            // @Todo: Obter inventário do jogador e resetar todos os itens com
            //        equip != 0
            return true;
        }
        catch(\Exception $ex)
        {
            $this->getApp()->logException($ex);
            return false;
        }
    }

    /**
     * Método utilizado para enviar o código de recuperação de contas.
     *
     * @param array $get
     * @param array $post
     * @param object $request
     *
     * @return object
     */
    private function recover_POST($get, $post, $response)
    {
        $return = ['error_state' => 0, 'success_state' => false];

        if(!$this->getApp()->testRecaptcha($post))
        {
            $return['error_state'] = 3;
        }
        else
        {
            // Tenta realizar o envio recuperação de senha e obtém o retorno
            if(isset($post['code']))
                $return['error_state'] = $this->recoverCode($post['code']);
            else
                $return['error_state'] = $this->recoverSend($post['userid'], $post['email']);

            $return['success_state'] = ($return['error_state'] == 0);

            // Erro de requisição?
            if(BRACP_RECAPTCHA_ENABLED && !$return['success_state'])
                $this->getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
        }

        // Responde com o retorno.
        return $response->withJson($return);
    }

    /**
     * Realiza a recuperação da conta do jogador com o código informado.
     *
     * @param string $code Código de recuperação
     *
     * @return int
     */
    private function recoverCode($code)
    {
        // -1: Recuperação de contas desabilitado.
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_ALLOW_RECOVER)
            return -1;
        
        // Código não está no pattern md5 válido...
        if(!$this->validate($code, '([0-9a-f]{32})'))
            return 2;

        // Obtém o código de recuperação no banco de dados.
        $recover = $this->getApp()
                        ->getCpEm()
                        ->createQuery('
                                SELECT
                                    recover
                                FROM
                                    Model\Recover recover
                                WHERE
                                    recover.code = :code AND
                                    recover.used = false AND
                                    :CURDATETIME BETWEEN recover.date AND recover.expire
                        ')
                        ->setParameter('code', $code)
                        ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                        ->getOneOrNullResult();

        // Verifica se o código de recuperação é válido.
        if(is_null($recover))
            return 1;

        // Define o código de recuperação como já utilizado e reenvia
        // Os dados de nova senha para o jogador.
        $recover->setUsed(true);
        $this->getApp()->getCpEm()->merge($recover);
        $this->getApp()->getCpEm()->flush();

        // Obtém a conta do jogador para re-enviar o código.
        $account = $this->getApp()
                        ->getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['account_id' => $recover->getAccount_id()]);
        
        // Conta inexistente? BUG? WTF?
        if(is_null($account))
            return 1;

        // Computa a nova senha para o jogador
        for($new_pass = '';
            strlen($new_pass) < BRACP_RECOVER_STRING_LENGTH;
            $new_pass .= substr(BRACP_RECOVER_RANDOM_STRING, (rand(1, strlen(BRACP_RECOVER_RANDOM_STRING)) - 1), 1));
        
        // Defina a senha da conta para a nova senha do jogador.
        $this->changePass($account->getUserid(), null, $new_pass, null, true);

        // Envia o e-mail para o jogador com a nova senha.
        $this->getApp()->sendMail('@RECOVER_MAIL_TITLE.SEND@', [
            $account->getEmail()
        ], 'mail.recover', [
            'userid'    => $account->getUserid(),
            'password'  => $new_pass,
        ]);

        // Ocorreu tudo ok.
        return 0;
   }

    /**
     * Realiza a recuperação da conta do jogador.
     *
     * @param string $userid
     * @param string $email
     *
     * @return int
     */
    private function recoverSend($userid, $email)
    {
        // Recuperação de usuário e senha somente por e-mail.
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_ALLOW_RECOVER)
            return -1;
        
        // Valida as expressões regulares.
        if(!$this->validate($userid, BRACP_REGEXP_USERNAME)
            || !$this->validate($email, BRACP_REGEXP_EMAIL))
            return 2;
        
        // Obtém a conta digitada.
        $account = $this->getApp()
                        ->getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid, 'email' => $email]);
        
        // Impossível recuperar contas tipo administrador caso
        // O modo administrador esteja ligado.
        if(is_null($account) || (BRACP_ALLOW_ADMIN && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL))
            return 1;
        
        // Com o código MD5 habilitado, é impossível recuperar a senha
        // Sendo necessário recriar a senha. Então, entra com a recuperação
        //  de código.
        if(BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE)
        {
            // Obtém os dados do código de recuperação.
            $recover = $this->getApp()
                            ->getCpEm()
                            ->createQuery('
                                SELECT
                                    recover
                                FROM
                                    Model\Recover recover
                                WHERE
                                    recover.account_id = :account_id AND
                                    recover.used = false AND
                                    :CURDATETIME BETWEEN recover.date AND recover.expire
                            ')
                            ->setParameter('account_id', $account->getACcount_id())
                            ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                            ->getOneOrNullResult();
            
            // Se não existir o código, então o mesmo será criado.
            // Caso exista, ele é quem será enviado.
            if(is_null($recover))
            {
                // Gera o código de recuperação para o usuário.
                $recover = new Recover;
                $recover->setAccount_id($account->getAccount_id());
                $recover->setCode(hash('md5', uniqid(rand().microtime(true), true)));
                $recover->setDate(date('Y-m-d H:i:s'));
                $recover->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
                $recover->setUsed(false);

                // Salva o código de recuperação no banco de dados.
                $this->getApp()->getCpEm()->persist($recover);
                $this->getApp()->getCpEm()->flush();
            }

            // Envia o e-mail para o titular da conta.
            $this->getApp()->sendMail('@RECOVER_MAIL_TITLE.CODE@',[
                    $account->getEmail()
                ],
                'mail.recover.code', [
                    'userid'    => $account->getUserid(),
                    'code'      => $recover->getCode(),
                    'expire'    => $recover->getExpire()
                ]);
        }
        else
        {
            $this->getApp()->sendMail('@RECOVER_MAIL_TITLE.SEND@', [
                $account->getEmail()
            ],
            'mail.recover', [
                'userid'    => $account->getUserid(),
                'password'  => $account->getUser_pass()
            ]);
        }

        return 0;
    }

    /**
     * Realiza o re-envio do código de confirmação do jogador.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function confirmation_POST($get, $post, $response)
    {
        // Dados de retorno para informações de erro.
        $return = ['error_state' => 0, 'success_state' => false];

        // Verifica os dados de recaptcha
        if(!$this->getApp()->testRecaptcha($post))
        {
            $return['error_state'] = 2;
        }
        else
        {
            if(isset($post['code']))
            {
                $return['error_state'] = $this->confirmAccountCode($post['code']);
            }
            else
            {
                // Tenta fazer o re-envio do código de confirmação para o usuário.
                $return['error_state'] = $this->sendConfirmationByUser(
                    $post['userid'],
                    $post['email']
                );
            }
            $return['success_state'] = ($return['error_state'] == 0);

            // Erro de requisição?
            if(BRACP_RECAPTCHA_ENABLED && !$return['success_state'])
                $this->getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
        }

        return $response->withJson($return);
    }

    /**
     * Rota para realizar alteração de endereço de e-mail.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function email_POST($get, $post, $response)
    {
        $return = ['error_state' => 0, 'success_state' => false];

        if(!$this->getApp()->testRecaptcha($post))
        {
            $return['error_state'] = 8;
        }
        else
        {
            // Tenta realizar a alteração de senha.
            $return['error_state'] = $this->changeMail(
                self::loggedUser()->getUserid(),
                $post['email'],
                $post['email_new'],
                $post['email_conf'],
                false
            );

            // Devolve informações de suscesso. (0 -> Sucesso, sempre)
            $return['success_state'] = ($return['error_state'] == 0);

            // Erro de requisição?
            if(BRACP_RECAPTCHA_ENABLED && !$return['success_state'])
                $this->getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
        }

        // exit;
        return $response->withJson($return);
    }

    /**
     * Realiza a alteração de e-mail da conta do jogador.
     * 
     * @param string $userid
     * @param string $old_email
     * @param string $new_email
     * @param string $new_email_conf
     * @param bool $admin
     *
     * @return
     */
    private function changeMail($userid, $old_email, $new_email, $new_email_conf, $admin = false)
    {
        // Alteração de e-mail desabilitado?
        if(!BRACP_ALLOW_CHANGE_MAIL)
            return -1;
        
        // Não validou os dados com as expressões regulares...
        if(!$this->validate($userid, BRACP_REGEXP_USERNAME) ||
            !$this->validate($old_email, BRACP_REGEXP_EMAIL) ||
            !$this->validate($new_email, BRACP_REGEXP_EMAIL))
            return 6;

        // Se o modo administrador for enviado mas o brACP
        // Estiver configurado para não poder usar modo administrador, então, desliga o parametro.
        if($admin && !BRACP_ALLOW_ADMIN)
            $admin = false;

        // Modo administrador não necessita verificar
        // Os dados de e-mail digitados.
        if(!$admin)
        {
            // E-mail antigo não pode ser igual ao anterior.
            if(hash('md5', $old_email) === hash('md5', $new_email))
                return 4;

            // Os e-mails digitados não conferem.
            if(hash('md5', $new_email) !== hash('md5', $new_email_conf))
                return 3;
        }

        // Objeto da conta informada.
        $account = $this->getApp()
                        ->getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid]);
        
        // Se a conta não existir ou se os endereços de e-mail
        // antigos digitados não coincidirem.
        if(is_null($account) || (!$admin && hash('md5', $old_email) !== hash('md5', $account->getEmail())))
            return 2;

        // Outras verificações quando não está em modo administrador.
        if(!$admin)
        {
            // Contas do tipo administrador não podem alterar seu endereço de e-mail 
            // Por vias normais, somente via painel adminsitrativo.
            if(BRACP_ALLOW_ADMIN && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
                return 1;

            // Se está realizando a alteração de e-mail sem o modo administrador
            if(BRACP_CHANGE_MAIL_DELAY > 0)
            {
                // Obtém todas as ultimas alterações de e-mail
                // dentro do periodo de delay configurado.
                $emailLastChange = $this->getApp()
                                        ->getCpEm()
                                        ->createQuery('
                                            SELECT
                                                log
                                            FROM
                                                Model\EmailLog log
                                            WHERE
                                                log.account_id = :account_id AND
                                                log.date >= :DELAYDATETIME
                                        ')
                                        ->setParameter('account_id', $account->getAccount_id())
                                        ->setParameter('DELAYDATETIME',
                                            date('Y-m-d H:i:s', time() - (BRACP_CHANGE_MAIL_DELAY*60)))
                                        ->getResult();

                // Caso possua alterações de e-mail dentro do periodo informado
                // Não permite que a alteração continue e bloqueia a alteração
                // Do endereço de e-mail.
                if(count($emailLastChange) > 0)
                    return 5;
            }

            // Verifica se é possível cadastrar somente um endereço de e-mail
            // Por conta.
            if(BRACP_MAIL_REGISTER_ONCE)
            {
                $lstAccount = $this->getApp()
                                    ->getSvrDftEm()
                                    ->getRepository('Model\Login')
                                    ->findOneBy(['email' => $new_email]);
                
                // Se o endereço de e-mail já estiver registrado
                // Bloqueia a alteração atual de e-mail.
                if(!is_null($lstAccount))
                    return 7;
            }
        }

        $account->setEmail($new_email);
 
        // Realiza a alteração do e-mail na conta do jogador.
        $this->getApp()->getSvrDftEm()->merge($account);
        $this->getApp()->getSvrDftEm()->flush();

        // Grava o log de alterações de e-mail.
        $log = new EmailLog;
        $log->setAccount_id($account->getAccount_id());
        $log->setFrom($old_email);
        $log->setTo($new_email);
        $log->setDate(date('Y-m-d H:i:s'));
        $this->getApp()->getCpEm()->persist($log);
        $this->getApp()->getCpEm()->flush();

        // Verifica a possibilidade de notificar o notificar
        // ambos os e-mails por conta da alteração.
        if(BRACP_ALLOW_MAIL_SEND && BRACP_NOTIFY_CHANGE_MAIL)
        {
            $this->getApp()
                ->sendMail('@CHANGEMAIL_MAIL_TITLE@',
                [$old_email, $new_email],
                'mail.change.mail', [
                    'userid'    => $account->getUserid(),
                    'mailOld'   => $old_email,
                    'mailNew'   => $new_email
                ]);
        }

        // Retornou 0, tudo certo.
        return 0;
    }

    /**
     * Rota definida para alteração de senhas.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function password_POST($get, $post, $response)
    {
        $return = ['error_state' => 0, 'success_state' => false];

        if(!$this->getApp()->testRecaptcha($post))
        {
            $return['error_state'] = 5;
        }
        else
        {
            // Tenta realizar a alteração de senha e obtém o retorno
            // Para a tentativa.
            $return['error_state'] = $this->changePass(
                Account::loggedUser()->getUserid(),
                $post['user_pass'],
                $post['user_pass_new'],
                $post['user_pass_conf'],
                false
            );
            $return['success_state'] = ($return['error_state'] == 0);

            // Erro de requisição?
            if(BRACP_RECAPTCHA_ENABLED && !$return['success_state'])
                $this->getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
        }

        // Responde com o retorno.
        return $response->withJson($return);
    }

    /**
     * Realiza a alteração de senha na conta do jogador.
     *
     * @param string $userid
     * @param string $user_pass
     * @param string $user_pass_new
     * @param string $user_pass_new_conf
     * @param bool $admin
     *
     * @return
     *      4: Falha na restrição de pattern
     */
    private function changePass($userid, $user_pass, $user_pass_new, $user_pass_new_conf, $admin = false)
    {
        // Se o modo administrador for enviado mas o brACP
        // Estiver configurado para não poder usar modo administrador, então, desliga o parametro.
        if($admin && !BRACP_ALLOW_ADMIN)
            $admin = false;

        // Valida os dados contra a expressão regular.
        if(!$this->validate($userid, BRACP_REGEXP_USERNAME)
            || (!$admin && !$this->validate($user_pass, BRACP_REGEXP_PASSWORD)) // Quando for modo administrador, não é necessário
                                                                                // fazer o teste de senha anterior
            || !$this->validate($user_pass_new, BRACP_REGEXP_PASSWORD))
            return 4;

        // Modo administrador não irá verificar informações de senhas digitadas.
        if(!$admin)
        {
            // Calcula o hash de dados das informações enviadas.
            if(hash('md5', $user_pass_new) !== hash('md5', $user_pass_new_conf))
                return 2;
            
            // Verifica se a senha antiga é igual a nova.
            if(hash('md5', $user_pass) === hash('md5', $user_pass_new))
                return 3;
        }

        // Usando senha md5? Aplica para verificação correta.
        if(BRACP_MD5_PASSWORD_HASH)
            $user_pass = hash('md5', $user_pass);

        // Obtém a conta do jogador.
        $account = $this->getApp()
                        ->getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid]);
        
        // Se a conta não existe, então, retorna senha atual inválida.
        if(is_null($account) || (!$admin && $user_pass !== $account->getUser_pass()))
            return 1;

        // Verifica os dados de administrador.
        if(!BRACP_ALLOW_ADMIN_CHANGE_PASSWORD
            && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return -1;

        if(BRACP_MD5_PASSWORD_HASH)
            $user_pass_new = hash('md5', $user_pass_new);
        
        // Define a nova senha para a conta.
        $account->setUser_pass($user_pass_new);

        // Salvou as alterações no banco de dados.
        $this->getApp()->getSvrDftEm()->merge($account);
        $this->getApp()->getSvrDftEm()->flush();

        // Verifica se o painel está habilitado para notificar o usuário sobre
        // As alterações de senha em sua conta.
        if(BRACP_ALLOW_MAIL_SEND && BRACP_NOTIFY_CHANGE_PASSWORD)
        {
            // Envia e-mail de notificação para o jogador.
            // Irá notificar mesmo se estiver em modo administrador.
            $this->getApp()
                    ->sendMail('@CHANGEPASS_MAIL_TITLE@',
                                [$account->getEmail()],
                                'mail.change.password', [
                                    'userid' => $account->getUserid()
                                ]);
        }

        // Ocorreu tudo com sucesso.
        return 0;
    }

    /**
     * Rota definida para realizar um novo registro de conta.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function register_POST($get, $post, $response)
    {
        // Inicializa o vetor de retorno.
        $return = ['error_state' => 0, 'success_state' => false];

        // Verifica se o recaptcha foi validado com sucesso.
        if(!$this->getApp()->testRecaptcha($post))
        {
            $return['error_state'] = 6;
        }
        else
        {
            // Obtém o retorno de informações de registro para a conta.
            $register_return = $this->register(
                $post['userid'],
                $post['user_pass'],
                $post['user_pass_conf'],
                $post['sex'],
                $post['email'],
                $post['email_conf'],
                $post['birthdate']
            );

            // Informa os dados de retorno para a
            $return = [
                'error_state'   => ($register_return != 0 ? $register_return : 0),
                'success_state' => ($register_return == 0)
            ];
        }

        return $response->withJson($return);
    }

    /**
     * Método utilizado para criar uma conta para o jogador.
     *
     * @param string $userid
     * @param string $user_pass
     * @param string $user_pass_conf
     * @param string $sex
     * @param string $email
     * @param string $email_conf
     * @param string $birthdate
     * @param int $group_id
     * @param bool $admin
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
    private function register($userid,
                            $user_pass, $user_pass_conf,
                            $sex,
                            $email, $email_conf,
                            $birthdate,
                            $group_id = 0, $admin = false)
    {
        // Se o modo administrador for enviado mas o brACP
        // Estiver configurado para não poder usar modo administrador, então, desliga o parametro.
        if($admin && !BRACP_ALLOW_ADMIN)
            $admin = false;

        // Registro de contas está desabilitado.
        // -> Somente permite nova conta em caso de ser cadastro com modo administrador.
        if(!$admin && !BRACP_ALLOW_CREATE_ACCOUNT)
            return -1;

        // Valida as expressões regulares para os campos informados no cadastro.
        if(!$this->validate($userid, BRACP_REGEXP_USERNAME)
            || !$this->validate($user_pass, BRACP_REGEXP_PASSWORD)
            || !$this->validate($email, BRACP_REGEXP_EMAIL)
            || !preg_match('/^(M|F)$/i', $sex))
            return 5;

        // Verifica se as senhas digitadas são iguais.
        if(hash('md5', $user_pass) !== hash('md5', $user_pass_conf))
            return 2;

        // Verifica se os e-mails digitados são iguais.
        if(hash('md5', $email) !== hash('md5', $email_conf))
            return 3;

        // Se não estiver em modo administrador, não pode
        // criar contas acima de lv 0.
        if(!$admin && $group_id > 0)
            return 4;

        // Inicializa a variavel da conta.
        $account = null;

        // Verifica se está configurado para não deixar contas com e-mail duplicado
        // -> Somente permite em modo administrador.
        if(!$admin && BRACP_MAIL_REGISTER_ONCE)
            $account = $this->getApp()
                            ->getSvrDftEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['email' => $email]);

        // Se ainda não encontrou uma conta, então
        // Refaz o teste procurando pelo nome de usuário
        // -> Restrição de banco, administradores não podem ultrapassar essa regra.
        if(is_null($account))
            $account = $this->getApp()
                            ->getSvrDftEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['userid' => $userid]);

        // Se a conta existir (userid ou email, caso configurado e não administrador)
        if(!is_null($account))
            return 1;

        // Está configurado para usar senhas em modo md5?
        if(BRACP_MD5_PASSWORD_HASH)
            $user_pass = hash('md5', $user_pass);

        // Realiza a instância da classe para realizar o registro no banco de dados.
        $account =  new Login;
        $account->setUserid($userid);
        $account->setUser_pass($user_pass);
        $account->setEmail($email);
        $account->setSex($sex);
        $account->setGroup_id($group_id);
        $account->setBirthdate($birthdate);
        $account->setState(0);

        // Se estiver habilitado a confirmação de contas e também estiver
        // habilitado o envio de e-mails, será utilizado o status 11 para
        // confirmação de contas.
        // -> Contas criadas em modo administrador não precisam de confirmar as contas.
        // NOTA.: NÃO USAR state=5 PARA CONTAS EM CONFIRMAÇÃO,
        //          O STATE 5 É DEFINIDO PARA USUÁRIO QUANDO FOR BANIDO COM @BLOCK
        if(!$admin && BRACP_CONFIRM_ACCOUNT && BRACP_ALLOW_MAIL_SEND)
            $account->setState(11);

        // Salva a conta de usuário no banco de dados.
        $this->getApp()->getSvrDftEm()->persist($account);
        $this->getApp()->getSvrDftEm()->flush();

        // Verifica a necessidade do envio de e-mails.
        if(BRACP_ALLOW_MAIL_SEND)
        {
            // Se estiver em modo administrador ou se estiver
            // configurado para não confirmar contas.
            if(!$admin && BRACP_CONFIRM_ACCOUNT)
            {
                // Envia a confirmação de conta para o endereço de e-mail
                // Da nova conta cadastrada.
                $this->sendConfirmationById($account->getAccount_id());
            }
            // Envia o e-mail de boas vindas.
            else if($admin || !BRACP_CONFIRM_ACCOUNT)
            {
                $this->getApp()->sendMail(
                    '@CREATE_MAIL_TITLE@',
                    [$account->getEmail()],
                    'mail.create', [
                        'userid'    => $account->getUserid()
                    ]);
            }
        }

        return 0;
    }

    /**
     * Realiza a confirmação do código recebido.
     *
     * @param string $code
     *
     * @return 
     * -1: Configuração não permite confirmação de contas.
     *  0: Código gerado/re-enviado
     *  1: Código de ativação não encontrado.
     */
    private function confirmAccountCode($code)
    {
        // Configurações desabilitadas não permitem
        // Que o código seja habilitado.
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
            return -1;

        if(!$this->validate($code, '([0-9a-f]{32})'))
            return 1;

        // Verifica se existe o código de confirmação para a conta informada
        $confirmation = $this->getApp()
                            ->getCpEm()
                            ->createQuery('
                                SELECT
                                    confirmation
                                FROM
                                    Model\Confirmation confirmation
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

        // Define o código de ativação como já utilizado.
        $confirmation->setUsed(true);
        $this->getApp()->getCpEm()->merge($confirmation);
        $this->getApp()->getCpEm()->flush();

        // Obtém a conta vinculada para realizar a ativação da conta.
        $account = $this->getApp()
                        ->getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy([
                            'account_id'    => $confirmation->getAccount_id(),
                            'state'         => 11 
                        ]);
                
        // Se a conta vinculada existir, então
        // Faz as atribuições corretas.
        if(!is_null($account))
        {
            // Remove a conta do estado de validação.
            $account->setState(0);
            $this->getApp()->getSvrDftEm()->merge($account);
            $this->getApp()->getSvrDftEm()->flush();

            // Envia um e-mail para o usuário informando que a conta foi ativada
            //  com sucesso.
            $this->getApp()->sendMail('@RESEND_MAIL_TITLE.CONFIRMED@',
                [$account->getEmail()],
                'mail.create.code.success',
                [
                    'userid' => $account->getUserid()
                ]);
        }

        return 0;
    }

    /**
     * Envia a confirmação de usuário.
     *
     * @param string $userid
     * @param string $email
     *
     * @return int
     * -1: Configuração não permite confirmação de contas.
     *  0: Código gerado/re-enviado
     *  1: Conta informada não espera confirmação.
     */ 
    private function sendConfirmationByUser($userid, $email)
    {
        // Se não for permitido enviar e-mails ou a configuração
        // Estiver desabilitando a confirmação de contas.
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
            return -1;
        
        // Valida por expressões regulares os dados enviados
        if(!$this->validate($userid, BRACP_REGEXP_USERNAME) ||
           !$this->validate($email, BRACP_REGEXP_EMAIL))
           return 1;
        
        // Tenta obter a conta do jogador da tabela.
        $account = $this->getApp()
                        ->getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy([
                            'userid'    => $userid,
                            'email'     => $email,
                            'state'     => 11,
                        ]);

        // Conta não encontrada para envio do código.
        if(is_null($account))
            return 1;

        // Re-envia o código de confirmação para o jogador.
        return $this->sendConfirmationById($account->getAccount_id());
    }

    /**
     * Envia a confirmação de usuário por código da conta.
     *
     * @param string $account_id
     *
     * @return int
     * -1: Configuração não permite confirmação de contas.
     *  0: Código gerado/re-enviado
     *  1: Conta informada não espera confirmação.
     */
    private function sendConfirmationById($account_id)
    {
        // Se não for permitido enviar e-mails ou a configuração
        // Estiver desabilitando a confirmação de contas.
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
            return -1;

        // Obtém a conta que será enviado o código de confirmação.
        $account = $this->getApp()
                        ->getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['account_id' => $account_id, 'state' => 11]);

        // Conta inexistente ou já confirmada.
        if(is_null($account))
            return 1;

        // Obtém ultimo código de confirmação para a conta.
        // -> Desnecessário ficar gerando um novo código toda hora... certo?
        $confirmation = $this->getApp()
                            ->getCpEm()
                            ->createQuery('
                                SELECT
                                    confirmation
                                FROM
                                    Model\Confirmation confirmation
                                WHERE
                                    confirmation.account_id = :account_id AND
                                    confirmation.used = false AND
                                    :CURDATETIME BETWEEN confirmation.date AND confirmation.expire
                           ')
                            ->setParameter('account_id', $account->getAccount_id())
                            ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                            ->getOneOrNullResult();

        // Se não houver código de confirmação da conta já criado/válido
        // Então, recria o código de confirmação.
        if(is_null($confirmation))
        {
            $confirmation = new Confirmation;
            $confirmation->setAccount_id($account->getAccount_id());
            $confirmation->setCode(hash('md5', uniqid(rand() . microtime(true), true)));
            $confirmation->setDate(date('Y-m-d H:i:s'));
            $confirmation->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
            $confirmation->setUsed(false);

            $this->getApp()->getCpEm()->persist($confirmation);
            $this->getApp()->getCpEm()->flush();
        }

        // Envia o e-mail para o jogador com o código de confirmação e o link
        // Para confirmação da conta.
        $this->getApp()->sendMail('@RESEND_MAIL_TITLE.CONFIRM@',
                                    [$account->getEmail()],
                                    'mail.create.code',[
                                        'userid'    => $account->getUserid(),
                                        'code'      => $confirmation->getCode(),
                                        'expire'    => $confirmation->getExpire()
                                    ]);
        // Enviado com sucesso.
        return 0;
    }

    /**
     * Rota definida para realizar login do usuário.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function login_POST($get, $post, $response)
    {
        // Vetor para retorno.
        $return = [
            'stage'         => 0,
            'loginSuccess'  => false,
            'loginError'    => true,
        ];

        // Verifica configurações do reCaptcha para realizar o login.
        if($this->getApp()->testRecaptcha($post)
            && isset($post['userid']) && $this->validate($post['userid'], BRACP_REGEXP_USERNAME)
            && isset($post['user_pass']) && $this->validate($post['user_pass'], BRACP_REGEXP_PASSWORD))
        {
            $userid = $post['userid'];
            $user_pass = $post['user_pass'];

            if(BRACP_MD5_PASSWORD_HASH)
                $user_pass = hash('md5', $user_pass);

            try
            {
                // Procura a conta no banco de dados.
                $account = $this->getApp()
                                ->getSvrDftEm()
                                ->getRepository('Model\Login')
                                ->findOneBy([
                                    'userid'        => $userid,
                                    'user_pass'     => $user_pass,
                                    'state'         => 0,
                                ]);
            }
            catch(Exception $ex)
            {
                $account = null;
                $return = array_merge($return, [
                    'info' => $ex->getMessage(),
                ]);
            }

            // Caso a conta não seja encontrada no banco de dados.
            if(is_null($account) || $account->getSex() == 'S' // Contas do tipo servidor não podem logar.
                || (BRACP_ALLOW_LOGIN_GMLEVEL > 0 && $account->getGroup_id() < BRACP_ALLOW_LOGIN_GMLEVEL)) // Contas com lv de gm inferior configuração não podem logar
            {
                if(BRACP_RECAPTCHA_ENABLED)
                    $this->getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
            }
            else
            {
                $this->getApp()->getSession()->BRACP_ISLOGGEDIN = true;
                $this->getApp()->getSession()->BRACP_ACCOUNTID  = $account->getAccount_id();

                // Define que o login foi realizado com sucesso.
                $return['stage'] = 1;
                $return['loginSuccess'] = true;
                $return['loginError'] = false;
            }
        }

        // Dados de retorno.
        return $response->withJson($return);
    }

    /**
     * Método utilizado para realizar logout da conta.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function logout_GET($get, $post, $response)
    {
        // Apaga da sessão dados do usuário.
        unset($this->getApp()->getSession()->BRACP_ISLOGGEDIN,
                $this->getApp()->getSession()->BRACP_ACCOUNTID);

        // Exibe o form de logout.
        $this->getApp()->display('account.logout');

        return $response;
    }

    /**
     * Verifica se o usuário está logado no sistema.
     *
     * @return boolean
     */
    public static function isLoggedIn()
    {
        $app = \brACPApp::getInstance();

        return isset($app->getSession()->BRACP_ISLOGGEDIN)
                    and $app->getSession()->BRACP_ISLOGGEDIN == true;
    }

    /**
     * Obtém o usuário logado no sistema.
     *
     * @return \Model\Login
     */
    public static function loggedUser()
    {
        $app = \brACPApp::getInstance();

        // Se não possui usuário em cache, obtém o usuário do banco
        //  e atribui ao cache.
        if(is_null(self::$user))
            self::$user = $app->getSvrDftEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['account_id' => $app->getSession()->BRACP_ACCOUNTID]);
        // Retorna o usuário logado.
        return self::$user;
    }
}

