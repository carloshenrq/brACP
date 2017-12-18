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

// Composer 'autoload' file. This is needed to run everything correctly. if this don't exists, then the project cant go on.
$composerFile = join(DIRECTORY_SEPARATOR, [
		__DIR__,
		'vendor',
		'autoload.php'
]);

if(!file_exists($composerFile))
	exit("Can't find composer dependencies. Please, verify your instalation and try again.");

// When the file exists, include it to run properly.
require_once $composerFile;

// This is a vector with all files needing to be included, if the file can't be find, then we can't run.
$filesInclude = [

	// Main app file to be included, don't remove it!!!
	join(DIRECTORY_SEPARATOR, [
		__DIR__,
		'app',
		'autoload.php'
	]),

	// If you need more files to be loaded, please do it from here and below...

];

// Pass item a item to include and run all programs.
foreach($filesInclude as $file)
{
	if(!file_exists($file))
		exit("Configuration file needed can't be found. Please verify your install folder.");

	require_once $file;
}

// Runs the application.
$app = new App;
$app->run();
