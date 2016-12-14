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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait para tratamento dos mods
 */
abstract class brACPMiddleware
{

    private $app;

    public final function __construct(brACPApp $app)
    {
        $this->setApp($app);
        $this->init();
    }

    /**
     * Método para inicializar as funções do middleware
     */
    protected function init()
    {

    }

    /**
     * Chamada do middleware.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callback $next
     *
     * @return
     */
    public function __invoke($request, $response, $next)
    {
        // Chama o próximo middleware.
        return $next($request, $response);
    }

    /**
     * Método para retornar a aplicação que fez a chamada.
     *
     * @return brACPApp
     */
    public function getApp()
    {
        return $this->app;
    }
    
    /**
     * Define a aplicação que está fazendo a chamada.
     *
     * @param brACPApp $app
     *
     * @return brACPApp
     */
    public function setApp(brACPApp $app)
    {
        return $this->app = $app;
    }

}
