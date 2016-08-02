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
 * Classe para criar os arquivos de cache de forma local.
 *
 * @author CarlosHenrq
 */
class LocalCache implements ICache
{
    /**
     * Diretório onde serão gerados os arquivos cache.
     * @var string
     */
    private $cacheDir;

    /**
     * Caminho para o arquivo de indice.
     * @var string
     */
    private $cacheIndexFile;

    public function __construct($cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR)
    {
        $this->cacheDir = $cacheDir;
        if(!is_dir($cacheDir))
            mkdir($cacheDir);

        // Indice de arquivo.
        $this->cacheIndexFile = $this->cacheDir . DIRECTORY_SEPARATOR . 'cache.index';

        // Verifica se o arquivo indice foi criado.
        if(!is_file($this->cacheIndexFile))
            file_put_contents($this->cacheIndexFile, serialize([]));

        // Limpa o conteudo antigo do arquivo de cache.
        $this->eraseOldData();
    }

    /**
     * Reaaliza a leitura/escrita de uma chave no cache.
     *
     * @param string $key 
     * @param mixed $value
     * @param int $time Tempo em segundos para ficar em cache.
     * @param boolean $force Se irá forcar a escrita no cache.
     *
     * @return mixed Retorna os dados em cache. Caso não exista a chave, retorna false.
     */
    public function parse($key, $value, $time = 600, $force = false)
    {
        if(($cache = $this->read($key)) !== false)
            return $cache;

        if( $this->write($key, $value, $time, $force) === true )
            return $this->parse($key, $value, $time, $force);

        return false;
    }

    /**
     * Realiza a leitura de uma chave no cache.
     *
     * @param string $key Chave no cache.
     *
     * @return mixed Retorna os dados em cache. Caso não exista a chave, retorna false.
     */
    public function read($key)
    {
        $file = $this->findFile($key);

        if(!file_exists($file))
            return false;

        $cache = unserialize(base64_decode(file_get_contents($file)));

        if($cache['time'] < time())
        {
            $this->erase($key);
            return false;
        }

        return $cache['data'];
    }

    /**
     * Escreve o valor da chave no cache.
     *
     * @param string $key 
     * @param mixed $value
     * @param int $time Tempo em segundos para ficar em cache.
     * @param boolean $force Se irá forcar a escrita no cache.
     *
     * @return boolean Verdadeiro se escrevou e falso caso contrario.
     */
    public function write($key, $value, $time = 600, $force = false)
    {

        if($this->read($key) !== false && !$force)
            return false;

        $file = $this->findFile($key);

        $content = ((is_callable($value)) ? $value() : $value);
        $cache = base64_encode(serialize(['time' => time() + $time, 'data' => $content]));

        file_put_contents($file, $cache);
        $this->saveIndex($key);

        return true;
    }

    /**
     * Apaga um arquivo de acordo com a chave informada no cache.
     *
     * @param string $key
     *
     * @return void
     */
    public function erase($key)
    {
        $this->removeIndex($key);

        return;
    }

    /**
     * Monta o caminho do arquivo de cache que será lido.
     *
     * @param string $key
     *
     * @return string Caminho do arquivo cache.
     */
    private function findFile($key)
    {
        return join(DIRECTORY_SEPARATOR, [ $this->cacheDir, $key . '.cache' ]);
    }

    /**
     * Salva a chave no arquivo de indice.
     *
     * @param string $key Chave para o cache no arquivo de indice.
     */
    private function saveIndex($key)
    {
        $indexData = unserialize(file_get_contents($this->cacheIndexFile));

        $indexData[$key] = $this->findFile($key);
        file_put_contents($this->cacheIndexFile, serialize($indexData));

        return;
    }

    /**
     * Remove a chave do arquivo de indice.
     *
     * @param string $key Chave para o cache do arquivo.
     */
    private function removeIndex($key)
    {
        $indexData = unserialize(file_get_contents($this->cacheIndexFile));

        if(isset($indexData[$key]))
        {
            unlink($indexData[$key]);
            unset($indexData[$key]);
            file_put_contents($this->cacheIndexFile, serialize($indexData));
        }

        return;
    }

    /**
     * Apaga todo o conteudo antigo do arquivo de indice.
     */
    private function eraseOldData()
    {
        $indexData = unserialize(file_get_contents($this->cacheIndexFile));

        foreach($indexData as $key => $file)
        {
            $cache = unserialize(base64_decode(file_get_contents($file)));

            if($cache['time'] < time())
                $this->removeIndex($key);
        }

        return;
    }

    /**
     * Apaga todo o conteudo do arquivo de indice.
     */
    private function flushIndex()
    {
        $indexData = unserialize(file_get_contents($this->cacheIndexFile));

        foreach( $indexData as $key => $file )
        {
            unlink($file);
        }

        file_put_contents($this->cacheIndexFile, serialize([]));
        return;
    }
}
