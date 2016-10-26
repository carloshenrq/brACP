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
     * Array contendo os dados de tradução para o brACP.
     *
     * @var array
     */
    private $translate;

    /**
     * Construtor para a classe de linguagens.
     *
     * @param string $lang Linguagem a ser carregada.
     */
    public function __construct($lang)
    {
        // Diretório para inclusão das linguagens.
        $lang_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .'lang';

        // Obtém os dados de tradução que estão em memória.
        $this->translate = Cache::get('BRACP_LANG_' . $lang, function() use ($lang_dir, $lang) {
            return (include_once $lang_dir . DIRECTORY_SEPARATOR . $lang . '.php');
        });

        // Veririca se o brACP está permitindo mods
        // Se estiver, verifica a pasta em busca de mods para tradução
        // do brACP.
        if(BRACP_ALLOW_MODS)
        {
            // Inicializa o loading dos mods para a linguagem em questão.
            $langMods = array_filter(scandir($lang_dir), function($file) use ($lang) {
                return preg_match('/^'.$lang.'\.([^\.]+)\.mod\.php$/i', $file);
            });
            sort($langMods);

            // Carrega os strings de tradução com mods aplicados.
            foreach($langMods as $langMod)
            {
                $this->translate = array_merge($this->translate,
                    (include_once $lang_dir . DIRECTORY_SEPARATOR . $langMod )
                );
            }
        }
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
     * Obtém o string de translate para o index informado.
     *
     * @param string $index
     *
     * @return string Index com a tradução ou se não encontrar o proprio index.
     */
    public function getTranslate($index)
    {
        // Verifica se é o formato para ser uma string de tradução.
        // Inicia-se com @
        // Termina-se com @
        // Se não for, verifica se é uma constante definida,
        //  Se for, exibe a constante, se não, retorna o index.
        if(!preg_match('/\@([^\@]+)\@$/i', $index, $match))
            return (defined($index) ? constant($index) : $index);

        // Obtém todos os indexes para procurar a string de tradução
        // Solicitada.
        $indexes = explode('_', $match[1]);

        // Varre os indices procurando a string de tradução.
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

        // Se for encontrado o indice, então retorna a tradução para
        // o indice informado.
        if(!is_null($_tmp) && !is_array($_tmp) && is_string($_tmp))
            $_translate = $_tmp;

        // Retorna o dado de tradução.
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
