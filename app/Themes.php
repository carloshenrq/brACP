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

use Model\Theme;

/**
 * Classe para controlar os temas do painel de controle.
 *
 * @author CarlosHenrq
 */
class Themes
{
    /**
     * Limpa a tabela de temas e cria um cache apartir dos temas que estão na pasta.
     *
     * @static
     *
     * @return
     */
    public static function cacheAll()
    {
        try
        {
        // Obtém todos os temas no banco e os remove da tabela.
        $themes = brACPApp::getInstance()->getEm()->getRepository('Model\Theme')->findAll();
        foreach($themes as $theme)
        {
            brACPApp::getInstance()->getEm()->remove($theme);
            brACPApp::getInstance()->getEm()->flush();
        }
        unset($themes, $theme);

        // Realiza a leitura da tabela do banco.
        $themes = self::readAll();
        foreach($themes as $i => $theme)
        {
            $objTheme = new Theme;
            $objTheme->setId($i+1);
            $objTheme->setName($theme->name);
            $objTheme->setVersion($theme->version);
            $objTheme->setFolder($theme->folder);
            $objTheme->setImportTime(date('Y-m-d H:i:s', time()));

            brACPApp::getInstance()->getEm()->persist($objTheme);
            brACPApp::getInstance()->getEm()->flush();
        }

        // Cria o arquivo de cache de temas.
        file_put_contents(__DIR__ . '/../theme.cache', '1');
        }
        catch(Exception $ex)
        {
            echo $ex->getMessage();
        }
        return;
    }

    /**
     * Realiza a leitura de todos os temas na pasta e retorna um array contendo as informações.
     *
     * @static
     *
     * @return ArrayObject
     */
    public static function readAll()
    {
        // Obtém todos os arquivos .json presentes na pasta themes.
        //  Nota: Os arquivos devem possuir todos os atributos para serem considerados
        //        do thema, caso contrario, falhará.
        $themeFiles = array_filter(scandir(__DIR__ . '/../themes'), function($file) {
            return preg_match('/^([a-z0-9]+).json$/i', $file) != 0;
        });

        $themes = new ArrayObject;

        // Varre os temas em busca verificando se é um arquivo válido.
        foreach($themeFiles as $theme)
        {
            $themeJson = @json_decode(file_get_contents(__DIR__ . '/../themes/'.$theme));

            if(is_null($themeJson) || !isset($themeJson->folder))
                continue;

            $themes[] = $themeJson;
        }

        return $themes;
    }
}
