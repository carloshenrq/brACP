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
     * Linguagem que será utilizada para uso do painel.
     */
    private static $lang;

    /**
     * Inicializador da classe de traduções.
     *
     * @param string $lang Tradução da linguagem a ser utilizada
     */
    public static function load($lang = 'pt_BR')
    {
        self::$lang = $lang;

        // Obtém os dados do brACP de linguagem do cache ou da memória.
        self::$translation = Cache::get('BRACP_LANGUAGE_'.$lang, function() use ($lang) {

            $langFile = realpath(__DIR__ . '/../lang/') . '/' . $lang . '.php';
            if(!file_exists($langFile))
                $langFile = realpath(__DIR__ . '/../lang/') . '/' . BRACP_DEFAULT_LANGUAGE . '.php';

            return include_once($langFile);
        });
    }

    /**
     * Realiza o parse de informações para traduzir os dados de arquivos.
     */
    public static function parse($textToTranslate)
    {
        // Retorna de acordo com o cache do sistema.
        // return Cache::get('BRACP_LANG_' . self::$lang . '_' . hash('md5', $textToTranslate), function() use ($textToTranslate) {
            // Marca os locais que a expressão regular de tradução ocorreram.
            if(preg_match_all('/\@\@([^\(]+)\(([^\)]+)*\)/', $textToTranslate, $matches))
            {
                $origins = $matches[0];
                $indexes = $matches[1];
                $values  = $matches[2];

                $count = count($origins);

                for($i = 0; $i < $count; $i++)
                {
                    $itens = explode(',', $indexes[$i]);
                    $lang = null;

                    foreach($itens as $item)
                    {
                        if(is_null($lang))
                            $lang = self::$translation[trim($item)];
                        else if(isset($lang[$item]))
                            $lang = $lang[$item];
                    }

                    // Obtém os dados a serem utilizados.
                    $data = explode(',', trim($values[$i]));

                    // Primeiro elemento diz que será o index a ser alterado.
                    $data2Pop = array_shift($data);

                    foreach($data as &$ptr)
                    {
                        $ptr = trim($ptr);
                    }

                    // Se a variavel for definida na tradução, então realiza a tradução da linha.
                    if(isset($lang[$data2Pop]))
                    {
                        // Chama o sprintf e obtém o texto a ser alterado.
                        $find = call_user_func_array('sprintf', array_merge([$lang[$data2Pop]], $data));

                        // Troca no texto as variaveis informadas.
                        $textToTranslate = str_replace($origins[$i], $find, $textToTranslate);
                    }

                    // Remove da memória as variaveis identificadas.
                    unset($lang, $find, $data2Pop, $data, $itens);
                }

                unset($i, $count, $values, $indexes, $origins);
            }
            unset($maches);

            return $textToTranslate;
        // });
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
        // Obtém o retorno dos linguagens.
        return Cache::get('BRACP_LANGUAGES', function() {
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
        });
    }
}
