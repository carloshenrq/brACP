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
use MatthiasMullie\Minify;

use \Cache;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Asset extends Caller
{
    use \TMod;

    public function __construct(\brACPApp $app)
    {
        parent::__construct($app, [
        ]);
    }

    /**
     * Obtém a imagem de um arquivo do tema em memória.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object Response com cabeçalhos para js
     */
    private function timg_GET($get, $post, $files, $response)
    {
        // Obtém o arquivo de imagem para o item.
        $filenm = realpath(__DIR__ .'/../../themes/' . $this->getApp()->getSession()->BRACP_THEME . '/img/') . '/' . $get['file'] . '.png';

        readfile($filenm);
        return $response->withHeader('Content-Type', 'image/png');
    }

    /**
     * Obtém a imagem de um item solicitado.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object Response com cabeçalhos para js
     */
    private function job_GET($get, $post, $files, $response)
    {
        // Obtém o arquivo de imagem para o item.
        $nameid = realpath(BRACP_TEMPLATE_DIR . '..'
                . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'jobs'
                . DIRECTORY_SEPARATOR . 'images') . DIRECTORY_SEPARATOR . $get['sex'] . DIRECTORY_SEPARATOR . $get['id'] . '.gif';

        readfile($nameid);
        return $response->withHeader('Content-Type', 'image/gif');
    }

    /**
     * Obtém a imagem de um item solicitado.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object Response com cabeçalhos para js
     */
    private function monster_GET($get, $post, $files, $response)
    {
        // Obtém o arquivo de imagem para o item.
        $nameid = realpath(__DIR__ .'/../../data/monsters/') . '/' . $get['id'] . '.gif';

        readfile($nameid);
        return $response->withHeader('Content-Type', 'image/gif');
    }

    /**
     * Obtém a imagem de um item solicitado.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object Response com cabeçalhos para js
     */
    private function images_GET($get, $post, $files, $response)
    {
        // Obtém o arquivo de imagem para o item.
        $nameid = realpath(__DIR__ .'/../../data/items/images/') . '/' . $get['id'] . '.png';

        readfile($nameid);
        return $response->withHeader('Content-Type', 'image/png');
    }

    /**
     * Obtém a imagem de um item solicitado.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object Response com cabeçalhos para js
     */
    private function icon_GET($get, $post, $files, $response)
    {
        // Obtém o arquivo de imagem para o item.
        $nameid = realpath(__DIR__ .'/../../data/items/icons/') . '/' . $get['id'] . '.png';

        readfile($nameid);
        return $response->withHeader('Content-Type', 'image/png');
    }

    /**
     * Método para retornar os javascripts.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object Response com cabeçalhos para js
     */
    private function js_GET($get, $post, $files, $response)
    {
        // Nome do arquivo solicitado para retorno.
        $basefile = $get['file'];

        $app = \brACPApp::getInstance();

        // // Obtém o conteúdo para o arquivo javascript solicitado.
        $js_content = Cache::get('BRACP_JS_' . strtoupper(hash('md5', $basefile)), function() use ($basefile) {
             $app = \brACPApp::getInstance();

            // Constroi o caminho correto para os arquivos do brACP serem compilados e servidos.
            $js_path = implode(DIRECTORY_SEPARATOR, [
                realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'),
                'js']);

            // Caminho para o arquivo ser compilado.
            $pathfile = $js_path . DIRECTORY_SEPARATOR . $basefile . '.js';

            // Caminho padrão para o bracp.
            $basepath_bracp = BRACP_DIR_INSTALL_URL;

            if(!empty($basepath_bracp))
                $basepath_bracp = '/' . $basepath_bracp;

            if(substr($basepath_bracp, -1, 1) == '/')
                $basepath_bracp = substr($basepath_bracp, 0, strlen($basepath_bracp) - 1);

            if(substr($basepath_bracp, 0, 2) == '//')
                $basepath_bracp = substr($basepath_bracp, 1, strlen($basepath_bracp));

            $js_files = [$pathfile];

            // Obtém os dados de javascript.
            $js_data = [];
            foreach($js_files as $js_file)
                $js_data[] = file_get_contents($js_file);

            // Carrega e aplica os mods ao arquivo.
            $js_data = array_merge($js_data, $this->loadMods($basefile, 1));

            $js_content = implode(' ', $js_data);

            // Se não estiver em modo desenvolvedor.
            if(!BRACP_DEVELOP_MODE)
            {
                $js_minify = new Minify\JS;
                $js_minify->add($js_content);
                $js_content = $js_minify->minify();
            }

            return $js_content;
        });

        // Exibe o conteudo do javascript.
        echo $js_content;

        // Reponde a requisição com os arquivos javascript.
        return $response
                ->withHeader('Content-Type', 'application/javascript');
    }

    /**
     * Método para compilar os scss dos temas.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object Response com cabeçalhos para css
     */
    private function css_GET($get, $post, $files, $response)
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
                $basepath_bracp = BRACP_DIR_INSTALL_URL;

                if(!empty($basepath_bracp) && $basepath_bracp != '/')
                    $basepath_bracp = '/' . $basepath_bracp;

                if(substr($basepath_bracp, -1, 1) == '/')
                    $basepath_bracp = substr($basepath_bracp, 0, strlen($basepath_bracp) - 1);

                if(substr($basepath_bracp, 0, 2) == '//')
                    $basepath_bracp = substr($basepath_bracp, 1, strlen($basepath_bracp));

                $scss = new Compiler;
                $scss->setVariables([
                    'data_path'     => implode('/', [
                        $basepath_bracp,
                        'asset',
                        'icon'
                    ]),
                    'asset_path'    => implode('/', [
                        $basepath_bracp,
                        'asset',
                        'timg'
                    ]),
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

                $scss_content = [];
                foreach($scss_files as $scss_file)
                    $scss_content[] = file_get_contents($scss_file);

                $scss_content = array_merge($scss_content, $this->loadMods($basefile, 1));

                $scss_compiled = [];
                foreach($scss_content as $content)
                    $scss_compiled[] = $scss->compile($content);

                // Escreve todos os dados do scss compilado (incluindo arquivos de mod)
                return implode(' ', $scss_compiled);
            });

        echo $css_compiled;

        // Responde a requisição com os dados compilados.
        return $response
                ->withHeader('Content-Type', 'text/css');
    }
}
