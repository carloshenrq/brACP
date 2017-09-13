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
 * Controlador de rotas para 'Home'. Sempre deverá existir.
 */
class Server extends AppController
{
    /**
     * @see AppController::init()
     */
    protected function init()
    {
        $this->setRepository($this->getApp()->getEntityManager()->getRepository('Model\Server'));
    }

    /**
     * Rota padrão para todos os controllers.
     *
     * @param object $response
     *
     * @return object Objeto de resposta.
     */
    public function index_GET($response, $args)
    {
        return $response->withJson($this->getRepository()->pingAll());
    }
}

