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
class Format
{
    /**
     * Transforma em bytes o formato presente em php.ini
     *
     * @param string $strSize
     *
     * @return int Valor em bytes.
     */
    public function parseBytes($strSize)
    {
        $strSize = trim($strSize);
        $size = intval(substr($strSize, 0, -1));
        $unit = strtoupper(substr($strSize, -1));

        switch($unit)
        {
            case 'G': $size *= 1024;
            case 'M': $size *= 1024;
            case 'K': $size *= 1024; break;
            default: break;
        }

        return $size;
    }

    /**
     * Transforma os bytes informados em valores formatados para serem lidos de forma simples.
     *
     * @param int $bytes
     * @param int $byteLimit
     *
     * @return string Valor formatado em bytes.
     */
    public function getBytesFormatted($bytes, $byteLimit = 1024)
    {
        // Formatos aceitos
        $format = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $count = count($format);
        $totalBytes = $bytes;

        // Não usar bytes negativos...
        $bytes = max(0, $bytes);

        // Indice do vetor.
        $index = min($count - 1, floor(log($bytes) / log($byteLimit)));

        // Bytes finais para retorno em tela.
        $bytes /= pow($byteLimit, $index);

        return sprintf('%.2f %s (%s bytes)', $bytes, $format[$index], $totalBytes);
    }

    /**
     * Método para proteger um endereço de e-mail enviado.
     *
     * @param string $email
     *
     * @return string Endereço de e-mail protegido.
     */
    public function protectMail($email)
    {
        if(!preg_match('/^([a-z0-9._%+-])([^\@]+)([a-z0-9._%+-])(.*)/i', $email, $match))
            return '?';
        
        array_shift($match);
        $match[1] = preg_replace('([a-z0-9._%+-])', '*', $match[1]);
        return implode('', $match);
    }

    /**
     * Método utilizado para obter o nome da classe já traduzido
     *
     * @param int $jobClass Código da classe a ser obtida.
     *
     * @return string Nome da classe.
     */
    public function jobname($jobClass)
    {
        return brACPApp::getInstance()
                        ->getLanguage()
                        ->getTranslate('@JOBS_' . $jobClass . '@');
    }

    /**
     * Método utilizado para formatação de campos do tipo zeny.
     *
     * @param int $zeny Valor em zenys a ser formatado.
     * @param string $delim Delimitador para os zenys.
     *
     * @return string Zenys formatadaos.
     */
    public function zeny($zeny, $delim = '.')
    {
        return strrev(implode($delim, str_split(strrev($zeny), 3)));
    }

    /**
     * Transforma um json em xml.
     *
     * @param object $json
     *
     * @return string Array formatado em xml.
     */
    public function json2xml($json)
    {
        $text = json_encode($json);
        return $this->array2xml(json_decode($text, true));
    }

    /**
     * Transforma json em um arquivo xml.
     *
     * @param array $array
     * @param \SimpleXMLElement $xml
     *
     * @return string Array formatado em xml.
     */
    public function array2xml($array, $xml = null)
    {
        if(is_null($xml))
        {
            $root = array_keys($array)[0];

            $xml = new \SimpleXMLElement('<'.$root.'/>');
            $this->array2xml($array[$root], $xml);
        }
        else
        {
            foreach($array as $k => $v)
            {
                if(preg_match('/^\-/', $k))
                    $xml->addAttribute(substr($k, 1), $v);
                else if(is_array($v))
                    $this->array2xml($v, $xml->addChild($k));
                else
                {
                    $xml->addChild($k, $v);
                }
            }
        }

        return $xml->asXML();
    }
}
