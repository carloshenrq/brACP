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

// This program generates the install key after composer is installed.
// This won't be executed if the 'config.php' file is present!
// So, if you wanna a new key, delete 'config.php'
// -> The file install-key.php will be tested too, so, if you wanna new key, make sure to delete
//    this file.

if(file_exists('config.php') || file_exists('install-key.php'))
	exit;

// Generates the install hash
$installHash = strtoupper(hash('md5', uniqid() . microtime(true)));

// Generates the string file to install hash...
$installFile = '<?php';
$installFile .= "\n";
$installFile .= "    return '$installHash';";
$installFile .= "\n";

// Writes in the disk the key
file_put_contents('install-key.php', $installFile);
