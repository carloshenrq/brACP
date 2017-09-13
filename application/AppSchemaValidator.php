<?php

/**
 * Classe para realizar as validações schemas de xmls
 *  contra os arquivos de schema xsds.
 */
class AppSchemaValidator extends AppComponent
{
    /**
     * Valida um conteudo xml contra o arquivo xsd.
     *
     * @param string $xml
     * @param string $xsdFile
     *
     * @return boolean Verdadeiro se o xml for válido contra o schema.
     */
    public function validate($xmlData, $xsdFile)
    {
        libxml_use_internal_errors(true);
        $xml = new \DOMDocument();

        if(@$xml->loadXML($xmlData) == false)
            return false;
        
        return @$xml->schemaValidate(APP_SCHEMA_DIR . DIRECTORY_SEPARATOR . $xsdFile);
    }

    /**
     * Obtém todos os erros em formato array após a tentativa de validação.
     *
     * @return array
     */
    public function getAllErrors()
    {
        $errors = [];
        $xmlErrors = libxml_get_errors();
        libxml_clear_errors();

        foreach($xmlErrors as $error)
            $errors[] = $error->code . ' - ' . $error->message;

        return $errors;
    }

    /**
     * Retorna todos os erros em formato de string.
     *
     * @param string $glue Caractere que será utilizado para juntar informações do erro.
     * 
     * @return string
     */
    public function getAllErrorsString($glue = '<br>')
    {
        return implode($glue, $this->getAllErrors());
    }

    /**
     * Realiza a conversão de um json em XML.
     *
     * @param object $json
     *
     * @return string Json em formato xml.
     */
    public function json2xml($json)
    {
        return $this->array2xml(json_decode(json_encode($json), true));
    }

    /**
     * Realiza a conversão de vetores em xmls.
     *
     * @param array $array
     * @param SimpleXMLElement $xml
     *
     * @return string Array em formato xml convertido.
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