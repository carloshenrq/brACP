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

class App extends \CHZApp\Application
{
	/**
	 * This var defines if we are in install mode or running mode.
	 * @var boolean
	 */
	private $installMode;

	/**
	 * Checks if this is on the install mode
	 * @return boolean
	 */
	public function isInstallMode()
	{
		return $this->installMode;
	}

	/**
	 * @see \CHZApp\Application::init()
	 */
	protected function init()
	{

		// Verify if exists the install configuration file.
		// We write on it to keep the install data safe and easly changeable.
		$config = join(DIRECTORY_SEPARATOR, [
			__DIR__, '..', 'config.php'
		]);

		// If this is not in install mode, then load all config data and
		// Apply it on us config.
		if(!($this->installMode = !file_exists($config)))
		{

		}
		else
		{
			// Sets Smarty configurations for install...
			$this->setSmartyConfigs([
        		'templateDir'	=> join(DIRECTORY_SEPARATOR, [__DIR__, 'View']),
			]);
		}

	}

	/**
	 * @see \CHZApp\Application::installSchema()
	 */
	public function installSchema($schema)
	{

	} 

}
