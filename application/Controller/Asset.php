<?php

namespace Controller;

use \Leafo\ScssPhp\Compiler;
use \MatthiasMullie\Minify;

/**
 * Classe para gerenciamento dos arquivos.
 */
class Asset extends AppController
{
    /**
     * Variavel para guardar o local de acesso aos arquivos
     *  de asset da aplicação.
     *
     * @var string
     */
    private $assetDir;

    /**
     * Atributo para armazenar o diretório dos temas, guarda também o tema atual do usuário.
     *
     * @var string
     */
    private $themeDir;

    /**
     * Obtém todos os dados dos arquivos in-cache
     */
    private $filesInCache = [];

    /**
     * Inicializa o controller e define o diretório dos assets.
     */
    protected function init()
    {
        // Diretório de assets
        $this->assetDir = realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__ ,'..', '..', 'assets'
        ]));

        // Diretório de temas
        $this->themeDir = implode(DIRECTORY_SEPARATOR, [
            $this->assetDir, 'themes', $this->getApp()->getSession()->APP_THEME
        ]);

        // Varre os itens e adiciona ao vetor de informações.
        // Todos os dados correspondentes.
        $stmt_files = $this->getApp()->getSqlite()->query('SELECT Filename, Filehash, FileOutput FROM asset_cache');
        $ds_files = $stmt_files->fetchAll(\PDO::FETCH_OBJ);
        foreach($ds_files as $rs_files)
            $this->filesInCache = array_merge($this->filesInCache, [
                $rs_files->Filename => (object)[
                    'hash'      => $rs_files->Filehash,
                    'output'    => $rs_files->FileOutput,
                ]
            ]);
    }

    /**
     * Método para obter arquivos do tipo imagem da requisição.
     *
     * @param object $response
     *
     * @return object
     */
    public function img_GET($response, $args)
    {
        // Obtém o caminho para a imagem do tema.
        $img = implode(DIRECTORY_SEPARATOR, [
            $this->themeDir,
            'img',
            $args->src
        ]);

        // Se o arquivo não existir, procura na pasta default.
        if(!file_exists($img))
            $img = implode(DIRECTORY_SEPARATOR, [
                $this->assetDir,
                'img',
                $args->src
            ]);

        // Obtém o tipo de imagem que será retornada na visualização.
        if(function_exists('mime_content_type'))
            $contentType = mime_content_type($img);
        else
            $contentType = 'image/png';

        // Faz leitura do arquivo e já dá o output.
        $contentImage = file_get_contents($img);

        // Responde com cabeçalho de imagem.
        return $response
                ->write($contentImage)
                ->withHeader('Content-Type', $contentType);
    }

    /**
     * Obtém os dados de css informados.
     */
    public function css_GET($response, $args)
    {
        $css = '';

        if(isset($args->file))
            $css = $this->getCssFile($args->file);
        else
            $css = $this->getCssFiles();

        // Retorna o conteudo css.
        return $response->write($css)
                        ->withHeader('Content-Type', 'text/css');
    }

    /**
     * Retorna os dados compilados de javascript.
     */
    public function js_GET($response, $args)
    {
        $js = '';

        if(isset($args->file))
            $js = $this->getJsFile($args->file);
        else
            $js = $this->getJsFiles();

        return $response->write($js)
                        ->withHeader('Content-Type', 'application/javascript');
    }

    /**
     * Obtém todos os arquivos CSS
     */
    public function getCssFiles()
    {
        // Obtém todos os arquivos .scss da pasta 
        $assetDirFiles = array_filter(scandir(implode(DIRECTORY_SEPARATOR, [
            $this->assetDir, 'scss'
        ])), function($value) {
            return preg_match('/^(?!_)(.+)\.scss$/i', $value);
        });
        sort($assetDirFiles);

        // Obtém todos os arquivos .scss da pasta 
        $themeDirFiles = array_filter(scandir(implode(DIRECTORY_SEPARATOR, [
            $this->themeDir, 'scss'
        ])), function($value) {
            return preg_match('/^(?!_)(.+)\.scss$/i', $value);
        });
        sort($themeDirFiles);
        $cssDirFiles = array_unique(array_merge($assetDirFiles, $themeDirFiles));

        $assetObj = $this;

        // Obtém todos os arquivos CSS compilados
        $cssCompiledFiles = array_map(function($value) use ($assetObj) {
            return $assetObj->getCssFile($value);
        }, $cssDirFiles);

        return implode(' ', $cssCompiledFiles);
    }

    /**
     * Obtém o arquivo css informado.
     *
     * @param string $cssFile
     */
    public function getCssFile($cssFile)
    {
        // Obtém o caminho para o arquivo css do tema informado
        $file = implode(DIRECTORY_SEPARATOR, [
            $this->themeDir, 'scss', $cssFile
        ]);

        // Verifica a existência do arquivo por tema, se não existir
        // Então ele faz a leitura da pasta oficial.
        if(!file_exists($file))
            $file = implode(DIRECTORY_SEPARATOR, [
                $this->assetDir, 'scss', $cssFile
            ]);

        // Se mesmo assim o arquivo não existir, retorna em branco, caso contrario vai a leitura.
        if(!file_exists($file))
            return '';

        // Calcula o hash para o arquivo local. 
        $cssHash = hash_file('sha384', $file);

        // Se o arquivo existe no indice de vetor verifica os hases, se forem iguals
        // Manterá o arquivo, caso contrario irá remover a entrada e logo após apagar o arquivo
        // criado anteriormente.
        if(array_key_exists($cssFile, $this->filesInCache))
        {
            // Obtém o parâmetro para verificar informações de cache.
            $cache = $this->filesInCache[$cssFile];

            // Se o hash o bater e o arquivo existir, então, retorna o
            // conteudo do arquivo.
            if($cache->hash == $cssHash && file_exists($cache->output))
                return file_get_contents($cache->output);

            // Remove o index do array.
            unset($this->filesInCache[$cssFile]);

            // Caso uma das informações acima não seja verdeira... 
            // Apaga o registro da tabela e também o arquivo que estava criado.
            if(file_exists($cache->output)) unlink($cache->output);

            $stmt_delete = $this->getApp()->getSqlite()->prepare('
                DELETE FROM asset_cache WHERE Filename = :Filename
            ');
            $stmt_delete->execute([
                ':Filename'     => $cssFile
            ]);
        }

        // Cria instância do compilador e compila os dados do scss para a tela.
        $compiler = new Compiler;
        $compiler->setImportPaths([
            implode(DIRECTORY_SEPARATOR, [
                $this->themeDir, 'scss'
            ]),
            implode(DIRECTORY_SEPARATOR, [
                $this->assetDir, 'scss'
            ])
        ]);
        $compiler->setVariables([
            'assetUrl'  => APP_URL_PATH . '/asset',
            'gridCols'  => 12,
        ]);
        $compiledCss = $compiler->compile(file_get_contents($file));

        // Cria a instância do minifier para remover comentários e agilizar o tamanho dos arquivos.
        $minify = new Minify\CSS;
        $minify->add($compiledCss);
        $minifiedCss = $minify->minify();
        $minifiedHash = hash('sha384', $minifiedCss);
        $minifiedFile = implode(DIRECTORY_SEPARATOR, [dirname($file), $minifiedHash . '.css']);
        file_put_contents($minifiedFile, $minifiedCss);

        // Grava na tabela de cache os dados do arquivo.
        $stmt_file = $this->getApp()->getSqlite()->prepare('
            INSERT INTO asset_cache VALUES (:Filename, :Filehash, :FileOutput)
        ');
        $stmt_file->execute([
            ':Filename'     => $cssFile,
            ':Filehash'     => $cssHash,
            ':FileOutput'   => $minifiedFile
        ]);

        // Retorna o css minificado.
        return $minifiedCss;
    }

    /**
     * Obtém todos os arquivos CSS
     */
    public function getJsFiles()
    {
        // Obtém todos os arquivos .scss da pasta 
        $assetDirFiles = array_filter(scandir(implode(DIRECTORY_SEPARATOR, [
            $this->assetDir, 'js'
        ])), function($value) {
            return preg_match('/^(?!_)([a-zA-Z0-9-\.]{1,60})\.js$/i', $value);
        });
        sort($assetDirFiles);

        // Obtém todos os arquivos .scss da pasta 
        $themeDirFiles = array_filter(scandir(implode(DIRECTORY_SEPARATOR, [
            $this->themeDir, 'js'
        ])), function($value) {
            return preg_match('/^(?!_)([a-zA-Z0-9-\.]{1,60})\.js$/i', $value);
        });
        sort($themeDirFiles);
        $jsDirFiles = array_unique(array_merge($assetDirFiles, $themeDirFiles));

        $assetObj = $this;

        // Obtém todos os arquivos CSS compilados
        $jsCompiledFiles = array_map(function($value) use ($assetObj) {
            return $assetObj->getJsFile($value);
        }, $jsDirFiles);

        return implode(';', $jsCompiledFiles);
    }

    /**
     * Obtém o arquivo css informado.
     *
     * @param string $cssFile
     */
    public function getJsFile($jsFile)
    {
        // Obtém o caminho para o arquivo css do tema informado
        $file = implode(DIRECTORY_SEPARATOR, [
            $this->themeDir, 'js', $jsFile
        ]);

        // Verifica a existência do arquivo por tema, se não existir
        // Então ele faz a leitura da pasta oficial.
        if(!file_exists($file))
            $file = implode(DIRECTORY_SEPARATOR, [
                $this->assetDir, 'js', $jsFile
            ]);

        // Se mesmo assim o arquivo não existir, retorna em branco, caso contrario vai a leitura.
        if(!file_exists($file))
            return '';

        // Calcula o hash para o arquivo local. 
        $jsHash = hash_file('sha384', $file);

        // Se o arquivo existe no indice de vetor verifica os hases, se forem iguals
        // Manterá o arquivo, caso contrario irá remover a entrada e logo após apagar o arquivo
        // criado anteriormente.
        if(array_key_exists($jsFile, $this->filesInCache))
        {
            // Obtém o parâmetro para verificar informações de cache.
            $cache = $this->filesInCache[$jsFile];

            // Se o hash o bater e o arquivo existir, então, retorna o
            // conteudo do arquivo.
            if($cache->hash == $jsHash && file_exists($cache->output))
                return file_get_contents($cache->output);

            // Remove o index do array.
            unset($this->filesInCache[$jsFile]);

            // Caso uma das informações acima não seja verdeira... 
            // Apaga o registro da tabela e também o arquivo que estava criado.
            if(file_exists($cache->output)) unlink($cache->output);

            $stmt_delete = $this->getApp()->getSqlite()->prepare('
                DELETE FROM asset_cache WHERE Filename = :Filename
            ');
            $stmt_delete->execute([
                ':Filename'     => $jsFile
            ]);
        }

        // Obtém o conteudo do arquivo JS solicitado.
        $jsContent = file_get_contents($file);

        // Cria a instância do minifier para remover comentários e agilizar o tamanho dos arquivos.
        $minify = new Minify\JS;
        $minify->add($jsContent);
        $minifiedJs = $minify->minify();
        $minifiedHash = hash('sha384', $minifiedJs);
        $minifiedFile = implode(DIRECTORY_SEPARATOR, [dirname($file), $minifiedHash . '.js']);
        file_put_contents($minifiedFile, $minifiedJs);

        // Grava na tabela de cache os dados do arquivo.
        $stmt_file = $this->getApp()->getSqlite()->prepare('
            INSERT INTO asset_cache VALUES (:Filename, :Filehash, :FileOutput)
        ');
        $stmt_file->execute([
            ':Filename'     => $jsFile,
            ':Filehash'     => $jsHash,
            ':FileOutput'   => $minifiedFile
        ]);

        // Retorna o css minificado.
        return $minifiedJs;
    }
}
