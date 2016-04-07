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

// Verifica se o painel de controle possui o arquivo de configurações.
// @issue 5
if(!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config.php'))
{
    // Inclui o arquivo de instalação para tratar os dados do painel de controle.
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'install.php';
}
else
{
    // Verify if the dependencies from composer are installed.
    if(!is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'vendor'))
    {
        echo 'Dependencies not found. (Run \'composer install\')';
        exit;
    }

    // Carrega o arquivo de configurações para o painel de controle.
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

    date_default_timezone_set(BRACP_DEFAULT_TIMEZONE);

    // Carrega informações de autoload do composer e do app.
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'autoload.php';

    // Calls the slim
    $app = new brACPApp();
    $app->run();
}
