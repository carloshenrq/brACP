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
 * Classe para controlar os dados de tradução do painel de controle.
 *
 * @author CarlosHenrq
 */
class Language
{
    /**
     * Dados da tradução para o painel de controle.
     */
    private static $translation;

    /**
     * Inicializador da classe de traduções.
     *
     * @param string $lang Tradução da linguagem a ser utilizada
     */
    public static function load($lang = 'pt_BR')
    {
        self::$translation = include_once(realpath(__DIR__ . '/../lang/') . '/' .$lang.'.php');
    }

    /**
     * Realiza o parse de informações para traduzir os dados de arquivos.
     */
    public static function parse($textToTranslate)
    {
        if(preg_match_all('/##([^##]+)##/', $textToTranslate, $matches) > 0)
        {
            $index = $matches[0];
            $value = $matches[1];

            foreach($index as $i => $var)
            {
                if(preg_match('/##([^,]+),(.*)##/', $var, $matches))
                    $textToTranslate = str_replace($var, self::translateLn($matches[1], $matches[2]), $textToTranslate);
                else
                    $textToTranslate = str_replace($var, self::translate($value[$i]), $textToTranslate);
            }
        }

        // Retorna o texto de tradução para a tela.
        return $textToTranslate;
    }

    /**
     * Tradução para o texto enviado ao sistema.
     *
     * @param string $str 
     *
     * @return string
     */
    public static function translate($str)
    {
        return ((isset(self::$translation[$str])) ? self::$translation[$str] : $str);
    }

    /**
     * Tradução para quando existe indices.
     *
     * @param string $str Código da string para tradução
     * @param mixed $index Código da linha que será utilizada
     *
     * @return string
     */
    public static function translateLn($str, $index = 0)
    {
        return ((isset(self::$translation[$str])) ? self::$translation[$str][trim($index)] : $str.'_'.trim($index));
    }

    /**
     * Realiza a leitura de todas as linguagens na pasta e retorna um array contendo as informações.
     *
     * @static
     *
     * @return ArrayObject
     */
    public static function readAll()
    {
        // Obtém todos os arquivos .php presentes na pasta lang.
        //  Nota: Os arquivos devem possuir todos os atributos para serem considerados
        //        arquivos de tradução, caso contrario, falhará.
        $tmp_langs = array_filter(scandir(__DIR__ . '/../lang'), function($file) {
            return preg_match('/^([a-z]{2})_([A-Z]{2}).php$/', $file) != 0;
        });

        $langs = [];
        foreach($tmp_langs as $lang)
            $langs[] = substr($lang, 0, 5);

        return $langs;
    }
}
