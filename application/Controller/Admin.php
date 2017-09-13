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
 * Classe controladora de informações de administração.
 */
class Admin extends AppController
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
                return Profile::isLoggedIn() &&
                        Profile::getLoggedUser()->privileges == 'A';
            });
        }
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
        return $response->write('oi');
    }
}
