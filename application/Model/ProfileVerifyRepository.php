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
 * Classe de repositório de dados para verificação de profiles.
 */
class ProfileVerifyRepository extends AppRepository
{
    /**
     * Verifica os dados de perfil para reenviar os dados de verificação.
     * 
     * @param Profile $profile
     *
     * @return boolean
     */
    public function verifyResend(Profile $profile)
    {
        // Obtém o códifo de ativação que será expirado...
        $verify = $this->_em->createQuery('
            SELECT
                verify, profile
            FROM
                Model\ProfileVerify verify
            INNER JOIN
                verify.profile profile
            WHERE
                verify.used = 0 AND
                verify.verifyProfile = 1 AND
                profile.id = :id
        ')
        ->setParameter('id', $profile->id)
        ->getOneOrNullResult();

        // Nenhum código foi encontrado para reenviar
        // ao usuário.
        if(is_null($verify))
            return false;

        // Obtém a data e hora atual para comparar com o código
        // De ativação.
        $now = new \DateTime();
    
        // Se a data e hora atual forem maiores que a data de expiração
        // Do código, então será gerado um novo código para envio.
        if($verify->expireDate != null
            && $verify->expireDate->format('Y-m-d H:i:s') < $now->format('Y-m-d H:i:s'))
        {
            // Deleta este código do banco de dados para não ficar reenviando
            // Toda hora.
            $this->remove($verify);

            // Manda gerar os dados de verificação novamente.
            $this->generate($profile, true, $verify->email);
            return true;
        }

        // Caso não seja código antigo, então reenvia o mesmo código para
        // O Jogador.
        // Envia o e-mail ao endereço de e-mail do perfil
        $this->getApp()
            ->getMailer()
            ->send('Verificação de E-mail', [$verify->email => $verify->profile->name], 'mail.profile_verify.tpl', [
                'verify'    => $verify
            ]);

        // Retorna verdadeiro informando que o código foi reenviado com sucesso.
        return true;
    }

    /**
     * Verifica o código de verificação enviado
     * 
     * @param string $code 
     *
     * @return boolean Verdadeiro caso seja verificado com sucesso ou falso caso não seja.
     */
    public function verifyCode($code, Profile $profile = null)
    {
        // Obtém o código de verificação. 
        $verify = $this->_em->createQuery('
            SELECT 
                verify, profile
            FROM 
                Model\ProfileVerify verify 
            INNER JOIN 
                verify.profile profile
            WHERE 
                verify.code = :code AND
                verify.used = 0 AND
                verify.expireDate >= :now
        ')->setParameter('code', $code)
        ->setParameter('now', new \DateTime())
        ->getOneOrNullResult();

        // Se o código não existir, já expirou ou tiver sido usado...
        if(is_null($verify))
            return false;

        // Se existe perfil enviado, então verifica o id do perfil. 
        if(!is_null($profile) && $verify->profile->id !== $profile->id)
            return false;

        // Testa se o código de verificação é para verificação de perfil, se for, 
        // Autoriza o perfil no cadastro.
        if($verify->verifyProfile)
        {
            $verify->profile->verified = true;
            $this->update($verify->profile);
        }
        else if(empty($verify->profile->email))
        {
            $verify->profile->email = $verify->email;
            $this->update($verify->profile);
        }

        // Define o código de verificação como utilizado e logo após a verificação
        // Ser concluida, envia um e-mail ao usuário.
        $verify->used = true;
        $verify->usedDate = new \DateTime();
        $this->update($verify->profile);

        return true;
    }

    /**
     * Método para gerar um novo código de autorização e verificação
     * do e-mail para o perfil informado.
     *
     * @param \Model\Profile $profile Perfil que será verificado.
     */
    public function generate(Profile $profile, $verifyProfile = false, $email = null)
    {
        // Cria a instância do objeto para criação do código de verificação
        // Do perfil solicitado.
        $verify = new \Model\ProfileVerify;
        $verify->profile = $profile;
        $verify->email = (is_null($email) ? $profile->email : $email);
        $verify->verifyProfile = $verifyProfile;
        $verify->used = false;
        $verify->usedDate = null;
        $verify->expireDate = null;

        // Caso exista tempo para expirar o código, então,
        if(BRACP_ACCOUNT_VERIFY_EXPIRE > 0)
            $verify->expireDate = new \DateTime(date('Y-m-d H:i:s', time() + BRACP_ACCOUNT_VERIFY_EXPIRE));

        // Realiza o calculo do código de verificação. 
        $verify->code = strtoupper(hash('md5', $profile->email . uniqid() . microtime(true)));

        // Salva o código de verificação no banco de dados.
        $this->save($verify);

        // Adiciona mensagem de log para envio do código de confirmação.
        $this->_em
            ->getRepository('Model\Profile')
            ->addLog($profile, 'O', 'Código de confirmação gerado e enviado com sucesso.');
    }

}
