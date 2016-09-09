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
class Account
{
    use \TApplication;

    /**
     * Obtém o usuário logado no sistema.
     * @var \Model\Login
     */
    private static $user = null;

    /**
     * Método para retornar os dados de personagem.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function charReset(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos pelo post para resetar os dados.
        $data = $request->getParsedBody();
        $char_id = $data['char_id'];

        // Dados de retorno para informações de erro.
        $return = ['error_state' => 0, 'success_state' => false];

        // Tipos de reset.
        switch($args)
        {
            // Reset de local
            case 'posit': $return['error_state'] = self::charResetPosit($char_id); break;
            // Reset de visual
            case 'appear': $return['error_state'] = self::charResetAppear($char_id); break;
            // Reset de equipamento
            case 'equip': $return['error_state'] = self::charResetEquip($char_id); break;

            // ????
            default:
                break;
        }

        $return['success_state']    = $return['error_state'] == 0;

        // Responde com um objeto json informando o estado do cadastro.
        $response->withJson($return);
    }

    /**
     * Reseta os equipamentos do personagem informado. (Desequipa)
     *
     * @param int $char_id Código do personagem a ser resetado.
     * @param boolean $admin Modo adminsitrador.
     *
     * @return int
     *    -1: Reset de posição desativado.
     *     0: Reset realizado com sucesso.
     *     1: Personagem online ou não encontrado.
     */
    public static function charResetEquip($char_id, $admin = false)
    {
        // Retorna -1 caos não esteja habilitado para resetar equipamentos.
        if(!BRACP_ALLOW_RESET_EQUIP && !$admin)
            return -1;

        // Obtém o personagem a ser resetado.
        $char = self::charFetch($char_id, $admin);

        // Personagem não encontrado.
        if(is_null($char))
            return 1;

        // @Todo: Fazer reset de equipamentos.

        Cache::delete('BRACP_CHARS_' . $char->getAccount_id());

        return 0;
    }

    /**
     * Reseta a aparência do personagem informado.
     *
     * @param int $char_id Código do personagem a ser resetado.
     * @param boolean $admin Modo adminsitrador.
     *
     * @return int
     *    -1: Reset de posição desativado.
     *     0: Reset realizado com sucesso.
     *     1: Personagem online ou não encontrado.
     */
    public static function charResetAppear($char_id, $admin = false)
    {
        // Retorna -1 caos não esteja habilitado para resetar aparência.
        if(!BRACP_ALLOW_RESET_APPEAR && !$admin)
            return -1;

        // Obtém o personagem a ser resetado.
        $char = self::charFetch($char_id, $admin);

        // Personagem não encontrado.
        if(is_null($char))
            return 1;

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

        self::getSvrEm()->merge($char);
        self::getSvrEm()->flush();

        Cache::delete('BRACP_CHARS_' . $char->getAccount_id());

        return 0;
    }

    /**
     * Reseta a posição do personagem informado.
     *
     * @param int $char_id Código do personagem a ser resetado.
     * @param boolean $admin Modo adminsitrador.
     *
     * @return int
     *    -1: Reset de posição desativado.
     *     0: Reset realizado com sucesso.
     *     1: Personagem online ou não encontrado.
     */
    public static function charResetPosit($char_id, $admin = false)
    {
        // Retorna -1 caos não esteja habilitado para resetar posições.
        if(!BRACP_ALLOW_RESET_POSIT && !$admin)
            return -1;

        // Obtém o personagem a ser resetado.
        $char = self::charFetch($char_id, $admin);

        // Personagem não encontrado.
        if(is_null($char))
            return 1;

        $char->setLast_map($char->getSave_map());
        $char->setLast_x($char->getSave_x());
        $char->setLast_y($char->getSave_y());

        self::getSvrEm()->merge($char);
        self::getSvrEm()->flush();

        Cache::delete('BRACP_CHARS_' . $char->getAccount_id());

        return 0;
    }

    /**
     * Método para retornar os dados de personagem.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function chars(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        $chars = self::charAccount(self::loggedUser()->getAccount_id());

        if(!empty($args) && isset($args['type']))
        {
            $response->withJson($chars);
        }
        else
        {
            // Exibe o display para home.
            self::getApp()->display('account.chars', [
                'chars' => self::charAccount(self::loggedUser()->getAccount_id())
            ]);
        }
    }

    /**
     * Encontra todos os personagens da conta de forma detalhada.
     *
     * @param int $account_id
     * @param boolean $admin (Se for administrador, refaz o cache do usuário)
     *
     * @return array
     */
    public static function charAccount($account_id, $admin = false)
    {
        // Personagens da conta em questão.
        $chars = Account::getSvrEm()
                        ->createQuery('
                            SELECT
                                char, guild, leader
                            FROM
                                Model\Char char
                            LEFT JOIN
                                char.guild guild
                            LEFT JOIN
                                guild.character leader
                            WHERE
                                char.account_id = :account_id
                            ORDER BY
                                char.char_num ASC
                        ')
                        ->setParameter('account_id', $account_id)
                        ->getResult();

        // Retorna os dados de personagens tratados.
        return self::charsParse($chars, 1);
    }

    /**
     * Trata os dados de personagens recebidos para exibição.
     *
     * @param array $chars
     * @param int $type (0: Simples, 1: Detalhado)
     *
     * @return array
     */
    public static function charsParse($chars, $type = 0)
    {
        $tmp = [];
        foreach($chars as $char)
        {
            $tmp[] = self::charParse($char, $type);
        }
        return $tmp;
    }

    /**
     * Transforma os dados do personagem para a resposta que será dada
     *  a requisição.
     *
     * @param Model\Char $char Personagem a ser retornado.
     * @param int $type (0: Simples [nome, classe, level, zeny <BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY>, clã, online], 1: Detalhado (Simples +) [id, mapa, grupo])
     *
     */
    private static function charParse(Char $char, $type = 0)
    {
        $char_data = [
            'name'          => $char->getName(),
            'classId'       => $char->getClass(),
            'class'         => Format::job($char->getClass()),
            'base_level'    => $char->getBase_level(),
            'job_level'     => $char->getJob_level(),
            'zeny'          => (($type == 0 && !BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY) ? 0 : Format::zeny($char->getZeny())),
            'guild'         => $char->getGuild(),
            'online'        => ((BRACP_ALLOW_SHOW_CHAR_STATUS) ? $char->getOnline() : 0),
        ];

        // Se a consulta é tipo detalhada do personagem, então
        //  retorna informações de localização também.
        if($type == 1)
        {
            $char_data = array_merge($char_data, [
                'char_id'   => $char->getChar_id(),
                'num'       => $char->getChar_num(),
                'party'     => null,
                'last_map'  => $char->getLast_map(),
                'last_x'    => $char->getLast_x(),
                'last_y'    => $char->getLast_y(),
                'save_map'  => $char->getSave_map(),
                'save_x'    => $char->getSave_x(),
                'save_y'    => $char->getSave_y(),

                'stats'         => [
                    'str'   => $char->getStr(),
                    'agi'   => $char->getAgi(),
                    'vit'   => $char->getVit(),
                    'int'   => $char->getInt(),
                    'dex'   => $char->getDex(),
                    'luk'   => $char->getLuk(),
                ],
            ]);
        }

        // Retorna os dados para a requisição.
        return json_decode(Language::parse(json_encode($char_data)));
    }

    /**
     * Encontra o personagem solicitado para realizar algumas operações.
     * -> Se $admin = false, vincula o teste a conta do jogador logado.
     *
     * @param int $char_id
     * @param boolean $admin
     *
     * @return Model\Char
     */
    private static function charFetch($char_id, $admin = false)
    {
        // Parametros para realizar a busca.
        $params = ['char_id' => $char_id, 'online' => 0];

        // Se a requisição não for modo
        //  administrador então vincula a conta do jogador logado.
        if(!$admin)
            $params = array_merge($params, ['account_id' => self::loggedUser()->getAccount_id()]);

        // Encontra o personagem com os parametros informados.
        return self::getSvrEm()
                ->getRepository('Model\Char')
                ->findOneBy($params);
    }

    /**
     * Método para realizar a alteração de senha de usuários.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function email(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos pelo post para confirmação de contas.
        $data = $request->getParsedBody();

        // Dados de retorno para informações de erro.
        $return = ['error_state' => 0, 'success_state' => false];

        // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
        $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

        // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
        if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
        {
            $return['error_state'] = 8;
        }
        else
        {
            // Define informaçõs de erro. (Caso exista)
            $return['error_state']      = self::accountChangeEmail(
                self::loggedUser()->getUserid(),
                $data['email'],
                $data['email_new'],
                $data['email_conf']
            );

            // Em caso de erro, atualiza as necessidades de chamar o reCaptcha
            if($return['error_state'] != 0 && BRACP_RECAPTCHA_ENABLED)
                self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;

            $return['success_state']    = $return['error_state'] == 0;
        }

        // Responde com um objeto json informando o estado do cadastro.
        $response->withJson($return);
    }

    /**
     * Método para realizar a alteração de senha de usuários.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function password(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos pelo post para confirmação de contas.
        $data = $request->getParsedBody();

        // Dados de retorno para informações de erro.
        $return = ['error_state' => 0, 'success_state' => false];

        // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
        $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

        // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
        if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
        {
            $return['error_state'] = 5;
        }
        else
        {
            // Define informaçõs de erro. (Caso exista)
            $return['error_state']      = self::accountChangePass(
                self::loggedUser()->getUserid(),
                $data['user_pass'],
                $data['user_pass_new'],
                $data['user_pass_conf']
            );

            // Em caso de erro, atualiza as necessidades de chamar o reCaptcha
            if($return['error_state'] != 0 && BRACP_RECAPTCHA_ENABLED)
                self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
            
            // userid, $old_pass, $new_pass, $new_pass_conf
            $return['success_state']    = $return['error_state'] == 0;
        }

        // Responde com um objeto json informando o estado do cadastro.
        $response->withJson($return);
    }

    /**
     * Método para realizar a confirmação de contas recebido via post.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function recover(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos pelo post para confirmação de contas.
        $data = $request->getParsedBody();

        // Dados de retorno para informações de erro.
        $return = ['error_state' => 0, 'success_state' => false];

        // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
        $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

        // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
        if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
        {
            $return['error_state'] = 3;
        }
        else
        {
            // Se ambos estão definidos, a requisição é para re-envio dos dados de confirmação.
            if(isset($data['userid']) && isset($data['email']))
                $return['error_state']      = self::registerRecover($data['userid'], $data['email']);
            // Se código está definido, a requisição é para confirmação da conta.
            else if(isset($data['code']))
                $return['error_state']      = self::registerRecoverCode($data['code']);

            // Em caso de erro, atualiza as necessidades de chamar o reCaptcha
            if($return['error_state'] != 0 && BRACP_RECAPTCHA_ENABLED)
                self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;

            // Define informaçõs de erro. (Caso exista)
            $return['success_state']    = $return['error_state'] == 0;
        }

        // Responde com um objeto json informando o estado do cadastro.
        $response->withJson($return);
    }

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

        // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
        $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

        // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
        if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
        {
            $return['error_state'] = 2;
        }
        else
        {
            // Se ambos estão definidos, a requisição é para re-envio dos dados de confirmação.
            if(isset($data['userid']) && isset($data['email']))
                $return['error_state']      = self::registerConfirmResend($data['userid'], $data['email']);
            // Se código está definido, a requisição é para confirmação da conta.
            else if(isset($data['code']))
                $return['error_state']      = self::registerConfirmCode($data['code']);

            // Em caso de erro, atualiza as necessidades de chamar o reCaptcha
            if($return['error_state'] != 0 && BRACP_RECAPTCHA_ENABLED)
                self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;

            // Define informaçõs de erro. (Caso exista)
            $return['success_state']    = $return['error_state'] == 0;
        }

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

        // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
        $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

        // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
        if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
        {
            $return['error_state'] = 6;
        }
        else
        {
            // Executa a tentativa de criar a conta do usuário no banco de dados.
            $i_create = self::registerAccount(
                $data['userid'], $data['user_pass'] , $data['user_pass_conf'],
                $data['email'] , $data['email_conf'], $data['sex'],
                false, 0
            );

            // Em caso de erro, atualiza as necessidades de chamar o reCaptcha
            if($i_create != 0 && BRACP_RECAPTCHA_ENABLED)
                self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;

            // Realiza os testes para saber o retorno do registro.
            if($i_create != 0)
                $return['error_state']      = $i_create;
            else
                $return['success_state']    = true;
        }

        // Responde com um objeto json informando o estado do cadastro.
        $response->withJson($return);
    }

    /**
     * Solicitação de alteração de e-mail pelo usuário.
     *
     * @param string $userid
     * @param string $email
     * @param string $email_new
     * @param string $email_conf
     *
     * @return int
     */
    public static function accountChangeEmail($userid, $email, $email_new, $email_conf)
    {
        // Falha em restrição pattern para alteração do endereço de e-mail
        if(!preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email) ||
            !preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email_new) ||
            !preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email_conf))
            return 6;

        // 3: Novos e-mails digitados não conferem.
        if(hash('md5', $email_new) !== hash('md5', $email_conf))
            return 3;

        // 4: Novo e-mail não pode ser igual ao anterior.
        if(hash('md5', $email) === hash('md5', $email_new))
            return 4;

        $account = self::getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid, 'email' => $email]);

        // 2: Conta não encontrada para alteração do endereço de e-mail.
        if(is_null($account))
            return 2;

        // Tenta realizar a alteração do endereço de e-mail.
        return self::accountSetEmail($account->getAccount_id(), $email_new);
    }

    /**
     * Define o endereço de e-mail do jogador.
     *
     * @param int $account_id
     * @param string $email
     * @param boolean $admin
     *
     * @return int
     */
    public static function accountSetEmail($account_id, $email, $admin = false)
    {
        // Somente é possível forçar uma alteração de e-mail se for realizada em
        //  modo administrador.
        if(!$admin && !BRACP_ALLOW_CHANGE_MAIL)
            return -1;

        // Falha em restrição pattern para alteração do endereço de e-mail
        if(!preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email))
            return 6;

        // Encontra a conta do usuário via código de contas.
        $account = self::getSvrDftEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['account_id' => $account_id]);

        // Conta não encontrada.
        if(is_null($account))
            return 2;

        // Somente modo administrador pode alterar e-mail de contas
        //  em modo administrador.
        if(!$admin && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return 1;

        // Verifica o delay de alteração de e-mail. (Somente administradores podem ignorar esse teste)
        if(!$admin)
        {
            // Se houver delay para alteração de endereço de e-mail
            //  então, realiza os testes de delay.
            if(BRACP_CHANGE_MAIL_DELAY > 0)
            {
                // Obtém a ultima modificação dentro do tempo de delay
                //  informado.
                $lastChange = self::getCpEm()
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

                // Verifica se existe alteração dentro do delay configurado
                //  Caso exista, não permite alteração até o fim do delay.
                if(count($lastChange) > 0)
                    return 5;
            }

            // Verifica se o e-mail já está registrado em outra conta,
            //  caso a configuração não permita essa configuração, será negada a alteração
            //  de e-mail.
            if(BRACP_MAIL_REGISTER_ONCE)
            {
                $lstAcc = self::getSvrDftEm()
                                ->getRepository('Model\Login')
                                ->findOneBy(['email' => $email]);

                // Caso encontre uma conta com esse endereço de e-mail,
                //  Retorna o status para alteração de e-mail inválida.
                if(!is_null($lstAcc))
                    return 7;
            }

            // Notifica alteração do endereço do e-mail para o e-mail novo
            //  e o e-mail antigo.
            if(BRACP_ALLOW_MAIL_SEND && BRACP_NOTIFY_CHANGE_MAIL)
            {
                self::getApp()->sendMail('@@CHANGEMAIL,MAIL(TITLE)',
                    [$account->getEmail(), $email],
                    'mail.change.mail', [
                    'userid' => $account->getUserid(),
                    'mailOld' => $account->getEmail(),
                    'mailNew' => $email,
                ]);
            }
        }

        // Grava a mudança de e-mail na tabela de logs.
        $log = new EmailLog;
        $log->setAccount_id($account->getAccount_id());
        $log->setFrom($account->getEmail());
        $log->setTo($email);
        $log->setDate(date('Y-m-d H:i:s'));

        self::getCpEm()->persist($log);
        self::getCpEm()->flush();

        // Realiza alteração do endereço de e-mail.
        $account->setEmail($email);
        self::getSvrDftEm()->merge($account);
        self::getSvrDftEm()->flush();

        return 0;

    }

    /**
     * Método para alteração de senha de um usuário utilizando senha antiga,
     *  senha nova + confirmação
     *
     * @param string $userid
     * @param string $old_pass
     * @param string $new_pass
     * @param string $new_pass_conf
     *
     * @return int
     *  -1: Administradores não podem alterar senha por aqui.
     *   1: Senha atual digitada não confere.
     *   2: Senhas digitadas não conferem.
     *   3: Nova senha não pode ser igual a anterior.
     *   4: Falha de restrição de pattern.
     */
    public static function accountChangePass($userid, $old_pass, $new_pass, $new_pass_conf)
    {
        // 4: Falha de restrição de pattern
        if(!preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $old_pass) ||
            !preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $new_pass) ||
            !preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $new_pass_conf))
            return 4;

        // 2: Senhas digitadas não são iguais.
        if(hash('md5', $new_pass) !== hash('md5', $new_pass_conf))
            return 2;

        // Se configurado para usar md5, então, aplica md5 para
        //  realizar os testes.
        if(BRACP_MD5_PASSWORD_HASH)
            $old_pass = hash('md5', $old_pass);

        $account = self::getSvrDftEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['userid' => $userid, 'user_pass' => $old_pass]);

        // Normalmente é senha incorreta para dar este status.
        // Não há problemas alterar senhas via recuperação de contas com state != 0
        // 1: Senha atual digitada não confere.
        if(is_null($account))
            return 1;

        // -1: Administradores não podem alterar senha por aqui.
        if($account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL && !BRACP_ALLOW_ADMIN_CHANGE_PASSWORD)
            return -1;

        // 3: Nova senha não pode ser igual a anterior.
        if(BRACP_MD5_PASSWORD_HASH && $old_pass === hash('md5', $new_pass) ||
            hash('md5', $old_pass) === hash('md5', $new_pass))
            return 3;

        // Realiza a alteração da senha do jogador
        //  e se configurado, notifica por e-mail (caso configuração ok)
        return self::accountSetPass($account->getAccount_id(), $new_pass);
    }

    /**
     * Aplica alteração de senhas na conta informada.
     *
     * @param int $account_id
     * @param string $password
     * @param boolean $admin (Padrão: false)
     *
     * @return int
     *     -1: Conta não encontrada/Administrador não pode alterar senha
     *      0: Senha alterada com sucesso.
     *      4: Falha de restrição de pattern
     */
    public static function accountSetPass($account_id, $password, $admin = false)
    {
        // Retorna 2 para restrição de pattern
        if(!preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $password))
            return 4;

        // Realiza a busca da conta para poder realizar a alteração de senha.
        $account = self::getSvrDftEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['account_id' => $account_id]);

        // Não permite que a senha de administradores sejam alteradas
        // se o painel de controle não permitir. (Somente em modo administrador)
        if(is_null($account) || (!$admin && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL
                    && !BRACP_ALLOW_ADMIN_CHANGE_PASSWORD))
            return -1;

        if(BRACP_MD5_PASSWORD_HASH)
            $password = hash('md5', $password);

        // Salva a nova senha aplicada.
        $account->setUser_pass($password);
        self::getSvrDftEm()->merge($account);
        self::getSvrDftEm()->flush();

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
     * Confirma o código digitado e se ainda não estiver expirado,
     *  gera a nova senha e recupera os dados de usuário.
     *
     * @param string $code
     *
     * @return int
     *      -1: Recuperação por código desativado.
     *       0: Código de recuperação enviado com sucesso.
     *       1: Código de recuperação inválido ou já utilizado.
     *       2: Falha na restrição de pattern do código.
     */
    public static function registerRecoverCode($code)
    {
        // -1: Recuperação de contas desabilitado.
        if(!BRACP_ALLOW_MAIL_SEND || !BRACP_ALLOW_RECOVER)
            return -1;

        // Código digitado não é md5
        if(!preg_match('/^([0-9a-f]{32})$/i', $code))
            return 2;

        // Verifica se o código de ativação está não utilizado
        //  ou existe ou não expirado.
        $recover = self::getCpEm()
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

        // Define que o código de recuperação foi utilizado.
        $recover->setUsed(true);
        self::getCpEm()->merge($recover);
        self::getCpEm()->flush();

        $account = self::getSvrDftEm()
                    ->getRepository('Model\Login')
                    ->findOneBy(['account_id' => $recover->getAccount_id()]);

        for($new_pass = '';
            strlen($new_pass) < BRACP_RECOVER_STRING_LENGTH;
            $new_pass .= substr(BRACP_RECOVER_RANDOM_STRING,
                            rand(0, strlen(BRACP_RECOVER_RANDOM_STRING) - 1), 1));

        // Realiza a alteração da senha do usuário.
        self::accountSetPass($account->getAccount_id(), $new_pass);

        // Envia o e-mail com a nova senha do jogador.
        self::getApp()->sendMail('@@RECOVER,MAIL(TITLE_SEND)',
            [$account->getEmail()],
            'mail.recover', [
            'userid' => $account->getUserid(),
            'password' => $new_pass,
        ]);

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
        $account = self::getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid, 'email' => $email]);

        // 1: Dados de recuperação são inválidos.
        // -> Contas do tipo administrador não podem ser recuperadas!
        if(is_null($account) || $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
            return 1;

        // Verifica se a recperação de senhas está ativa por código
        if(BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE)
        {
            // Verifica se existe o código de confirmação para a conta informada
            $recover = self::getCpEm()
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
                            ->setParameter('account_id', $account->getAccount_id())
                            ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
                            ->getOneOrNullResult();

            // Se não houver código de recuperação, então gera um novo código
            if(is_null($recover))
            {
                $recover = new Recover;
                $recover->setAccount_id($account->getAccount_id());
                $recover->setCode(hash( 'md5', uniqid(rand() . microtime(true), true)));
                $recover->setDate(date('Y-m-d H:i:s'));
                $recover->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
                $recover->setUsed(false);

                self::getCpEm()->persist($recover);
                self::getCpEm()->flush();
            }

            // Envia o e-mail com o código de recuperação do usuário.
            self::getApp()->sendMail('@@RECOVER,MAIL(TITLE_CODE)',
                [$account->getEmail()],
                'mail.recover.code', [
                'userid'    => $account->getUserid(),
                'code'      => $recover->getCode(),
                'expire'    => $recover->getExpire(),
            ]);
        }
        else
        {
            // Envia o e-mail com a senha perdida do usuário.
            self::getApp()->sendMail('@@RECOVER,MAIL(TITLE_SEND)',
                [$account->getEmail()],
                'mail.recover', [
                'userid'    => $account->getUserid(),
                'password'  => $account->getUser_pass()
            ]);
        }

        // Recuperação aconteceu com sucesso, retorna 0
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
            $account = self::getSvrDftEm()
                            ->getRepository('Model\Login')
                            ->findOneBy(['email' => $email]);

        if(is_null($account))
            $account = self::getSvrDftEm()
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
        $account->setSex($sex);
        // NOTA.: NÃO USAR state=5 PARA CONTAS EM CONFIRMAÇÃO,
        //        O STATE=5 É DEFINIDO PARA O USUÁRIO
        //        QUANDO FOR UTILIZADO O COMANDO @BLOCK POR UM GM DENTRO DO JOGO.
        // NOTA².: Modo administrador deve estar definido como falso. Se for verdadeiro, a conta não precisa ser confirmada.
        $account->setState(((!$admin && BRACP_ALLOW_MAIL_SEND && BRACP_CONFIRM_ACCOUNT) ? 11 : 0));

        self::getSvrDftEm()->persist($account);
        self::getSvrDftEm()->flush();

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
        $confirmation = self::getCpEm()
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

        // Informa que o código de ativação foi utilizado e o estado da conta
        //  passa a ser 0 (ok)
        $account = self::getSvrDftEm()
                    ->getRepository('Model\Login')
                    ->findOneBy(['account_id' => $confirmation->getAccount_id()]);
        $account->setState(0);
        $confirmation->setUsed(true);

        self::getSvrDftEm()->merge($account);
        self::getSvrDftEm()->flush();

        self::getCpEm()->merge($confirmation);
        self::getCpEm()->flush();

        // Envia um e-mail para o usuário informando que a conta foi ativada
        //  com sucesso.
        self::getApp()->sendMail('@@RESEND,MAIL(TITLE_CONFIRMED)',
                                    [$account->getEmail()],
                                    'mail.create.code.success',
                                    [
                                        'userid' => $account->getUserid()
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
        $account = self::getSvrDftEm()
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

        $account = self::getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['account_id' => $account_id, 'state' => 11]);

        // Dados não encontrados para confirmação de usuário.
        // state == 11, é uma conta aguardando confirmação.
        if(is_null($account))
            return 1;

        // Verifica se existe o código de confirmação para a conta informada
        $confirmation = self::getCpEm()
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

        // Se não houver código de confirmação com os dados informados,
        //  então cria o registro no banco de dados.
        if(is_null($confirmation))
        {
            $confirmation = new Confirmation;
            $confirmation->setAccount_id($account->getAccount_id());
            $confirmation->setCode(hash( 'md5', uniqid(rand() . microtime(true), true)));
            $confirmation->setDate(date('Y-m-d H:i:s'));
            $confirmation->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
            $confirmation->setUsed(false);

            self::getCpEm()->persist($confirmation);
            self::getCpEm()->flush();
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

        // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
        $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

        // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
        if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
        {
            $return['stage'] = 0;
            $return['loginError'] = true;
        }
        else
        {
            // Verifica os padrões para recepção dos parametros de usuário e senha verificando
            //  se os dados estão de acordo com os patterns informados.
            if(    !isset($data['userid'])
                || !isset($data['user_pass'])
                || !preg_match('/^'.BRACP_REGEXP_USERNAME.'$/', $data['userid'])
                || !preg_match('/^'.BRACP_REGEXP_PASSWORD.'$/', $data['user_pass']))
            {
                // Informa que ocorreu erro durante o retorno.
                $return['loginError'] = true;

                if(BRACP_RECAPTCHA_ENABLED)
                    self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
            }
            else
            {
                // Obtém a senha que será utilizada para realizar login.
                $user_pass = ((BRACP_MD5_PASSWORD_HASH) ? hash('md5', $data['user_pass']) : $data['user_pass']);

                // Tenta obter a conta que fará login no painel de controle.
                $account = self::getSvrDftEm()
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
                {
                    $return['loginError'] = true;

                    if(BRACP_RECAPTCHA_ENABLED)
                        self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;
                }
            }
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
            self::$user = self::getSvrDftEm()
                                ->getRepository('Model\Login')
                                ->findOneBy(['account_id' => self::getApp()->getSession()->BRACP_ACCOUNTID]);
        // Retorna o usuário logado.
        return self::$user;
    }
}

