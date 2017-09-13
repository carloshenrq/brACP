<?php

/**
 * Classe para gerênciamento da tradução da aplicação.
 */
class AppLanguage extends AppMiddleware
{
    /**
     * Vetor contendo todas as linguagens disponíveis para uso.
     * @var array
     */
    private $langs = [];

    /**
     * Vetor contendo todas as traduções para a linguagem informada.
     * @var array
     */
    private $langData = [];

    /**
     * @see AppMiddleware::init()
     */
    protected function init()
    {
        // Define o objeto de linguagem da aplicação.
        $this->getApp()->setLanguage($this);

        // Trata as informações de cache para as linguagens
        // Contidas no application.
        $this->langs = $this->getApp()->getCache()->parse('appLanguages', function() {
            $app = App::getInstance();

            // Faz um select para saber quais linguagens estão instaladas
            // No banco de dados.
            $stmt_langs = $app->getSqlite()->query('SELECT * FROM languages');
            $ds_langs = $stmt_langs->fetchAll(PDO::FETCH_OBJ);

            // Se não houver linguagens instaladas, varre o diretório de linguagens
            // Para verificar os arquios de linguagem
            if(count($ds_langs) == 0)
            {
                $langDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR  . 'languages';
                $phpFiles = scandir(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR  . 'languages');
                $langFiles = array_filter($phpFiles, function($lang) {
                    return preg_match('/[a-zA-Z]{2}(?:-|_)[a-zA-Z]{2}\.php/i', $lang) > 0;
                });
                sort($langFiles);

                $stmt_lang = $app->getSqlite()->prepare('
                    INSERT INTO languages VALUES (:LanguageID, :LanguageName, :LanguageFile);
                ');

                // Varre todos os arquivos de linguagem encontrados
                // Para gravar no banco de dados.
                foreach($langFiles as $langFile)
                {
                    $fullLangFile = realpath($langDir . DIRECTORY_SEPARATOR . $langFile);
                    $langData = (include $fullLangFile);

                    $stmt_lang->execute([
                        ':LanguageID'   => $langData['Information']['Accron'],
                        ':LanguageName' => $langData['Information']['Name'],
                        ':LanguageFile' => $fullLangFile,
                    ]);

                    // Atribui ao vetor de dados para não fazer o select novamente.
                    $ds_langs[] = (object)[
                        'LanguageID'    => $langData['Information']['Accron'],
                        'LanguageName' => $langData['Information']['Name'],
                        'LanguageFile' => $fullLangFile,
                    ];
                }
            }

            // Array para gravar em cache as linguagens que estão no banco de dados.
            $langs = [];

            foreach($ds_langs as $rs_langs)
            {
                $langs[$rs_langs->LanguageID] = (object)[
                    'name' => $rs_langs->LanguageName,
                    'file' => $rs_langs->LanguageFile,
                ];
            }

            return $langs;
        });
    }

    /**
     * Obtém todos os arquivos de linguagem para serem utilizados.
     *
     * @return array
     */
    public function getLangs()
    {
        return $this->langs;
    }

    /**
     * @see AppMiddleware::__invoke()
     */
    public function __invoke($request, $response, $next)
    {
        // Se não estiver definido a linguagem de uso da aplicação
        // Tenta obter a linguagem que o navegador fornece ao usuário.
        if(!isset($this->getApp()->getSession()->APP_LANGUAGE))
        {
            // Obtém o cabeçalho de linguagens que o navegador do usuário
            // aceita.
            $headerLang = $request->getHeader('accept-language')[0];
            $userLang = APP_DEFAULT_LANGUAGE;

            // Verifica o cabeçalho de linguagem e depois encontra as possíveis
            // Linguagens que o navegador do usuário aceita.
            if(preg_match_all('/[a-zA-Z]{2}(?:-|_)[a-zA-Z]{2}/i', $headerLang, $matches))
            {
                // Linguagens encontradas.
                $langsFound = array_shift($matches);

                foreach($langsFound as $lang)
                {
                    if(array_key_exists($lang, $this->langs))
                    {
                        $userLang = $lang;
                        break;
                    }
                }
            }

            // Define a linguagem do usuário no session.
            $this->getApp()->getSession()->APP_LANGUAGE = $userLang;
        }

        // Indice da linguagem selecionada pelo usuário.
        $langIndex = $this->getApp()->getSession()->APP_LANGUAGE;

        // Carrega os dados de linguagem para a sessão de usuário.
        if(array_key_exists($langIndex, $this->langs))
        {
            $langData = (include $this->langs[$langIndex]->file);
            $this->langData = $langData['Translate'];
        }

        // Move para a próxima execução.
        return parent::__invoke($request, $response, $next);
    }

    /**
     * Obtém a tradução para o indice informado.
     *
     * @param string $index Indice para achar no arquivo de tradução.
     * @param array $params Caso exista tratamento para %s, %d, etc...
     *
     * @return string Dados traduzidos ou o indice de tradução.
     */
    public function getTranslate($index, $params = array(), $sep = ';')
    {
        // Se o valor não estiver na expressão regular,
        // Então será retornado como constante, se houver uma declarada para o indice
        // Informado, se não, retorna o indice.
        if(!preg_match('/^\@([^\@]+)\@$/i', $index, $matches))
            return ((defined($index)) ? constant($index) : $index);
        
        // Obtém os dados informados pelo indice.
        // @TEST;HELLO;TITLE@
        $indexes = explode($sep, $matches[1]);

        // Varre os indices procurando a string de tradução.
        $_tmp = $this->langData;
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
        {
            $_translate = $_tmp;
            if(count($params) > 0)
                $_translate = vsprintf($_translate, $params);
        }

        // Retorna o dado de tradução.
        return $_translate;
    }
}

