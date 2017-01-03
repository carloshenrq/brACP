<?php
/**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2017  brAthena, CHLFZ
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
 * Classe para validação de xml com uso de schemas.
 * Será utilizado a pasta de schemas como padrão para a validação.
 *
 * @static
 */
class SchemaValidator
{
    /**
     * Verifica um arquivo xml contra o xsd informado.
     *
     * @param string $xmlFile Caminho para o arquivo xml.
     * @param string $xsdFile Caminho para o arquivo xsd.
     *
     * @return bool Retorna verdadeiro caso seja validado.
     */
    public static function checkFileAgainst($xmlFile, $xsdFile)
    {
        return self::checkAgainst(file_get_contents($xmlFile), $xsdFile);
    }

    /**
     * Verifica o conteúdo de um arquivo xml contra um xsd informado.
     *
     * @param string $xmlData Dados do arquivo xml.
     * @param string $xsdFile Caminho para o arquivo xsd.
     *
     * @return bool Retorna verdadeiro caso seja validado.
     */
    public static function checkAgainst($xmlData, $xsdFile)
    {
        $xml = new DOMDocument();
        
        if(@$xml->loadXML($xmlData) == false)
            return false;
        
        return @$xml->schemaValidate('..' . DIRECTORY_SEPARATOR . 'schema' . DIRECTORY_SEPARATOR . $xsdFile);
    }

    /**
     * Converte um json para xml e aplica o validador de schema.
     *
     * @param object $json Dados json para serem validados.
     * @param string $xsdFile Caminho para o arquivo xsd.
     *
     * @return bool Retorna verdadeiro caso seja validado.
     */
    public static function checkJsonAgainst($json, $xsdFile)
    {
        $xmlData = \brACPApp::getInstance()->getFormat()->json2xml($json);
        return self::checkAgainst($xmlData, $xsdFile);
    }
}
