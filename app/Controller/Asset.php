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

class Asset extends Controller
{
    /**
     * This is the asset dir. Contains all themes data info
     * @var string
     */
    private $assetDir;

    /**
     * This is the theme-folder inside assetDir. Contains all js, css and image files for a theme.
     * @var string
     */
    private $themeDir;

    /**
     * This is the img-folder inside themeDir
     * @var string
     */
    private $imgDir;

    /**
     * This is the css-folder inside themeDir
     * @var string
     */
    private $cssDir;

    /**
     * This is the js-folder inside themeDir
     * @var string
     */
    private $jsDir;

    /**
     * @see Controller::init()
     */
    protected function init()
    {
        // Loads the asset dir. Here'll be all files and themes loaded
        $this->assetDir = realpath(join(DIRECTORY_SEPARATOR, [
            __DIR__, '..', '..', 'assets'
        ]));

        // @Todo: theme-folder with user settings, for now
        //        loads the default folder and ignores the theme info.
        $this->themeDir = $this->assetDir;

        // Indicates the img-folder from theme selected
        $this->imgDir = realpath(join(DIRECTORY_SEPARATOR, [
            $this->themeDir,
            'img'
        ]));

        // Indicates the css-folder from theme selected
        $this->cssDir = realpath(join(DIRECTORY_SEPARATOR, [
            $this->themeDir,
            'css'
        ]));

        // Indicates the css-folder from theme selected
        $this->jsDir = realpath(join(DIRECTORY_SEPARATOR, [
            $this->themeDir,
            'js'
        ]));

        // Adds the route expression that we can read all files and
        // output it when need
        $this->addRouteRegexp('/^\/asset\/js\/(.*)$/i', '/asset/js/{file}');
        $this->addRouteRegexp('/^\/asset\/scss\/(.*)$/i', '/asset/scss/{file}');
        $this->addRouteRegexp('/^\/asset\/img\/(.*)$/i', '/asset/img/{file}');

    }

    /**
     * Common route to img files.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function img_GET($response, $args)
    {
        // Gets realpath from choose file.
        $img = join(DIRECTORY_SEPARATOR, [
            $this->imgDir,
            $args['file']
        ]);

        // Obtém o conteúdo da imagem.
        $imgContent = file_get_contents($img);

        // Output the image in browser
        return $response->write($imgContent)
                        ->withHeader('Content-Type', 'image/png');
    }

    /**
     * Common route to scss/css files.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function scss_GET($response, $args)
    {
        // Gets realpath from choose file.
        $css = realpath(join(DIRECTORY_SEPARATOR, [
            $this->cssDir,
            $args['file']
        ]));

        // When the file doens't exists...
        if($css === false)
            return $response->withStatus(404);  

        // Generates the css output
        $cssContent = $this->getScssFile($css, true, [], $this->cssDir);

        // Send output scss
        return $response->write($cssContent)
                        ->withHeader('Content-Type', 'text/css');
    }

    /**
     * Common route to js files.
     *
     * @param object $response
     * @param array $args
     *
     * @return object
     */
    public function js_GET($response, $args)
    {
        // Gets realpath from choose file.
        $js = realpath(join(DIRECTORY_SEPARATOR, [
            $this->jsDir,
            $args['file']
        ]));

        // When the file doens't exists...
        if($js === false)
            return $response->withStatus(404);  

        // Gets the file from cache and apply it on the screen.
        return $response->write($this->getJsFile($js))
                        ->withHeader('Content-Type', 'application/javascript');
    }

    /**
     * Gets the SCSS file compiled and parsed
     *
     * @param string $cssFile Path to scss/css file
     * @param bool $minify Teels if the file should be minified
     * @param array $vars Vars to change in scss files
     * @param string $importPath Path for included files in .scss file
     *
     * @return string
     */
    private function getScssFile($cssFile, $minify = true, $vars = [], $importPath = __DIR__)
    {
        return $this->getFileFromCache($cssFile,
                        file_get_contents($cssFile),
                        $minify,
                        $vars,
                        $importPath);
    }

    /**
     * Gets the JS file and output it.
     *
     * @param string $jsFile
     *
     * @return string JS Content to return
     */
    private function getJsFile($jsFile)
    {
        return $this->getFileFromCache($jsFile, file_get_contents($jsFile));
    }

    /**
     * Gets the js file internal
     *
     * @param string $file
     * @param string $fileContent
     *
     * @return string
     */
    private function getFileFromCache($file, $fileContent, $minify = true, $vars = [], $importPath = __DIR__)
    {
        return $this->getAssetParser()
                           ->getSqlCache()
                           ->parseFileFromCache($file, $fileContent, $minify, $vars, $importPath);
    }

    /**
     * Gets the assetParser
     *
     * @return \CHZApp\AssetParser
     */
    private function getAssetParser()
    {
        return $this->getApplication()->getAssetParser();
    }

}

