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
     *
     */
    private $translate;

    /**
     * Construtor para a classe de linguagens.
     */
    public function __construct($lang)
    {
        $this->translate = Cache::get('BRACP_LANG_' . $lang, function() use ($lang) {
            return (include_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'lang' . DIRECTORY_SEPARATOR . $lang . '.php');
        });
    }

    /**
     * Método para tratar informações de tradução para o smarty.
     */
    public function __translate($params, $content, $template, &$repeat)
    {
        // Verifica se o string não encaixa nos dados de tradução.
        if(empty($content))
            return "";

        // Tenta realizar a tradução.
        return $this->getTranslate($content);
    }

    /**
     * 
     */
    public function getTranslate($index)
    {
        if(!preg_match('/\@([^\@]+)\@$/i', $index, $match))
            return $index;

        $indexes = explode('_', $match[1]);

        $_tmp = $this->translate;
        $_translate = $index;
        while(count($indexes) > 0)
        {
            $_curIndex = array_shift($indexes);
            
            if(!isset($_tmp[$_curIndex]))
            {
                $_tmp = null;
                break;
            }

            $_tmp = $_tmp[$_curIndex];
        }

        if(!is_null($_tmp) && !is_array($_tmp) && is_string($_tmp))
            $_translate = $_tmp;

        return $_translate;
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
        // Obtém o retorno dos linguagens.
        return Cache::get('BRACP_LANGUAGES', function() {
            // Obtém todos os arquivos .php presentes na pasta lang.
            //  Nota: Os arquivos devem possuir todos os atributos para serem considerados
            //        arquivos de tradução, caso contrario, falhará.
            $tmp_langs = array_filter(scandir(__DIR__ . '/../lang'), function($file) {
                return preg_match('/^([a-z]{2})_([A-Z]{2}).php$/', $file) != 0;
            });

            $langs = array();
            foreach($tmp_langs as $lang)
                $langs[] = substr($lang, 0, 5);

            return $langs;
        });
    }
}
