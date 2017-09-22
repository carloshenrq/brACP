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
class GameRepository extends AppRepository
{
    public function createError($code)
    {
        switch($code)
        {
            case 1: return "Usuário informado já foi cadastrado.";
            case 2: return "Limite máximo de acessos já atingido.";
            case 3: return "As senhas digitadas não conferem";
            case 4: return "Gênero do acesso é inválido!";
            default: return "~~ ERRO DESCONHECIDO ~~";
        }
    }

    /**
     * Método para realizar a mudança de senha do acesso do jogo.
     *
     * @param Profile $profile 
     * @param int $account_id
     * @param string $user_pass
     * @param string $new_user_pass
     * @param string $cnf_user_pass
     *
     * @return boolean Mudança de senha.
     */
    public function changePass(Profile $profile, $account_id, $user_pass, $new_user_pass, $cnf_user_pass)
    {
        // Verifica se as senhas digitadas existem.
        if(!$this->isEquals($new_user_pass, $cnf_user_pass))
            return false;

        // Obtém o objeto de login associado com o perfil informado
        // Se o login existir e não for associado com o perfil, a mudança não ocorre.
        $login = $this->getAccountFromProfile($profile, $account_id);

        // Impossível chegar na conta informada.
        if(is_null($login))
            return false;
        
        // Aplica MD5 na senha caso necessário para realizar as verificações.
        if(BRACP_RAG_ACCOUNT_PASSWORD_HASH)
        {
            $user_pass      = hash(BRACP_RAG_ACCOUNT_PASSWORD_ALGO, $user_pass);
            $new_user_pass  = hash(BRACP_RAG_ACCOUNT_PASSWORD_ALGO, $new_user_pass);
            $cnf_user_pass  = hash(BRACP_RAG_ACCOUNT_PASSWORD_ALGO, $cnf_user_pass);
        }
        
        // Verifica se a senha do banco de dados é igual a senha informada.
        if(!$this->isEquals($user_pass, $login->user_pass))
            return false;

        // Realiza a alteração de senha do acesso do jogo.
        $login->user_pass = $new_user_pass;
        $this->getLoginEm()->merge($login);
        $this->getLoginEm()->flush();

        // Adiciona um log ao perfil do usuário
        $this->getApp()
            ->getEntityManager()
            ->getRepository('Model\Profile')
            ->addLog($profile, 'O', 'Senha de acesso ao jogo do usuário "'.$login->userid.'" foi alterado.');

        // Retorna verdadeiro indicando que foi alterado com sucesso
        return true;
    }

    /**
     * Cria o acesso para o perfil.
     *
     * @param Profile $profile
     * @param string $userid
     * @param string $user_pass
     * @param string $user_pass_cnf
     * @param string $sex
     *
     * @return int 0 em caso de sucesso.
     */
    public function createAccess(Profile $profile, $userid, $user_pass, $user_pass_cnf, $sex)
    {
        // Usuário já existe no banco de dados.
        if(!is_null($this->verifyUser($userid)))
            return 1;

        // Limite de cadastro atingido.
        if(BRACP_RAG_ACCOUNT_LIMIT > 0 && count($this->getAccountsFromProfile($profile)) >= BRACP_RAG_ACCOUNT_LIMIT)
            return 2;

        // Senha digitadas não conferem.
        if(!$this->isEquals($user_pass, $user_pass_cnf))
            return 3;

        // Gênero da conta não confere com o permitido. :) Just in-case
        if(!preg_match('/^(M|F)$/', $sex))
            return 4;

        // Adiciona o hash de senha caso necessário.
        if(BRACP_RAG_ACCOUNT_PASSWORD_HASH)
            $user_pass = hash(BRACP_RAG_ACCOUNT_PASSWORD_ALGO, $user_pass);

        // Cria o objeto de login para salvar no banco de dados.
        $login = new RAG_Login;
        $login->userid = $userid;
        $login->user_pass = $user_pass;
        $login->email = is_null($profile->email) ? '' : $profile->email;
        $login->sex = $sex;
        $login->group_id = 0;
        $login->state = 0;
        $login->lastlogin = new \DateTime;
        $login->birthdate = $profile->birthdate;
        $login->unban_time = 0;
        $login->logincount = 0;
        $login->last_ip = '?.?.?.?';
        $login->mac_address = '';
        $login->character_slots = 12;
        $login->pincode = '';
        $login->pincode_change = 0;
        $login->last_password_change = 0;

        // EntityManager do login para salvar o login no banco de dados.
        $loginEm = $this->getLoginEm();
        $loginEm->persist($login);
        $loginEm->flush();

        // Salva o login no repositório de dados
        return $this->linkAccess($profile, $login) == true ? 0 : 5;
    }

    /**
     * Tenta realizar o vinculo do acesso ao jogo com o perfil do jogador.
     *
     * @param Profile $profile
     * @param RAG_Login $login
     *
     * @return boolean Se o usuário foi vinculado com sucesso.
     */
    public function linkAccess(Profile $profile, RAG_Login $login)
    {
        // Verifica se o acesso do jogo está vinculado a outro perfil do jogo.
        if(!is_null($this->getProfile($login)))
            return false;
        
        // Verifica a quantidade de contas que o perfil pode possuir, se passar o limite
        // Impede que seja feito o vinculo.
        if(BRACP_RAG_ACCOUNT_LIMIT > 0 && count($this->getAccountsFromProfile($profile)) >= BRACP_RAG_ACCOUNT_LIMIT)
            return false;
        
        // Obtém o ID do servidor na hora de fazer a gravação
        // -- Por padrão, obtém o da session e se não for possível
        //    Obtém 1 por default.
        $serverId = 1;
        if(isset($this->getApp()->getSession()->BRACP_SERVER_SELECTED))
            $serverId = $this->getApp()->getSession()->BRACP_SERVER_SELECTED;
        
        // Caso esteja tudo correto, realiza o vinculo do perfil com o acesso informado.
        $game = new Game;
        $game->profile = $profile;
        $game->account_id = $login->account_id;
        $game->userid = $login->userid;
        $game->sex = $login->sex;
        $game->verifyDt = new \DateTime;
        $game->server = $this->_em->getRepository('Model\Server')->findOneBy(['id' => $serverId]);

        // Salva os dados no banco e retorna verdadeiro.
        $this->save($game);

        // Gera um log para o perfil informando que o acesso foi vinculado ao perfil.
        $this->getApp()
            ->getEntityManager()
            ->getRepository('Model\Profile')
            ->addLog($profile, 'O', 'Acesso do jogo "'.$login->userid.'" vinculado com sucesso.');

        return true;
    }



    /**
     * Obtém o perfil do brACP associado com o login.
     *
     * @param RAG_Login $login
     *
     * @return Profile Perfil associado ao login.
     */
    public function getProfile(RAG_Login $login)
    {
        // Obtém o perfil associado ao perfil indicado.
        $profile = $this->_em->createQuery('
            SELECT
                game, profile
            FROM
                Model\Game game
            INNER JOIN
                game.profile profile
            WHERE
                game.account_id = :account_id
        ')
        ->setParameter('account_id', $login->account_id)
        ->getOneOrNullResult();

        // Retorna o perfil associado ao login.
        return $profile;
    }

    /**
     * Verifica o usuário e senha do usuário pertencente a tabela do jogo.
     * 
     * @param string $userid Nome de usuário
     * @param string $user_pass Senha do usuário (sem md5)
     *
     * @return mixed Falso se não for encontrado usuário.
     */
    public function verifyAccess($userid, $user_pass)
    {
        // Repositório de dados de login.
        $loginRepo = $this->getLoginEm()->getRepository('Model\RAG_Login');

        // Adiciona a verificação de hash caso necessário.
        if(BRACP_RAG_ACCOUNT_PASSWORD_HASH) $user_pass = hash(BRACP_RAG_ACCOUNT_PASSWORD_ALGO, $user_pass);

        // Dados de login para o usuário informado.
        $login = $this->verifyUser($userid);
        // Se não houve usuário encontrado ou é um login do tipo servidor.
        // Contas do tipo servidor não podem ser acessadas de forma alguma.
        if(is_null($login) || $login->sex == 'S' || $login->user_pass != $user_pass)
            return false;
        
        // Retorna o objeto de acesso pois encontrou os dados de login.
        return $login;
    }

    /**
     * Verifica e retorna o objeto de login, se existir.
     *
     * @param string $userid
     *
     * @return mixed Retorn null se existir.
     */
    public function verifyUser($userid)
    {
        // Repositório de dados de login.
        $loginRepo = $this->getLoginEm()->getRepository('Model\RAG_Login');

        // Encontra um usuário pelo nome...
        return $loginRepo->findOneBy([
            'userid'    => $userid
        ]);
    }

    /**
     * Obtém o objeto de login para o código de conta informado.
     *
     * @param int $account_id Código de conta
     *
     * @return RAG_Login
     */
     public function getRAG_Login($account_id)
     {
         return $this->getLoginEm()
                     ->getRepository('Model\RAG_Login')
                     ->findOneBy([
                         'account_id'    => $account_id
                     ]);
     }
 
    /**
     * Obtém o acesso do jogo que está vinculado ao perfil.
     *
     * @param Profile $profile
     * @param int $account_id
     *
     * @return RAG_Login Null se não existir para o perfil e objeto se existir
     */
    public function getAccountFromProfile(Profile $profile, $account_id)
    {
        // Obtém o login para o código de conta informado.  
        $login = $this->getRAG_Login($account_id);

        // Se não existir, retorna null
        if(is_null($login))
            return null;

        // Verifica se o login está vinculado ao perfil informado.
        // Caso não esteja, retorna null
        if(!$this->hasAccountInProfile($login, $profile))
            return null;
        
        // Retorna o objeto de login
        return $login;
    }

    /**
     * Verifica se o perfil possui o login informado vinculado a ele mesmo.
     *
     * @param RAG_Login $login
     * @param Profile $profile
     *
     * @return boolean Verdadeiro caso esteja vinculado.
     */
    public function hasAccountInProfile(RAG_Login $login, Profile $profile)
    {
        // Obtém todas as contas vinculadas ao perfil. 
        $accounts = $this->getAccountsFromProfile($profile);

        // Se o perfil não possui contas vinculadas, desnecessário
        // fazer verificações posteriores
        if(!count($accounts))
            return false;

        // Varre os dados de contas para saber se existe o login informado
        // como vinculo.
        foreach($accounts as $account)
        {
            if($account->account_id == $login->account_id)
                return true;
        }

        // Retorna falso como padrão.
        return false;
    }

    /**
     * Obtém todos os acessos para o perfil informado.
     *
     * @param Profile $profile
     *
     * @return array
     */
    public function getAccountsFromProfile(Profile $profile)
    {
        // Obtém todos os acessos vinculados ao perfil informado.
        $accounts = $this->_em->createQuery('
            SELECT
                game, profile
            FROM
                Model\Game game
            INNER JOIN
                game.profile profile
            WHERE
                profile.id = :id
        ')
        ->setParameter('id', $profile->id)
        ->getResult();

        // Retorna os acessos para o perfil.
        return $accounts;
    }

    /**
     * Obtém o login do entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getLoginEm()
    {
        return $this->getApp()->getLoginEntityManager();
    }
}
