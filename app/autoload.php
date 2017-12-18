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
 * Class to register autoloading about this panel.
 * All of classes that already not instanted and hasnt the path found'll pass here.
 */
final class Autoload
{
    /**
     * Register the autoloading functions
     */
    public static function register()
    {
        spl_autoload_register([
            'Autoload',
            'loader'
        ], true, false);
    }

    /**
     * Load all class files
     *
     * @param string $className Class name that needs to be created
     */
    public static function loader($className)
    {
        $classFile = join(DIRECTORY_SEPARATOR, [
            __DIR__,
            $className . '.php'
        ]);
        $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $classFile);

        if(file_exists($classFile))
            require_once $classFile;
    }
}

// Register the autoload function and than continues.
Autoload::register();
