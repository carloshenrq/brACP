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

// Defines the default exception handler for this app.
set_exception_handler(function(Exception $unhandledEx) {

    try
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir(BRACP_TEMPLATE_DIR);
        $smarty->assign('ex', $unhandledEx);
        $smarty->display('error.tpl');
    }
    catch(Exception $ex)
    {
        echo print_r($ex, true);
        exit;
    }

});

// Defines the default error handler for this app.
set_error_handler(function($errno , $errstr, $errfile, $errline, $errcontext) {
    // Throws an error exception with all especifing data from the error.
    throw new ErrorException($errstr, $errno, 1, $errfile, $errline);
}, E_ALL);

// Defines the autoloader class for this app.
spl_autoload_register(function($className) {
    // Includes the class file on the code.
    include_once (__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php');
}, true);


