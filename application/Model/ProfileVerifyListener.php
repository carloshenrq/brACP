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
class ProfileVerifyListener
{
    /**
     * @PostUpdate
     *
     * Método para enviar um e-mail confirmando os dados de verificação
     *  de perfil, caso necessário.
     */
    public function postUpdate(ProfileVerify $verify, $args)
    {
        // Se o código de verificação foi usado, então, envia um e-mail para 
        // O Usuário informando que os dados foram verificados com sucesso.
        if($verify->used)
        {
            $app = \App::getInstance();
            $app->getMailer()
                ->send('Verificação Concluída', [$verify->email => $verify->profile->name], 'mail.profile_verify.success.tpl', [
                    'verify'    => $verify
                ]);
        }
    }

    /**
     * @PostPersist
     *
     * Método para enviar os dados de verificação via e-mail ao usuário. 
     * -> Sempre iré enviar o e-mail por aqui quando os dados de perfil forem alterados.
     *
     * @param ProfileVerify $verify Dados de validação do perfil. 
     * @param object $args Dados de argumentos enviados pelo EntityManager
     */
    public function postPersist(ProfileVerify $verify, $args)
    {
        try
        {
            $app = \App::getInstance();
            // Envia o e-mail ao endereço de e-mail do perfil
            $app->getMailer()
                ->send('Verificação de E-mail', [$verify->email => $verify->profile->name], 'mail.profile_verify.tpl', [
                    'verify'    => $verify
                ]);
        }
        catch(\Exception $ex)
        {
            // @Todo: Não enviar os dados
        }
    }
}
