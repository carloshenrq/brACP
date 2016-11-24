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
        parent::__construct($app, [
            // Rota de login é necessário estar deslogado para logar.
            // Caso contrario, irá retornar 404.
            'login_POST'    => function()
            {
                return !Account::isLoggedIn();
            },
            // Rota para logout, é necessário estar logado para
            //  para acessar, irá retornar 404.
            'logout_GET'    => function()
            {
                return Account::isLoggedIn();
            }
        ]);
    }

    // /**
    //  * Método para retornar os dados de personagem.
    //  *
    //  * @param ServerRequestInterface $request
    //  * @param ResponseInterface $response
    //  * @param array $args
    //  */
    // public static function charReset(ServerRequestInterface $request, ResponseInterface $response, $args)
    // {
    //     // Dados recebidos pelo post para resetar os dados.
    //     $data = $request->getParsedBody();
    //     $char_id = $data['char_id'];

    //     // Dados de retorno para informações de erro.
    //     $return = ['error_state' => 0, 'success_state' => false];

    //     // Tipos de reset.
    //     switch($args)
    //     {
    //         // Reset de local
    //         case 'posit': $return['error_state'] = self::charResetPosit($char_id); break;
    //         // Reset de visual
    //         case 'appear': $return['error_state'] = self::charResetAppear($char_id); break;
    //         // Reset de equipamento
    //         case 'equip': $return['error_state'] = self::charResetEquip($char_id); break;

    //         // ????
    //         default:
    //             break;
    //     }

    //     $return['success_state']    = $return['error_state'] == 0;

    //     // Responde com um objeto json informando o estado do cadastro.
    //     $response->withJson($return);
    // }

    // /**
    //  * Reseta os equipamentos do personagem informado. (Desequipa)
    //  *
    //  * @param int $char_id Código do personagem a ser resetado.
    //  * @param boolean $admin Modo adminsitrador.
    //  *
    //  * @return int
    //  *    -1: Reset de posição desativado.
    //  *     0: Reset realizado com sucesso.
    //  *     1: Personagem online ou não encontrado.
    //  */
    // public static function charResetEquip($char_id, $admin = false)
    // {
    //     // Retorna -1 caos não esteja habilitado para resetar equipamentos.
    //     if(!BRACP_ALLOW_RESET_EQUIP && !$admin)
    //         return -1;

    //     // Obtém o personagem a ser resetado.
    //     $char = self::charFetch($char_id, $admin);

    //     // Personagem não encontrado.
    //     if(is_null($char))
    //         return 1;

    //     // @Todo: Fazer reset de equipamentos.

    //     Cache::delete('BRACP_CHARS_' . $char->getAccount_id());

    //     return 0;
    // }

    // /**
    //  * Reseta a aparência do personagem informado.
    //  *
    //  * @param int $char_id Código do personagem a ser resetado.
    //  * @param boolean $admin Modo adminsitrador.
    //  *
    //  * @return int
    //  *    -1: Reset de posição desativado.
    //  *     0: Reset realizado com sucesso.
    //  *     1: Personagem online ou não encontrado.
    //  */
    // public static function charResetAppear($char_id, $admin = false)
    // {
    //     // Retorna -1 caos não esteja habilitado para resetar aparência.
    //     if(!BRACP_ALLOW_RESET_APPEAR && !$admin)
    //         return -1;

    //     // Obtém o personagem a ser resetado.
    //     $char = self::charFetch($char_id, $admin);

    //     // Personagem não encontrado.
    //     if(is_null($char))
    //         return 1;

    //     // Reseta aparência do personagem.
    //     $char->setHair(0);
    //     $char->setClothes_color(0);
    //     $char->setBody(0);
    //     $char->setWeapon(0);
    //     $char->setShield(0);
    //     $char->setHead_top(0);
    //     $char->setHead_mid(0);
    //     $char->setHead_bottom(0);
    //     $char->setRobe(0);

    //     self::getSvrEm()->merge($char);
    //     self::getSvrEm()->flush();

    //     Cache::delete('BRACP_CHARS_' . $char->getAccount_id());

    //     return 0;
    // }

    // /**
    //  * Reseta a posição do personagem informado.
    //  *
    //  * @param int $char_id Código do personagem a ser resetado.
    //  * @param boolean $admin Modo adminsitrador.
    //  *
    //  * @return int
    //  *    -1: Reset de posição desativado.
    //  *     0: Reset realizado com sucesso.
    //  *     1: Personagem online ou não encontrado.
    //  */
    // public static function charResetPosit($char_id, $admin = false)
    // {
    //     // Retorna -1 caos não esteja habilitado para resetar posições.
    //     if(!BRACP_ALLOW_RESET_POSIT && !$admin)
    //         return -1;

    //     // Obtém o personagem a ser resetado.
    //     $char = self::charFetch($char_id, $admin);

    //     // Personagem não encontrado.
    //     if(is_null($char))
    //         return 1;

    //     $char->setLast_map($char->getSave_map());
    //     $char->setLast_x($char->getSave_x());
    //     $char->setLast_y($char->getSave_y());

    //     self::getSvrEm()->merge($char);
    //     self::getSvrEm()->flush();

    //     Cache::delete('BRACP_CHARS_' . $char->getAccount_id());

    //     return 0;
    // }

    // /**
    //  * Método para retornar os dados de personagem.
    //  *
    //  * @param ServerRequestInterface $request
    //  * @param ResponseInterface $response
    //  * @param array $args
    //  */
    // public static function chars(ServerRequestInterface $request, ResponseInterface $response, $args)
    // {
    //     $chars = self::charAccount(self::loggedUser()->getAccount_id());

    //     if(!empty($args) && isset($args['type']))
    //     {
    //         $response->withJson($chars);
    //     }
    //     else
    //     {
    //         // Exibe o display para home.
    //         self::getApp()->display('account.chars', [
    //             'chars' => self::charAccount(self::loggedUser()->getAccount_id())
    //         ]);
    //     }
    // }

    // /**
    //  * Encontra todos os personagens da conta de forma detalhada.
    //  *
    //  * @param int $account_id
    //  * @param boolean $admin (Se for administrador, refaz o cache do usuário)
    //  *
    //  * @return array
    //  */
    // public static function charAccount($account_id, $admin = false)
    // {
    //     // Personagens da conta em questão.
    //     $chars = Account::getSvrEm()
    //                     ->createQuery('
    //                         SELECT
    //                             char, guild, leader
    //                         FROM
    //                             Model\Char char
    //                         LEFT JOIN
    //                             char.guild guild
    //                         LEFT JOIN
    //                             guild.character leader
    //                         WHERE
    //                             char.account_id = :account_id
    //                         ORDER BY
    //                             char.char_num ASC
    //                     ')
    //                     ->setParameter('account_id', $account_id)
    //                     ->getResult();

    //     // Retorna os dados de personagens tratados.
    //     return self::charsParse($chars, 1);
    // }

    // /**
    //  * Trata os dados de personagens recebidos para exibição.
    //  *
    //  * @param array $chars
    //  * @param int $type (0: Simples, 1: Detalhado)
    //  *
    //  * @return array
    //  */
    // public static function charsParse($chars, $type = 0)
    // {
    //     $tmp = [];
    //     foreach($chars as $char)
    //     {
    //         $tmp[] = self::charParse($char, $type);
    //     }
    //     return $tmp;
    // }

    // /**
    //  * Transforma os dados do personagem para a resposta que será dada
    //  *  a requisição.
    //  *
    //  * @param Model\Char $char Personagem a ser retornado.
    //  * @param int $type (0: Simples [nome, classe, level, zeny <BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY>, clã, online], 1: Detalhado (Simples +) [id, mapa, grupo])
    //  *
    //  */
    // private static function charParse(Char $char, $type = 0)
    // {
    //     $char_data = [
    //         'name'          => $char->getName(),
    //         'classId'       => $char->getClass(),
    //         'class'         => Format::job($char->getClass()),
    //         'base_level'    => $char->getBase_level(),
    //         'job_level'     => $char->getJob_level(),
    //         'zeny'          => (($type == 0 && !BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY) ? 0 : Format::zeny($char->getZeny())),
    //         'guild'         => $char->getGuild(),
    //         'online'        => ((BRACP_ALLOW_SHOW_CHAR_STATUS) ? $char->getOnline() : 0),
    //     ];

    //     // Se a consulta é tipo detalhada do personagem, então
    //     //  retorna informações de localização também.
    //     if($type == 1)
    //     {
    //         $char_data = array_merge($char_data, [
    //             'char_id'   => $char->getChar_id(),
    //             'num'       => $char->getChar_num(),
    //             'party'     => null,
    //             'last_map'  => $char->getLast_map(),
    //             'last_x'    => $char->getLast_x(),
    //             'last_y'    => $char->getLast_y(),
    //             'save_map'  => $char->getSave_map(),
    //             'save_x'    => $char->getSave_x(),
    //             'save_y'    => $char->getSave_y(),

    //             'stats'         => [
    //                 'str'   => $char->getStr(),
    //                 'agi'   => $char->getAgi(),
    //                 'vit'   => $char->getVit(),
    //                 'int'   => $char->getInt(),
    //                 'dex'   => $char->getDex(),
    //                 'luk'   => $char->getLuk(),
    //             ],
    //         ]);
    //     }

    //     // Retorna os dados para a requisição.
    //     return json_decode(Language::parse(json_encode($char_data)));
    // }

    // /**
    //  * Encontra o personagem solicitado para realizar algumas operações.
    //  * -> Se $admin = false, vincula o teste a conta do jogador logado.
    //  *
    //  * @param int $char_id
    //  * @param boolean $admin
    //  *
    //  * @return Model\Char
    //  */
    // private static function charFetch($char_id, $admin = false)
    // {
    //     // Parametros para realizar a busca.
    //     $params = ['char_id' => $char_id, 'online' => 0];

    //     // Se a requisição não for modo
    //     //  administrador então vincula a conta do jogador logado.
    //     if(!$admin)
    //         $params = array_merge($params, ['account_id' => self::loggedUser()->getAccount_id()]);

    //     // Encontra o personagem com os parametros informados.
    //     return self::getSvrEm()
    //             ->getRepository('Model\Char')
    //             ->findOneBy($params);
    // }

    // /**
    //  * Método para realizar a confirmação de contas recebido via post.
    //  *
    //  * @param ServerRequestInterface $request
    //  * @param ResponseInterface $response
    //  * @param array $args
    //  */
    // public static function recover(ServerRequestInterface $request, ResponseInterface $response, $args)
    // {
    //     // Dados recebidos pelo post para confirmação de contas.
    //     $data = $request->getParsedBody();

    //     // Dados de retorno para informações de erro.
    //     $return = ['error_state' => 0, 'success_state' => false];

    //     // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
    //     $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

    //     // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
    //     if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
    //     {
    //         $return['error_state'] = 3;
    //     }
    //     else
    //     {
    //         // Se ambos estão definidos, a requisição é para re-envio dos dados de confirmação.
    //         if(isset($data['userid']) && isset($data['email']))
    //             $return['error_state']      = self::registerRecover($data['userid'], $data['email']);
    //         // Se código está definido, a requisição é para confirmação da conta.
    //         else if(isset($data['code']))
    //             $return['error_state']      = self::registerRecoverCode($data['code']);

    //         // Em caso de erro, atualiza as necessidades de chamar o reCaptcha
    //         if($return['error_state'] != 0 && BRACP_RECAPTCHA_ENABLED)
    //             self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;

    //         // Define informaçõs de erro. (Caso exista)
    //         $return['success_state']    = $return['error_state'] == 0;
    //     }

    //     // Responde com um objeto json informando o estado do cadastro.
    //     $response->withJson($return);
    // }

    // /**
    //  * Método para realizar a confirmação de contas recebido via post.
    //  *
    //  * @param ServerRequestInterface $request
    //  * @param ResponseInterface $response
    //  * @param array $args
    //  */
    // public static function confirmation(ServerRequestInterface $request, ResponseInterface $response, $args)
    // {
    //     // Dados recebidos pelo post para confirmação de contas.
    //     $data = $request->getParsedBody();

    //     // Dados de retorno para informações de erro.
    //     $return = ['error_state' => 0, 'success_state' => false];

    //     // Obtém os dados para caso o usuário precise realizar as requisições do captcha.
    //     $needRecaptcha = self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

    //     // Adicionado teste para recaptcha para segurança das requisições enviadas ao forms.
    //     if($needRecaptcha && BRACP_RECAPTCHA_ENABLED && !self::getApp()->checkReCaptcha($data['recaptcha']))
    //     {
    //         $return['error_state'] = 2;
    //     }
    //     else
    //     {
    //         // Se ambos estão definidos, a requisição é para re-envio dos dados de confirmação.
    //         if(isset($data['userid']) && isset($data['email']))
    //             $return['error_state']      = self::registerConfirmResend($data['userid'], $data['email']);
    //         // Se código está definido, a requisição é para confirmação da conta.
    //         else if(isset($data['code']))
    //             $return['error_state']      = self::registerConfirmCode($data['code']);

    //         // Em caso de erro, atualiza as necessidades de chamar o reCaptcha
    //         if($return['error_state'] != 0 && BRACP_RECAPTCHA_ENABLED)
    //             self::getApp()->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST++;

    //         // Define informaçõs de erro. (Caso exista)
    //         $return['success_state']    = $return['error_state'] == 0;
    //     }

    //     // Responde com um objeto json informando o estado do cadastro.
    //     $response->withJson($return);
    // }

    // /**
    //  * Confirma o código digitado e se ainda não estiver expirado,
    //  *  gera a nova senha e recupera os dados de usuário.
    //  *
    //  * @param string $code
    //  *
    //  * @return int
    //  *      -1: Recuperação por código desativado.
    //  *       0: Código de recuperação enviado com sucesso.
    //  *       1: Código de recuperação inválido ou já utilizado.
    //  *       2: Falha na restrição de pattern do código.
    //  */
    // public static function registerRecoverCode($code)
    // {
    //     // -1: Recuperação de contas desabilitado.
    //     if(!BRACP_ALLOW_MAIL_SEND || !BRACP_ALLOW_RECOVER)
    //         return -1;

    //     // Código digitado não é md5
    //     if(!preg_match('/^([0-9a-f]{32})$/i', $code))
    //         return 2;

    //     // Verifica se o código de ativação está não utilizado
    //     //  ou existe ou não expirado.
    //     $recover = self::getCpEm()
    //                         ->createQuery('
    //                             SELECT
    //                                 recover
    //                             FROM
    //                                 Model\Recover recover
    //                             WHERE
    //                                 recover.code = :code AND
    //                                 recover.used = false AND
    //                                 :CURDATETIME BETWEEN recover.date AND recover.expire
    //                         ')
    //                         ->setParameter('code', $code)
    //                         ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
    //                         ->getOneOrNullResult();

    //     // Verifica se o código de recuperação é válido.
    //     if(is_null($recover))
    //         return 1;

    //     // Define que o código de recuperação foi utilizado.
    //     $recover->setUsed(true);
    //     self::getCpEm()->merge($recover);
    //     self::getCpEm()->flush();

    //     $account = self::getSvrDftEm()
    //                 ->getRepository('Model\Login')
    //                 ->findOneBy(['account_id' => $recover->getAccount_id()]);

    //     for($new_pass = '';
    //         strlen($new_pass) < BRACP_RECOVER_STRING_LENGTH;
    //         $new_pass .= substr(BRACP_RECOVER_RANDOM_STRING,
    //                         rand(0, strlen(BRACP_RECOVER_RANDOM_STRING) - 1), 1));

    //     // Realiza a alteração da senha do usuário.
    //     self::accountSetPass($account->getAccount_id(), $new_pass);

    //     // Envia o e-mail com a nova senha do jogador.
    //     self::getApp()->sendMail('@@RECOVER,MAIL(TITLE_SEND)',
    //         [$account->getEmail()],
    //         'mail.recover', [
    //         'userid' => $account->getUserid(),
    //         'password' => $new_pass,
    //     ]);

    //     return 0;
    // }

    // /**
    //  * Método utilizado para recuperar dados das contas de usuário.
    //  *
    //  * @param string $userid
    //  * @param string $email
    //  *
    //  * @return int
    //  *  -1: Recuperação de contas desabilitado.
    //  *   0: Recuperação de contas realizado com sucesso.
    //  *   1: Dados de recuperação são inválidos.
    //  *   2: Falha na restrição de pattern
    //  */
    // public static function registerRecover($userid, $email)
    // {
    //     // -1: Recuperação de contas desabilitado.
    //     if(!BRACP_ALLOW_MAIL_SEND || !BRACP_ALLOW_RECOVER)
    //         return -1;

    //     // Faz validação de pattern dos campos.
    //     if(!preg_match('/^'.BRACP_REGEXP_USERNAME.'$/', $userid) ||
    //         !preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email))
    //         return 2;

    //     // Verifica se a conta digitada existe.
    //     $account = self::getSvrDftEm()
    //                     ->getRepository('Model\Login')
    //                     ->findOneBy(['userid' => $userid, 'email' => $email]);

    //     // 1: Dados de recuperação são inválidos.
    //     // -> Contas do tipo administrador não podem ser recuperadas!
    //     if(is_null($account) || $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL)
    //         return 1;

    //     // Verifica se a recperação de senhas está ativa por código
    //     if(BRACP_MD5_PASSWORD_HASH || BRACP_RECOVER_BY_CODE)
    //     {
    //         // Verifica se existe o código de confirmação para a conta informada
    //         $recover = self::getCpEm()
    //                         ->createQuery('
    //                             SELECT
    //                                 recover
    //                             FROM
    //                                 Model\Recover recover
    //                             WHERE
    //                                 recover.account_id = :account_id AND
    //                                 recover.used = false AND
    //                                 :CURDATETIME BETWEEN recover.date AND recover.expire
    //                         ')
    //                         ->setParameter('account_id', $account->getAccount_id())
    //                         ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
    //                         ->getOneOrNullResult();

    //         // Se não houver código de recuperação, então gera um novo código
    //         if(is_null($recover))
    //         {
    //             $recover = new Recover;
    //             $recover->setAccount_id($account->getAccount_id());
    //             $recover->setCode(hash( 'md5', uniqid(rand() . microtime(true), true)));
    //             $recover->setDate(date('Y-m-d H:i:s'));
    //             $recover->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
    //             $recover->setUsed(false);

    //             self::getCpEm()->persist($recover);
    //             self::getCpEm()->flush();
    //         }

    //         // Envia o e-mail com o código de recuperação do usuário.
    //         self::getApp()->sendMail('@@RECOVER,MAIL(TITLE_CODE)',
    //             [$account->getEmail()],
    //             'mail.recover.code', [
    //             'userid'    => $account->getUserid(),
    //             'code'      => $recover->getCode(),
    //             'expire'    => $recover->getExpire(),
    //         ]);
    //     }
    //     else
    //     {
    //         // Envia o e-mail com a senha perdida do usuário.
    //         self::getApp()->sendMail('@@RECOVER,MAIL(TITLE_SEND)',
    //             [$account->getEmail()],
    //             'mail.recover', [
    //             'userid'    => $account->getUserid(),
    //             'password'  => $account->getUser_pass()
    //         ]);
    //     }

    //     // Recuperação aconteceu com sucesso, retorna 0
    //     return 0;
    // }

    // /**
    //  * Realiza a confirmação da conta do usuário com o código que o usuário digitou.
    //  *
    //  * @param string $code
    //  *
    //  * @return int
    //  * -1: Configuração não permite confirmação de contas.
    //  *  0: Código gerado/re-enviado
    //  *  1: Código de ativação não encontrado.
    //  */
    // public static function registerConfirmCode($code)
    // {
    //     if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
    //         return -1;

    //     // O Código de ativação não é valido pela formatação do md5,
    //     //  então, ignora o código e nem verifica o banco de dados.
    //     if(!preg_match('/^([0-9a-f]{32})$/', $code))
    //         return 1;

    //     // Verifica se existe o código de confirmação para a conta informada
    //     $confirmation = self::getCpEm()
    //                     ->createQuery('
    //                         SELECT
    //                             confirmation
    //                         FROM
    //                             Model\Confirmation confirmation
    //                         WHERE
    //                             confirmation.code = :code AND
    //                             confirmation.used = false AND
    //                             :CURDATETIME BETWEEN confirmation.date AND confirmation.expire
    //                     ')
    //                     ->setParameter('code', $code)
    //                     ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
    //                     ->getOneOrNullResult();

    //     // Código de ativação não encontrado ou é inválido porque expirou ou já foi utilizado.
    //     if(is_null($confirmation))
    //         return 1;

    //     // Informa que o código de ativação foi utilizado e o estado da conta
    //     //  passa a ser 0 (ok)
    //     $account = self::getSvrDftEm()
    //                 ->getRepository('Model\Login')
    //                 ->findOneBy(['account_id' => $confirmation->getAccount_id()]);
    //     $account->setState(0);
    //     $confirmation->setUsed(true);

    //     self::getSvrDftEm()->merge($account);
    //     self::getSvrDftEm()->flush();

    //     self::getCpEm()->merge($confirmation);
    //     self::getCpEm()->flush();

    //     // Envia um e-mail para o usuário informando que a conta foi ativada
    //     //  com sucesso.
    //     self::getApp()->sendMail('@@RESEND,MAIL(TITLE_CONFIRMED)',
    //                                 [$account->getEmail()],
    //                                 'mail.create.code.success',
    //                                 [
    //                                     'userid' => $account->getUserid()
    //                                 ]);

    //     return 0;
    // }

    // /**
    //  * Reenvia o código de ativação para o usuário pelas informações
    //  *  de usuário e email indicado.
    //  *
    //  * @param string $userid
    //  * @param string $email
    //  *
    //  * @return int
    //  * -1: Configuração não permite confirmação de contas.
    //  *  0: Código gerado/re-enviado
    //  *  1: Conta informada não espera confirmação.
    //  */
    // public static function registerConfirmResend($userid, $email)
    // {
    //     if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
    //         return -1;

    //     // Realiza validação servidor dos patterns de usuário e senha
    //     //  digitados.
    //     if(!preg_match('/^'.BRACP_REGEXP_USERNAME.'$/', $userid) ||
    //         !preg_match('/^'.BRACP_REGEXP_EMAIL.'$/', $email))
    //         return 1;

    //     // Obtém a conta informada.
    //     $account = self::getSvrDftEm()
    //                     ->getRepository('Model\Login')
    //                     ->findOneBy(['userid' => $userid, 'email' => $email, 'state' => 11]);

    //     // Dados não encontrados para confirmação de usuário.
    //     // state == 11, é uma conta aguardando confirmação.
    //     if(is_null($account))
    //         return 1;

    //     // Realiza o envio padrão com o código da conta informada.
    //     return self::registerConfirmSend($account->getAccount_id());
    // }

    // /**
    //  * Método para enviar o código de confirmação para a conta.
    //  * Se já existir um código de confirmação, ele será reenviado.
    //  * Se não existir, será riado um novo código e enviado ao jogador.
    //  *
    //  * -> Somente serão enviados os códigos de ativação para contas com state = 11
    //  *
    //  * @param integer $account_id
    //  *
    //  * @return int
    //  * -1: Configuração não permite confirmação de contas.
    //  *  0: Código gerado/re-enviado
    //  *  1: Conta informada não espera confirmação.
    //  */
    // public static function registerConfirmSend($account_id)
    // {
    //     if(!BRACP_ALLOW_MAIL_SEND || !BRACP_CONFIRM_ACCOUNT)
    //         return -1;

    //     $account = self::getSvrDftEm()
    //                     ->getRepository('Model\Login')
    //                     ->findOneBy(['account_id' => $account_id, 'state' => 11]);

    //     // Dados não encontrados para confirmação de usuário.
    //     // state == 11, é uma conta aguardando confirmação.
    //     if(is_null($account))
    //         return 1;

    //     // Verifica se existe o código de confirmação para a conta informada
    //     $confirmation = self::getCpEm()
    //                         ->createQuery('
    //                             SELECT
    //                                 confirmation
    //                             FROM
    //                                 Model\Confirmation confirmation
    //                             WHERE
    //                                 confirmation.account_id = :account_id AND
    //                                 confirmation.used = false AND
    //                                 :CURDATETIME BETWEEN confirmation.date AND confirmation.expire
    //                         ')
    //                         ->setParameter('account_id', $account->getAccount_id())
    //                         ->setParameter('CURDATETIME', date('Y-m-d H:i:s'))
    //                         ->getOneOrNullResult();

    //     // Se não houver código de confirmação com os dados informados,
    //     //  então cria o registro no banco de dados.
    //     if(is_null($confirmation))
    //     {
    //         $confirmation = new Confirmation;
    //         $confirmation->setAccount_id($account->getAccount_id());
    //         $confirmation->setCode(hash( 'md5', uniqid(rand() . microtime(true), true)));
    //         $confirmation->setDate(date('Y-m-d H:i:s'));
    //         $confirmation->setExpire(date('Y-m-d H:i:s', time() + (60*BRACP_RECOVER_CODE_EXPIRE)));
    //         $confirmation->setUsed(false);

    //         self::getCpEm()->persist($confirmation);
    //         self::getCpEm()->flush();
    //     }

    //     // Envia o e-mail de confirmação para o usuário com o código
    //     //  de ativação e o link para ativação dos dados.
    //     // Envia o e-mail para usuário caso o painel de controle esteja com as configurações
    //     //  de envio ativas.
    //     self::getApp()->sendMail('@@RESEND,MAIL(TITLE_CONFIRM)',
    //                                 [$account->getEmail()],
    //                                 'mail.create.code',
    //                                 [
    //                                     'userid' => $account->getUserid(),
    //                                     'code' => $confirmation->getCode(),
    //                                     'expire' => $confirmation->getExpire(),
    //                                 ]);
    //     return 0;
    // }

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
        if(!$this->validate($userid, BRACP_REGEXP_USERID) ||
            !$this->validate($old_email, BRACP_REGEXP_EMAIL) ||
            !$this->validate($new_email, BRACP_REGEXP_EMAIL))
            return 6;

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
                                                log.date >= DELAYDATETIME
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
        // Valida os dados contra a expressão regular.
        if(!$this->validate($userid, BRACP_REGEXP_USERNAME)
            || !$this->validate($user_pass, BRACP_REGEXP_PASSWORD)
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
                                    'user_pass'     => $user_pass
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
            if(is_null($account))
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

