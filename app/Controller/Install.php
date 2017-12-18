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

class Install extends Controller
{
	/**
	 * @see Controller::init()
	 */
	protected function init()
	{
		// Gets the install-key.php file and writes it on the memory.
		// If the file it's not present, so, throw a nice error...
		$installKey = realpath(join(DIRECTORY_SEPARATOR, [
			__DIR__,
			'..',
			'..',
			'install-key.php'
		]));

		// If there is no 'install-key.php' file, so, show the error informing
		// de user needs to run install.php to new key be generated...
		if($installKey === false)
			exit('No install key found, please run "install.php" and try again.');

		// This install controller and routes only will grant access if the
		// project is running under install mode.
		$this->applyRestrictionOnAllRoutes(function() {
			return $this->getApplication()->isInstallMode();
		});
	}

	/**
	 * This is the default route install and grant data
	 *
	 * @param object $response
	 * @param array $args
	 *
	 * @return object
	 */
	public function index_GET($response, $args)
	{
		return $response;
	}
}
