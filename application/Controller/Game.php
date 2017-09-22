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

/**
 * Classe controladora de informações de acesso ao jogo.
 */
class Game extends AppController
{
    /**
     * @see AppController::init()
     */
    protected function init()
    {
        // Todas as rotas devem ter restrição administrativa
        // E necessário usuário estár logado para executar.
        foreach($this->getAllRoutes() as $route)
        {
            $this->addRouteRestriction($route, function() {
                return Profile::isLoggedIn();
            });
        }

        // Define o repositorio de dados com informações de Game 
        $this->setRepository($this->getApp()->getEntityManager()->getRepository('Model\Game'));
    }

    /**
     * Rota inicial para o menu administrativo.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function index_GET($response, $args)
    {
        // Informa em tela os dados de perfil.
        return $this->render($response, 'bracp.game.tpl', [
        ]);
    }

    /**
     * Rota para realizar a alteração de senha da conta de usuário.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function change_password_POST($response, $args)
    {
        $data = ['error' => true];

        // Tenta realizar a alteração da senha
        $changeResult = $this->getRepository()
                                ->changePass($this->getLoggedUser(),
                                            $this->post['account_id'], $this->post['user_pass'],
                                            $this->post['new_user_pass'], $this->post['cnf_user_pass']);
        
        // Caso a senha seja alterada com sucesso, então
        // Retorna como sucesso para a tela
        if($changeResult)
            $data = ['success' => true];

        return $response->withJson($data);
    }

    /**
     * Rota para criar a conta do usuário.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function create_POST($response, $args)
    {
        // Dados de retorno
        $data = ['error' => true];

        // Cria o registro e retorna os dados de retorno.
        $createResponse = $this->getRepository()->createAccess($this->getLoggedUser(),
                            $this->post['userid'], $this->post['user_pass'],
                            $this->post['user_pass_cnf'], $this->post['sex']);
        
        
        if(!$createResponse)
        {
            $data = ['success' => true];
        }
        else
        {
            $data = [
                'error' => true,
                'message'   => $this->getRepository()->createError($createResponse)
            ];
        }

        return $response->withJson($data);
    }

    /**
     * Rota para vincular perfil e dados de acesso do jogo.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function link_POST($response, $args)
    {
        $data = ['error' => true];

        // Tenta obter os dados de login para a conta.
        $login = $this->getRepository()->verifyAccess($this->post['userid'], $this->post['user_pass']);

        // Se houver dados encontrados, tenta vincular o acesso ao perfil do jogador.
        if($login !== false && $this->getRepository()->linkAccess($this->getLoggedUser(), $login))
            $data = [
                'success'   => true,
            ];

        // Dados retornados.
        return $response->withJson($data);
    }

    /**
     * Obtém o perfil logado no sistema.
     *
     * @return Model\Profile
     */
    protected function getLoggedUser()
    {
        return Profile::getLoggedUser();
    }
}
