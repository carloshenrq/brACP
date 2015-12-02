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

/**
 *
 */
class brACPSlim extends Slim\Slim
{
    public function __construct($userSettings = [])
    {
        // Initialize session for this app.
        session_cache_limiter(false);
        session_start();

        // Loads the default settings for this app.
        parent::__construct($userSettings);

        // Add the template folder to smarty.
        $this->view()->setTemplatesDirectory(BRACP_TEMPLATE_DIR);

        // Add the new middleware to run.
        $this->add(new \Slim\Middleware\ContentTypes());
        $this->add(new \brAMiddlewareRoutes());
    }
}

