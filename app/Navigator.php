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

    private static $navigators = [
        // Teste para navegador google chrome
        [
            'name' => 'Google Chrome',
            'icon' => 'nav-chrome',
            'regex' => [
                '/Chrome\/([^\s]+) Safari\/(?:[a-zA-Z0-9.]+)$/i',
            ]
        ],
        // Teste para navegador firefox
        [
            'name' => 'Mozilla Firefox',
            'icon' => 'nav-firefox',
            'regex' => [
                '/(?:Firefox|Firebird)\/([a-zA-Z0-9.]+)$/i',
            ]
        ],
        // Teste para navegador Internet Explorer
        [
            'name' => 'Internet Explorer',
            'icon' => 'nav-ie',
            'regex' => [
                '/MSIE ([a-zA-Z0-9.]+)$/i',
                '/rv\:([a-zA-Z0-9.]+)\) like Gecko$/i',
            ]
        ],
        // Teste para navegador Safari
        [
            'name' => 'Safari',
            'icon' => 'nav-safari',
            'regex' => [
                '/Version\/([a-zA-Z0-9.]+) (?:(?:Mobile\/(?:[a-zA-Z0-9.]+)\s)?)Safari\/(?:[a-zA-Z0-9.]+)$/i',
            ]
        ],
        // Teste para navegador Opera
        [
            'name' => 'Opera',
            'icon' => 'nav-opera',
            'regex' => [
                '/^(?:Opera|Mozilla).*(?:Version\/|Opera\s)([a-zA-Z0-9.]+)$/i',
            ]
        ],
    ];

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
        // Varre todos os navegadores identificados para realizar o teste na tela.
        foreach(self::$navigators as $navigator)
        {
            // Varre todas as expressões regulares para encontrar o navegador que se
            //  encaixa no usuário.
            foreach($navigator['regex'] as $regex)
            {
                // Verifica a expressão regular no userAgent do usuário.
                if(preg_match($regex, $userAgent, $matches))
                {
                    // Retorna os dados de navegador que for encontrado.
                    return new Navigator($navigator['name'], $navigator['icon'], $matches[1]);
                }
            }
        }

        // Caso não encontre o navegador, então retorna NULL.
        // Não será exibido em tela.
        return null;
    }

}
