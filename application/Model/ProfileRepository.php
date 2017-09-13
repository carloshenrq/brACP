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
 * Classe de repositório de dados para profiles.
 */
class ProfileRepository extends AppRepository
{
    /**
     * Reporta um perfil de jogador salvando os dados para avaliação.
     *
     * @param Profile $informer
     * @param int $profileId
     * @param string $type
     * @param string $text
     *
     * @return bool Verdadeiro se reportado.
     */
    public function reportProfile(Profile $informer, $profileId, $type, $text)
    {
        // Não se pode denunciar a si mesmo...
        // Também verifica se o informante tem direito de reportar, se não houver
        // Ignora as verificações...
        if($informer->id == $profileId || !$informer->canReportProfiles)
            return false;

        // Obtém o perfil que será denunciado.
        $profile = $this->findById($profileId);

        // Verifica se o perfil existe, se não existir, retorna NULL.
        // Também verifica se há delay para lançamento da próxima denuncia.
        if(is_null($profile) || $this->checkReportDelay($informer, $profile))
            return false;

        // Inicializa a entidade de reporting
        $report = new ProfileReport;
        $report->profile = $profile;
        $report->informer = $informer;
        $report->date = new \DateTime();
        $report->type = $type;
        $report->text = $text;
        $report->staff = null;
        $report->staffReply = null;
        $report->staffReplyDate = null;
        $report->staffStatus = null;

        // Salva a entidade no banco de dados.
        $this->save($report);

        // Grava o log de desbloqueio para usuário.
        $this->addLog($informer, 'O', 'Perfil  ["' . $profile->name . '" (ID: ' . $profile->id . ')] foi denunciado com sucesso.');

        return true;
    }

    /**
     * Verifica se o informante já realizou uma denuncia contra o perfil informado
     * nas ultimas 2h, caso já tenha reportado, impede que sejam feitas novas denuncias.
     */
    private function checkReportDelay(Profile $infomer, Profile $profile)
    {
        // Obtém a data de 2 horas atrás
        $date2check = new \DateTime();
        $date2check->sub(date_interval_create_from_date_string('2 hours'));

        // Cria a query para execução dos dados.
        $count = $this->_em->createQuery('
            SELECT
                COUNT(report.id)
            FROM
                Model\ProfileReport report
            INNER JOIN
                report.profile profile
            INNER JOIN
                report.informer informer
            LEFT JOIN
                report.staff staff
            WHERE
                report.date >= :date2check AND
                profile.id = :profileId AND
                informer.id = :informerId
        ')
        ->setParameter('date2check',    $date2check)
        ->setParameter('profileId',     $profile->id)
        ->setParameter('informerId',    $infomer->id)
        ->getSingleScalarResult();

        // Se a contagem for maior que zero, então, há registros.
        return ($count > 0);
    }

    /**
     * Verifica se o perfil '$profile' foi bloqueado com sucesso
     *
     * @param Profile $profile
     * @param Profile $blocked
     *
     * @return boolean Verdadeiro se os perfils foram bloqueados.
     */
    public function isBlockedProfile(Profile $profile, Profile $blocked)
    {
        // Faz a contagem para saber se o perfil foi bloqueado.
        $count = $this->_em->createQuery('
                    SELECT
                        COUNT(block.id)
                    FROM
                        Model\ProfileBlock block
                    INNER JOIN
                        block.profile profile
                    INNER JOIN
                        block.blocked blocked
                    WHERE
                        profile.id = :id
                            AND
                        blocked.id = :blockedId
                ')->setParameter('id', $profile->id)
                ->setParameter('blockedId', $blocked->id)
                ->getSingleScalarResult();

        // Se for maior que zero, então houve bloqueio.
        return ($count > 0);
    }

    /**
     * Realiza o bloqueio de um perfil que foi solicitado para ser bloqueado
     *
     * @param Profile $profile Quem está pedindo o desbloqueio
     * @param int $blockedId Perfil que será desbloqueado para $profile
     *
     * @return boolean Verdadeiro se desbloqueado com sucesso. 
     */
    public function unblockProfileView(Profile $profile, $blockedId)
    {
        // Você não pode desbloquear a si mesmo, certo?
        if($profile->id == $blockedId)
            return false;

        // Encontra o perfil para bloqueio.
        $blocked = $this->findById($blockedId);

        // Perfil não pode ser encontrado pois o Id não existe?
        // Também verifica, em caso de existência, se o perfil não está bloqueado.
        if(is_null($blocked) || !$this->isBlockedProfile($profile, $blocked))
            return false;
        
        // Faz a contagem para saber se o perfil foi bloqueado.
        $blockedResult = $this->_em->createQuery('
                    SELECT
                        block, profile, blocked
                    FROM
                        Model\ProfileBlock block
                    INNER JOIN
                        block.profile profile
                    INNER JOIN
                        block.blocked blocked
                    WHERE
                        profile.id = :id
                            AND
                        blocked.id = :blockedId
                ')->setParameter('id', $profile->id)
                ->setParameter('blockedId', $blocked->id)
                ->getOneOrNullResult();
        
        // Remove o registro do banco de dados.
        $this->remove($blockedResult);

        // Grava o log de desbloqueio para usuário.
        $this->addLog($profile, 'O', 'Perfil desbloqueado com sucesso. "' . $blocked->name . '" (ID: ' . $blocked->id . ') ');

        return true;
    }

    /**
     * Realiza o bloqueio de um perfil que foi solicitado para ser bloqueado
     *
     * @param Profile $profile Quem está pedindo o bloqueio.
     * @param int $blockedId Perfil que será bloqueado para $profile
     *
     * @return boolean Verdadeiro se bloqueado com sucesso.
     */
    public function blockProfileView(Profile $profile, $blockedId)
    {
        // Você não pode bloquear a si mesmo, certo?
        if($profile->id == $blockedId)
            return false;

        // Encontra o perfil para bloqueio.
        $blocked = $this->findById($blockedId);

        // Perfil não pode ser encontrado pois o Id não existe?
        // Também verifica, em caso de existência, se o perfil já não está bloqueado.
        if(is_null($blocked) || $this->isBlockedProfile($profile, $blocked))
            return false;

        // Salva o bloqueio no banco de dados.
        $block = new ProfileBlock;
        $block->profile = $profile;
        $block->blocked = $blocked;
        $block->blockedDate = new \DateTime();

        $this->save($block);

        // Grava o log de bloqueio para usuário.
        $this->addLog($profile, 'O', 'Perfil bloqueado com sucesso. "' . $blocked->name . '" (ID: ' . $blocked->id . ') ');

        return true;
    }

    /**
     * Método para realizar uma alteração de senha no perfil do usuário.
     *
     * @param Profile $profile Perfil que terá sua senha alterada.
     * @param string $old Senha antiga
     * @param string $new Nova senha
     * @param string $cnf Confirmação da nova senha.
     *
     * @return int
     *      1: Sucesso
     *      0: Falha, combinação de usuário e senha incorretos.
     *     -1: Falha, sua nova senha não pode ser igual a anterior.
     *     -2: Falha, as senhas digitadas não conferem.
     *     -3: Falha na restrição de pattern
     *  
     */
    public function changePassword(Profile $profile, $old, $new, $cnf)
    {
        // Se não encontrar o perfil do usuário com as informações
        // do e-mail e senha antigos, então erro.
        if(is_null($this->findByEmail($profile->email, $old)))
            return 'Senha atual é inválida!';

        // Caso a nova senha digitada seja igual a anterior.
        if($old === $new)
            return 'Nova senha não pode ser igual a sua senha atual';

        // Caso a nova senha não seja igual a senha de confirmação.
        if($new !== $cnf)
            return 'As senhas digitadas não conferem';

        // Caso o pattern de senhas não seja compativel.
        if(!$this->verify($new, BRACP_REGEXP_PASS))
            return 'Falha na restrição de pattern.';

        // Realiza a alteração da senha do perfil de usuário.
        $profile->password = hash(BRACP_ACCOUNT_PASSWORD_HASH, $new);
        $this->update($profile);

        // Grava o log de alteração de senha.
        $this->addLog($profile, 'O', 'Senha de acesso alterada com sucesso.');

        // Retorna 1 informando que senha foi alterada com sucesso.
        return 1;
    }

    /**
     * Realiza a verificação do código de autenticação do google.
     * 
     * @param integer $profileId
     * @param string $gaCode
     *
     * @return boolean Verdadeiro se verificado com sucesso.
     */
    public function verifyGoogleAuthenticatorProfileId($profileId, $gaCode)
    {
        // Obtém o perfil no banco de dados.
        $profile = $this->findById($profileId);

        // Se o perfil não foi encontrado
        if(is_null($profile))
            return false;

        // Verifica código de atenticador do google.  
        return $this->verifyGoogleAuthenticatorProfile($profile, $gaCode);
    }

    /**
     * Verifica a autenticação do google por perfil.
     *
     * @param Profile $profile
     * @param string $gaCode
     *
     * @return boolean Verdadeiro caso autenticado.
     */
    public function verifyGoogleAuthenticatorProfile(Profile $profile, $gaCode)
    {
        // Se o perfil não aceitar autenticador, retorna falso.
        if(!$profile->gaAllowed)
            return false;

        // Compara os dados de autenticação com a chave informada pelo perfil.
        return $this->verifyGoogleAuthenticatorSecret($profile->gaSecret, $gaCode);
    }

    /**
     * Faz a verificação do código digitado com o segredo informado.
     *
     * @param string $secret Código secreto.
     * @param string $gaCode Código do autenticador.
     *
     * @return boolean Verdadeiro caso seja o código validado.
     */
    public function verifyGoogleAuthenticatorSecret($secret, $gaCode)
    {
        // Declara a instância do objeto do google.
        $ga = new \PHPGangsta_GoogleAuthenticator();

        // Calcula o código para este momento, deve ser igual ao código do dispositivo.
        $gaCodeCalc = $ga->getCode($secret, time() / 30);

        // Compara ambos os códigos, se forem identificos, a verificação foi realizada
        // com sucesso.
        return $this->isEquals($gaCode, $gaCodeCalc);
    }

    /**
     * Gera um novo código do autenticador google de 2 fatores
     * e retorna o URL de autenticação.
     *
     * @param Profile $profile
     *
     * @return mixed
     */
    public function generateGoogleAuthenticator()
    {
        // Cria a instância do google authenticator
        $ga = new \PHPGangsta_GoogleAuthenticator();

        // Gera os dados que serão usados para a validação dos códigos e
        // Vinculação a conta.
        $secret = $ga->createSecret();
        $qrCode = $ga->getQRCodeGoogleUrl(APP_GOOGLE_AUTH_NAME, $secret);

        // Retorna o código QRCode para ser exibido.
        return [
            'qrCodeUrl'     => $qrCode,
            'secret'        => preg_replace('/^([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})$/', '$1 $2 $3 $4', $secret),
            'code'          => '',
        ];
    }

    /**
     * Remove o authenticador da google do perfil do usuário.
     *
     * @param Profile $profile
     *
     * @return boolean Verdadeiro se o authenticator for removido.
     */
    public function removeGoogleAuthenticator(Profile $profile)
    {
        // Se não possuir authenticador, não faz a remoção.
        if(!$profile->gaAllowed)
            return false;
        
        // Grava a alteração no perfil solicitado.
        $profile->gaAllowed = false;
        $profile->gaSecret = null;
        $this->save($profile);

        // Adiciona um log informando que o authenticator foi removido.
        $this->addLog($profile, 'O', 'Google Authenticator foi removido com sucesso.');

        return true;
    }

    /**
     * Cria um novo log para o perfil informado. 
     * 
     * @param Profile $profile Profile a ser vinculado o log
     * @param string $type
     * @param string $ipAddress
     * @param string $message
     */
    public function addLog(Profile $profile, $type, $message, $ipAddress = null)
    {
        // Verifica se o endereço de ip foi atribuido.
        // Se nao foi, irá usar o padrão do firewall
        if(is_null($ipAddress))
            $ipAddress = $this->getApp()->getFirewall()->getIpAddress();

        $log = new \Model\ProfileLog;

        $log->profile = $profile;
        $log->type = $type;
        $log->message = $message;
        $log->dateTime = new \DateTime();
        $log->address = $ipAddress;

        $this->save($log);
    }

    /**
     * Encontra um profile pelo endereço e-mail e senha
     *
     * @param string $email Endereço e-mail a ser localizado. 
     * @param string $password Senha a ser verificada. 
     *
     * @return \Model\Profile Encontra dados de informações relacionados a usuário e senha
     */
    public function findByEmail($email, $password)
    {
        // Verifica os dados de acordo com as expressões regulares.
        if(!$this->verify($email, BRACP_REGEXP_MAIL) || !$this->verify($password, BRACP_REGEXP_PASS))
            return null;

        // Verifica se o perfil para e-mail informado existe.
        $profile = $this->findOneBy([
            'email'     => $email
        ]);

        // Se o profile não existe, retorna NULL
        if(is_null($profile))
            return null;

        // 1. Se o profile foi bloqueado temporariamente, não há necessidade
        // de se realizar login na conta.
        // **** PERFIS BLOQUEADOS DE FORMA PERMANENTE, PODEM SIM FAZER LOGIN
        // **** PORÉM, NÃO PODERÃO EXECUTAR AÇÕES DENTRO DO BRACP.
        // 2. Verifica se o endereço ip do usuário que está tentando realizar o acesso
        // Foi bloqueado por realizar acesso e recebeu bloqueio temporario.
        if((!empty($profile->blockedUntil) && intval($profile->blockedUntil) >= time()) || $this->checkBlockFromAddress())
        {
            $this->addLog($profile, 'O', 'Tentativa de acesso negado devido a bloqueio temporário.');
            return null;
        }

        // Se o profile existe, então verifica a senha da conta, se for incorreta
        // Grava um log informando que não foi possível logar com os dados. 
        if($profile->password !== hash(BRACP_ACCOUNT_PASSWORD_HASH, $password))
        {
            // Cria um log para acesso incorreto.
            $this->addLog($profile, 'W', 'Senha de acesso incorreta');

            // Verifica quantas tentativas de acesso a conta possui com senha incorreta
            // Nos últimos 5 minutos. Caso seja superior a 5, bloqueia o acesso
            // Ao perfil.
            if(BRACP_ACCOUNT_WRONGPASS_BLOCKCOUNT > 0 && BRACP_ACCOUNT_WRONGPASS_BLOCKTIME > 0)
                $this->checkWrongPassword($profile);

            return null;
        }

        // Tenta encontrar os dados no banco de dados.
        return $profile;
    }

    /**
     * Realiza verificação para saber se o endereço IP está bloqueado.
     *
     * @return boolean Verdadeiro se o acesso estiver bloqueado.
     */
    private function checkBlockFromAddress()
    {
        // Obtém o endereço ip do usuário atual para fazer a verificação.
        $ipAddress = $this->getApp()->getFirewall()->getIpAddress();

        // @Todo: Verificações de bloqueio para o endereço IP do jogador.

        return false;
    }

    /**
     * Método para verificar a quantidade de tentativas de logins com senha incorretos o usuário
     * Realizou nos últimos 5 minutos.
     *
     * @param Profile $profile
     */
    private function checkWrongPassword(Profile $profile)
    {
        // Obtém a data de 5 minutos atrás para saber se há necessidade de
        // Punir o acesso por tentativa de acesso.
        $date2check = new \DateTime();
        $date2check->sub(date_interval_create_from_date_string('5 minutes'));

        // Cria a query para execução dos dados.
        $count = $this->_em->createQuery('
            SELECT
                COUNT(log.id)
            FROM
                Model\ProfileLog log
            INNER JOIN
                log.profile profile
            WHERE
                profile.id = :id AND
                log.type = :type AND
                log.dateTime >= :date2check
        ')
        ->setParameter('id', $profile->id)
        ->setParameter('type', 'W')
        ->setParameter('date2check', $date2check)
        ->getSingleScalarResult();

        // Adiciona um novo log para bloqueio de acesso da conta do usuário.
        // Caso a contagem seja maior ou igual 5
        if($count >= BRACP_ACCOUNT_WRONGPASS_BLOCKCOUNT)
        {
            // Adiciona o timer para bloqueio do acesso.
            $profile->blockedUntil = time() + BRACP_ACCOUNT_WRONGPASS_BLOCKTIME;
            $profile->blockedReason = 'Muitas tentativas de acesso com senha incorreta.';

            // Salva as alterações no banco de dados.
            $this->update($profile);

            // Adiciona um log de bloqueio temporario por errar diversas vezes a senha.
            $this->addLog($profile, 'B', 'Bloqueado temporariamente por errar muitas vezes a senha.');
        }
    }

    /**
     * Encontra um profile com o código de acesso do token de facebook 
     *
     * @param string $accessToken
     *
     * @return \Model\Profile
     */
    public function findByFacebookToken($accessToken)
    {
        try
        {
            $id = $this->getApp()->getFacebook()->getLoginStatus($accessToken)['id'];
            return $this->findByFacebookId($id);
        }
        catch(\Exception $ex)
        {
            return null;
        }
    }

    /**
     * Encontra um profile com o código de facebook informado.
     *
     * @param string $facebookId
     *
     * @return \Model\Profile
     */
    public function findByFacebookId($facebookId)
    {
        // Verifica se o usuário foi bloqueado por acesso
        // Em seu endereço de ip, caso tenha sido,
        // Então impede de realizar acesso usando o facebook.
        if($this->checkBlockFromAddress())
            return null;

        // Encontra um registro por facebookId
        return $this->findOneBy([
            'facebookId'    => $facebookId
        ]);
    }

    /**
     * Encontra um perfil pelo id informado.
     *
     * @param int $id
     *
     * @return Profile
     */
    public function findById($id)
    {
        return $this->findOneBy([
            'id'    => $id
        ]);
    }

}
