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

namespace Model;

use \Doctrine\ORM\Mapping;

/**
 * Classe para escutar os eventos relacionados aos perfis. 
 * Alterações e etc...
 */
class ProfileListener
{
    /**
     * @PreUpdate
     *
     * Método utilizado para notificar o usuário de algumas ações realizadas em sua conta.
     *
     * ****************************************************************************************
     * ** Verificação de e-mail não vai ser tratada aqui, pois o usuário pode              * **
     * ** Realizar diversos tipos de verificação e se for por aqui, não irá                * **
     * ** Receber as notificações devidas.                                                 * **
     * ** Verificações é em: ProfileVerifyListener.php                                     * **
     * ****************************************************************************************
     */
    public function preUpdate(Profile $profile, $args)
    {
        $app = \App::getInstance();

        // Verifica se o bloqueio do usuário foi definido permanentemente ou desbloqueado.
        if($args->hasChangedField('blocked'))
        {
            // Envia o e-mail ao endereço de e-mail do perfil
            $app->getMailer()
                ->send('Aviso: Direito de Acesso',
                        [$profile->email => $profile->name],
                        'mail.profile.notify.blocked.tpl',
                        [
                            'profile'    => $profile,
                            'blocked'    => $args->getNewValue('blocked')
                        ]);
        }
        else if($args->hasChangedField('blockedUntil') && $args->getNewValue('blockedUntil') > time())
        {
            // @Todo: Notificação de que o usuário foi bloqueado temporariamente.
        }
        else if($args->hasChangedField('canCreateAccount'))
        {
            // @Todo: Notificação de que o usuário recuperou/perdeu a permissão
            //        Para criação de novas contas no jogo.
        }
        else if($args->hasChangedField('password'))
        {
            // @Todo: Notificação de que a senha foi alterada.
        }
        else if($args->hasChangedField('email'))
        {
            // @Todo: Notificação de que o endereço de e-mail foi alterado.
        }
        else if($args->hasChangedField('canReportProfiles'))
        {
            // @Todo: Notificação de que o usuário perdeu permissão de reportar perfis.
        }
    }

    /**
     * @PostPersist 
     *
     * Método para enviar os e-mails de notificação e etc... dependendo da operação realizada.
     *
     * @param Profile $profile
     * @param object $args
     */
    public function postPersist(Profile $profile, $args)
    {
        // Grava informações para log de criação da conta.
        $args->getObjectManager()
            ->getRepository('Model\Profile')
            ->addLog($profile, 'O', 'Acesso criado com sucesso');

        // Realiza notificações por e-mail relacionados a criação das contas.
        if(APP_MAILER_ALLOWED)
        {
            // Se for necessário a verificação da conta.
            if(BRACP_ACCOUNT_VERIFY && !$profile->verified)
            {
                // Gera um novo código de verificação para o perfil informado.
                $args->getObjectManager()
                    ->getRepository('Model\ProfileVerify')
                    ->generate($profile, true);
            }
            else
            {
                // @Todo: Mensagem de boas vindas.
            }
        }
    }

    /**
     * @PrePersist
     *
     * Este método realiza algumas verificações de cadastro previnindo. 
     *
     * @param Profile $profile
     * @param object $args
     */
    public function prePersist(Profile $profile, $args)
    {
        // EntityManager para realizar os dados de request ao banco de dados para
        // as verificações.
        $repo = $args->getObjectManager()->getRepository('Model\Profile');

        // Verifica o nome do perfil
        if(!$repo->verify($profile->name, BRACP_REGEXP_NAME))
            throw new \Exception('O Nome informado para o perfil está em formato incorreto.');

        // Verifica se o facebookId informado já foi cadastrado no banco de dados. 
        if(!empty($profile->facebookId))
        {
            // Verifica a existência de um perfil de facebook com os dados informados
            // Pelo usuário
            if(!is_null($repo->findByFacebookId($profile->facebookId)))
                throw new \Exception('Este facebook já está vinculado a outro perfil.');
        }
        else
        {
            // Verifica o e-mail contra o expressão regular em tela.
            if(!$repo->verify($profile->email, BRACP_REGEXP_MAIL))
                throw new \Exception('Endereço de e-mail informado está em formato inválido.');

            // Verifica a senha contra a expressão regular em tela. 
            if(!$repo->verify($profile->password, BRACP_REGEXP_PASS))
                throw new \Exception('Sua senha deve ter no mínimo 6 caracteres e possuir pelo menos: 1 Letra (inclui espaços), 1 Número e 1 Caractere Especial ($%@)');

            // Verifica o endereço de e-mail do usuário, se houver um profile com os dados já
            // Cadastrados, então impede que o endereço de e-mail seja utilizado.
            if(!is_null($repo->findOneBy(['email' => $profile->email])))
                throw new \Exception('Este endereço de e-mail já está vinculado a outro perfil.');

            // Atribui hash BRACP_ACCOUNT_PASSWORD_HASH a senha do usuário no perfil do brACP.
            $profile->password = hash(BRACP_ACCOUNT_PASSWORD_HASH, $profile->password);

            // Bloqueia a criação de contas para o perfil pois será enviado o e-mail ao 
            // Perfil informado.
            if(APP_MAILER_ALLOWED && BRACP_ACCOUNT_VERIFY)
                $profile->verified = false;
        }
    }
}
