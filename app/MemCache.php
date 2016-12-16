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
 * Classe para cache utilizando o servidor memcache.
 *
 * @author CarlosHenrq
 */
class MemCache implements ICache
{
    /**
     * Endereço do servidor de memcache.
     * @var string
     */
    private $host;

    /**
     * Porta para conexão com o servidor memcache
     * @var int
     */
    private $port;

    /**
     * Objeto de conexão com o servidor memcache.
     * @var resource
     */
    private $memcached;

    /**
     * Construtor para a classe do memcache.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port)
    {
        // Não foi possível se conectar no servidor de cache de memória.
        if(($this->memcached = @memcache_pconnect($host, $port)) === false)
            throw new Exception();
    }

    /**
     * @see ICache::read()
     */
    public function read($key)
    {
        $value = memcache_get($this->memcached, $key);
        return ((empty($value)) ? false : unserialize(base64_decode($value)));
    }

    /**
     * @see ICache::write()
     */
    public function write($key, $value, $time = 600, $force = false)
    {
        // Valor não está sendo forçado e existe em memória,
        // Retorna falso, não pode escrever.
        if(!$force && $this->read($key) !== false)
            return false;
        
        // Valor a ser armazenado em memória.
        $parsedValue = base64_encode(serialize(((is_callable($value)) ? $value() : $value)));

        // Define cache no memcache para os dados.
        return @memcache_set($this->memcached, $key, $parsedValue);
    }

    /**
     * @see ICache::erase()
     */
    public function erase($key)
    {
        @memcache_delete($this->memcached, $key);
    }

    /**
     * @see ICache::parse()
     */
    public function parse($key, $value, $time = 600, $force = false)
    {
        if(!$force && ($cache = $this->read($key)) !== false)
            return $cache;
        else if($this->write($key, $value, $time, $force))
            return $this->parse($key, $value, $time, $force);
        else
            return false;
    }
}