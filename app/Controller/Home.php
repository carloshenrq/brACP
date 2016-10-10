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
use \ServerPing;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Home extends Caller
{
    /**
     * Construtor para o 
     */
    public function __construct(\brACPApp $app)
    {
        // Controller sem restrições de chamada
        parent::__construct($app, []);
    }

    /**
     * Página principal para o brACP.
     *
     * @param array $get
     * @param array $post
     */
    public function index_GET($get, $post, $response)
    {
        $this->getApp()->display('home');
    }

    /**
     * Rota para alterar a linguagem de tradução do brACP.
     *
     * @param array $get
     * @param array $post
     */
    public function language_POST($get, $post, $response)
    {
        if(isset($post['BRACP_LANGUAGE']))
            $this->getApp()->getSession()->BRACP_LANGUAGE = $post['BRACP_LANGUAGE'];
    }

    /**
     * Action para alterar o tema do brACP.
     *
     * @param array $get
     * @param array $post
     */
    public function theme_POST($get, $post, $response)
    {
        if(isset($post['BRACP_THEME']))
            $this->getApp()->getSession()->BRACP_THEME = $post['BRACP_THEME'];
    }

    /**
     * Método para alterar o servidor de execução atual do brACP.
     *
     * @param array $get
     * @param array $post
     */
    public function server_POST($get, $post, $response)
    {
        // Realiza a alteração de tema se for necessário.
        if(isset($post['BRACP_SRV_SELECTED']))
            $this->getApp()->getSession()->BRACP_SVR_SELECTED = $post['BRACP_SRV_SELECTED'];

        // Obtém o status do servidor selecionado.
        $serverStatus = ServerPing::pingServer($this->getApp()->getSession()->BRACP_SVR_SELECTED, true);

        // Responde ao client que foi alterado com sucesso.
        $response->withJson([
            'login'         => $serverStatus->login,
            'char'          => $serverStatus->char,
            'map'           => $serverStatus->map,
            'playerCount'   => $serverStatus->playerCount,
        ]);
    }
}

