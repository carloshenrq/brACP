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

use \Cache;

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
        // Obtém o nome do arquivo base que será compilado.
        $basefile = $get['file'];

        // Obtém dados do css compilado.
        $css_compiled = Cache::get('BRACP_SCSS_' . strtoupper($this->getApp()->getSession()->BRACP_THEME) . '_' . strtoupper($basefile),
            function() use ($basefile) {
                // Obtém a instância da aplicação.
                $app = \brACPApp::getInstance();

                // Constroi o caminho correto para os arquivos do brACP serem compilados e servidos.
                $scss_path = implode(DIRECTORY_SEPARATOR, [
                    realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'),
                    'themes',
                    $app->getSession()->BRACP_THEME]);

                // Caminho para o arquivo ser compilado.
                $pathfile = $scss_path . DIRECTORY_SEPARATOR . $basefile . '.scss';

                // Caminho padrão para o bracp.
                $basepath_bracp = basename(BRACP_DIR_INSTALL_URL);
                $basepath_bracp = ($basepath_bracp == '/' ? '' : '/') . $basepath_bracp;

                $scss = new Compiler;
                $scss->setVariables([
                    'data_path'     => implode('/', [
                        $basepath_bracp,
                        'data'
                    ]),
                    'theme_path'    => implode('/',[
                        $basepath_bracp,
                        'themes',
                        $app->getSession()->BRACP_THEME
                    ])
                ]);
                $scss->addImportPath($scss_path);   // Caminho para os mixins

                // Arquivos a serem compilados e retornados ao usuário.
                $scss_files = [$pathfile];

                if(BRACP_ALLOW_MODS)
                {
                    // Inicializa o loading dos mods para a linguagem em questão.
                    $scssMods = array_filter(scandir($scss_path), function($file) use ($basefile) {
                        return preg_match('/^'.$basefile.'\.([^\.]+)\.mod\.scss$/i', $file);
                    });
                    sort($scssMods);

                    // Adiciona os arquivos scss para compilação.
                    foreach($scssMods as $sccsFile)
                        $scss_files[] = $pathfile . DIRECTORY_SEPARATOR . $sccsFile;
                }

                // Obtém todos os dados para compilação do css.
                $scss_compiled = [];
                foreach($scss_files as $scss_file)
                    $scss_compiled[] = $scss->compile(file_get_contents($scss_file));

                // Escreve todos os dados do scss compilado (incluindo arquivos de mod)
                return implode(' ', $scss_compiled);
            });

        // Escreve o css_compilado.
        echo $css_compiled;

        // Responde a requisição com os dados compilados.
        return $response
                ->withHeader('Content-Type', 'text/css');
    }
}
