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
 * Classe para formatação dos dados.
 *
 * @static
 */
class Navigator
{
    const MozillaFirefox = '/^Mozilla\/5.0 \([^\)]+\) Gecko\/([0-9.]+) Firefox\/([0-9.]+)$/i';
    const GoogleChrome = '/^Mozilla\/5.0 \([^\)]+\) AppleWebKit\/([0-9.]+) \([^\)]+\) Chrome\/([0-9.]+) Safari\/([0-9.]+)$/i';
    const MicrosoftEdge = '/^Mozilla\/5.0 \([^\)]+\) AppleWebKit\/([0-9.]+) \([^\)]+\) Chrome\/([0-9.]+) Safari\/([0-9.]+) Edge\/([0-9.]+)$/i';
    const MicrosoftInternetExplorer = '/(?:MSIE ([0-9]{1,}[\.0-9]{0,})|^Mozilla\/5.0 \([^\)]+\) like Gecko$)/i';
    const Opera = '/Opera\/([0-9.]+)/i';
    const Safari = '/^Mozilla\/5.0 \([^\)]+\) AppleWebKit\/([0-9.]+) \([^\)]+\) Version\/([0-9.]+) ((?:Mobile\/([^\s]+)\s)?)Safari\/([0-9.A-Z]+)$/i';

    private $name;
    private $class;
    private $version;

    private function __construct($name, $class, $version)
    {
        $this->name = $name;
        $this->class = $class;
        $this->version = $version;
    }

    /**
     * Obtém o nome do navegador.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Obtém a classe css para o div que irá exibir.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Obtém a versão do navegador.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Obtém o objeto de navegador de acordo com o useragent.
     *
     * @param string $userAgent
     *
     * @return Navigator
     */
    public static function getBrowser($userAgent)
    {
        if(preg_match(self::GoogleChrome, $userAgent, $matches))
        {
            return '@Todo';
        }
        else if(preg_match(self::MozillaFirefox, $userAgent, $matches))
        {
            return new Navigator('Mozilla Firefox', 'nav-firefox', $matches[2]);
        }

        // @Todo: Fazer os testes para os demais navegadores.

        return new Navigator('Desconhecido', 'nav-unknow', '?');
    }

}
