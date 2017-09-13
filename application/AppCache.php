<?php
/**
 * Classe para gerenciamento do cache da aplicação.
 */
class AppCache extends AppMiddleware
{
    /**
     * Armazena o caminho completo para o arquivo de index.
     * @var string
     */
    private $index;

    /**
     * Armazena o vetor de informações contidos no arquivo de index.
     * @var array
     */
    private $cache_data;

    /**
     * Inicializa o tratamento para o cache da aplicação.
     * O cache irá armazenar os arquivos em disco, e fornece-los quando necessário.
     */
    protected function init()
    {
        // Vincula o cache ao App
        $this->getApp()->setCache($this);

        // Adiciona o arquivo de index
        // Se o arquivo não existir, o mesmo será criado.
        $this->index = APP_CACHE_DIR . DIRECTORY_SEPARATOR . 'cache.index';
        if(!file_exists($this->index))
            file_put_contents($this->index, base64_encode(serialize([])));
        
        // Obtém o conteudo do arquivo de index.
        $this->cache_data = unserialize(base64_decode(file_get_contents($this->index)));

        // Trata todo o conteudo de cache.
        $this->parseIndex();
    }

    /**
     * Trata as informações de cache e retorna os dados que foram ou estão em cache.
     *
     * @param string $index Indice a ser adicionado ao cache.
     * @param mixed $value Dados a serem adicionados ao cache.
     * @param int $expire Tempo para o cache expirar.
     * @param bool $force Caso o cache exista, e aqui seja verdadeiro, então apaga e adiciona novamente.
     */
    public function parse($index, $value, $expire = APP_CACHE_TIMEOUT, $force = false)
    {
        // Se o modo desenvolvedor estiver ligado, então
        // O Cache será refeito e enviado.
        if(!$force && !APP_CACHE_ENABLED) $force = true;

        $this->add($index, $value, $expire, $force);
        return $this->get($index);
    }

    /**
     * Adiciona dados ao cache.
     *
     * @param string $index Indice a ser adicionado ao cache.
     * @param mixed $value Dados a serem adicionados ao cache.
     * @param int $expire Tempo para o cache expirar.
     * @param bool $force Caso o cache exista, e aqui seja verdadeiro, então apaga e adiciona novamente.
     */
    public function add($index, $value, $expire = APP_CACHE_TIMEOUT, $force = false)
    {
        // Caso não seja forcando os dados de indice e ele exista...
        if(!$force && isset($this->cache_data[$index]))
            return false;

        // Adiciona os dados ao indice com sucesso.
        $this->addIndex($index, ((is_callable($value)) ? $value():$value), $expire);
        return true;
    }

    /**
     * Apaga os dados de indice
     *
     * @param string $index
     */
    public function del($index)
    {
        $this->delIndex($index);
    }

    /**
     * Obtém os dados do cache.
     *
     * @param string $index
     *
     * @return mixed Dados em cache.
     */
    public function get($index)
    {
        return $this->getIndex($index);
    }

    /**
     * Adiciona um item ao arquivo de indice do cache.
     *
     * @param string $index Nome do indice a ser utilizado.
     * @param mixed $data Dados a serem adicionados ao indice.
     * @param int $expire Tempo que os dados vão ficar no indice.
     */
    private function addIndex($index, $data, $expire)
    {
        // Deleta do indice os dados anteriores.
        $this->delIndex($index);

        // Gera o nome do arquivo que será utilizado para
        // O Arquivo de cache.
        $tmpname = tempnam(APP_CACHE_DIR, 'app');

        // Gera os dados para o arquivo de indice no cache.
        $cache = (object)[
            'file'      => $tmpname,
            'expire'    => (($expire == -1) ? '0' : (time() + $expire)),
        ];

        // Grava o conteúdo de cache dentro do arquivo.
        file_put_contents($tmpname, base64_encode(serialize($data)));

        // Adiciona os dados de cache ao arquivo de indice.
        $this->cache_data = array_merge($this->cache_data, [
            $index => $cache,
        ]);

        // Salva o arquivo de indice.
        $this->saveIndex();
    }

    /**
     * Deleta o arquivo do indice.
     *
     * @param string $index
     */
    private function delIndex($index)
    {
        // Se o indice não existir no vetor de cache
        // Não tem nada a se fazer aqui.
        if(!isset($this->cache_data[$index]))
            return;

        // Obtém os dados de cache a serem apagados do indice.
        $cache = $this->cache_data[$index];
        unset($this->cache_data[$index]);
        $this->saveIndex();

        // Apaga o arquivo de cache informado.
        @unlink($cache->file);
    }

    /**
     * Obtém os dados de um arquivo de acordo com o indice informado.
     *
     * @param string $index
     *
     * @return mixed Irá retornar NULL caso o indice não seja encontrado.
     */
    private function getIndex($index)
    {
        // Se não for encontrado no arquivo de indice,
        // Então, será retornado NULL.
        if(!isset($this->cache_data[$index]))
            return null;

        // Obtém as informações de cache.
        $cache = $this->cache_data[$index];

        // Retorna os dados de cache.
        return unserialize(base64_decode(file_get_contents($cache->file)));
    }

    /**
     * Trata todo o conteúdo de cache verificando pelos arquivos antiso e removendo eles
     * Caso necessário.
     */
    private function parseIndex()
    {
        // Copia os dados do array para não perder nenhuma
        // Referência.
        $cacheData = $this->cache_data;

        foreach($cacheData as $key => $value)
        {
            // Se o arquivo existir, e não tiver expirado ou
            // For um cache eterno, então mantém o arquivo
            if(file_exists($value->file) && ($value->expire == 0 || $value->expire > time()))
                continue;
            
            // Caso não seja, então irá remover do cache.
            $this->delIndex($key);
        }
    }

    /**
     * Salva o arquivo de indice.
     */
    private function saveIndex()
    {
        file_put_contents($this->index, base64_encode(serialize($this->cache_data)));
    }

}
