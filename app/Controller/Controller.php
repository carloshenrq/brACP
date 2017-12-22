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
	 * Defines the default route for panel.
	 * @var string
	 */
	private $defaultRoute;

	/**
	 * Gets the defaut route for panel.
	 * @var string
	 */
	public function getDefaultRoute()
	{
		return $this->defaultRoute;
	}

	/**
	 * @see \CHZApp\Controller
	 */
	public function response(ResponseInterface $response, $template, $data = [])
	{
		// Merge the default route into data array and sends it to template
		$data = array_merge($data, [
			'DEFAULT_ROUTE'	=> $this->getDefaultRoute(),
		]);

		// Parent data to send into template info
		return parent::response($response, $template, $data);
	}

	/**
	 * @see \CHZApp\Controller
	 */
	public function __router(ServerRequestInterface $request, ResponseInterface $response, $args)
	{
		// Gets className and info from this object and start to test
		// The main route about brACP.
		$classInfo = explode('\\', get_class($this));
		$className = strtolower($classInfo[1]);

		// Locate servers param to run it properly
		$serverParams = $request->getServerParams();
		$requestUri = $serverParams['REQUEST_URI'];

		// Locate the main route and fixes it.
		$this->defaultRoute = substr($requestUri, 0, strpos($requestUri, $className) - 1);

		// This override is just for installation test. If we are under install
		// And we aren't in install route... then response with install route...
		if($this->getApplication()->isInstallMode()
			&& !($this instanceof Install || $this instanceof Asset) )
			return $response->withRedirect($this->defaultRoute . '/install', 302);

		// Continue the normal exec
		return parent::__router($request, $response, $args);
	}
}
