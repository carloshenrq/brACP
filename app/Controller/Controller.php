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

/**
 * Main controller class for this application
 */
class Controller extends \CHZApp\Controller
{
	/**
	 * @see \CHZApp\Controller
	 */
	public function __router(ServerRequestInterface $request, ResponseInterface $response, $args)
	{
		// This override is just for installation test. If we are under install
		// And we aren't in install route... then response with install route...
		if($this->getApplication()->isInstallMode()
			&& !($this instanceof Install) )
		{
			$serverParams = $request->getServerParams();
			$requestUri = $serverParams['REQUEST_URI'];
			$requestInfo = explode('/', $requestUri);
			array_shift($requestInfo);

			// Install controller redirect pass here...
			$requestFinal = '/' . join('/',
			[
				$requestInfo[0],
				'install'
			]);

			return $response->withRedirect($requestFinal, 302);
		}

		// Continue the normal exec
		return parent::__router($request, $response, $args);
	}
}
