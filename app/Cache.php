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
 * Classe para controlar os dados do memcache.
 *
 * @author CarlosHenrq
 */
class Cache
{
    private static $memcache = false;

    /**
     * Método utilizado para inicializar informações sobre o cache
     *  dos dados de usuário.
     *
     * @static
     *
     */
    public static function init()
    {
        // Se existir a biblioteca de memcache e a mesma estiver habilitada
        if(extension_loaded('memcache') && BRACP_MEMCACHE)
            self::$memcache = memcache_connect(BRACP_MEMCACHE_SERVER ,BRACP_MEMCACHE_PORT);
    }

    /**
     * Obtém os dados do cache da aplicação.
     *
     * @param mixed $index
     * @param mixed $defaultValue
     *
     * @return $mixed
     */
    public static function get($index, $defaultValue, $force = false)
    {
        // Se o cache não estiver habilitado, retorna o valor diretamente
        if(self::$memcache === false)
            return ((is_callable($defaultValue)) ? $defaultValue():$defaultValue);

        // Se houver dados no cache, então retorna os dados do cache
        if($force === false && ($fromCache = memcache_get(self::$memcache, $index, 0)) !== false)
            return $fromCache;

        // Se forcando, então deleta o indice do cache.
        if($force === true)
            self::delete($index);

        // Para o valor padrão retornado.
        $fromDefault = ((is_callable($defaultValue)) ? $defaultValue():$defaultValue);

        // Define no cache o valor
        memcache_set(self::$memcache, $index, $fromDefault, 0, BRACP_MEMCACHE_EXPIRE);

        // Retorna do padrão.
        return $fromDefault;
    }

    /**
     * Deleta os indices de memória do memcache.
     *
     * @param mixed $index
     */
    public static function delete($index)
    {
        if(self::$memcache !== false)
            memcache_delete(self::$memcache, $index);
    }

    /**
     * Limpa o cache de memória.
     */
    public static function flush()
    {
        // Limpa o cache
        if(self::$memcache !== false)
            memcache_flush(self::$memcache);
    }
}
