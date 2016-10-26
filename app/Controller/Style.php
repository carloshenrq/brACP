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

use Leafo\ScssPhp\Compiler;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Style extends Caller
{
    public function __construct(\brACPApp $app)
    {
        parent::__construct($app, [
        ]);
    }

    /**
     * Método para compilar os scss dos temas.
     */
    public function css_GET($get, $post, $response)
    {
        // Constroi o caminho correto para os arquivos do brACP serem compilados e servidos.
        $scss_path = implode(DIRECTORY_SEPARATOR, [
            realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'),
            'themes',
            $this->getApp()->getSession()->BRACP_THEME]);

        // Caminho para o arquivo ser compilado.
        $pathfile = $scss_path . DIRECTORY_SEPARATOR . $get['file'];

        $scss = new Compiler;
        $scss->addImportPath($scss_path);   // Caminho para os mixins
        echo $scss->compile(file_get_contents($pathfile)); // Arquivo a ser compilado.

        return $response
                ->withHeader('Content-Type', 'text/css');
    }
}

