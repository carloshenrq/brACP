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
    private static $cache = false;

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
        // [2016-08-02, CHLFZ] Adicionado teste porque quando o brACP não está instado a variável não está definida.
        if(defined('BRACP_CACHE') && BRACP_CACHE && !BRACP_DEVELOP_MODE)
            self::$cache = new LocalCache(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache');
    }

    /**
     * Obtém os dados do cache da aplicação.
     *
     * @param mixed $index
     * @param mixed $defaultValue
     *
     * @return $mixed
     */
    public static function get($index, $defaultValue, $force = false, $cache = null)
    {
        // [2016-10-10, CHLFZ] Problemas durante a instalação, leitura das pastas sem a definição
        //                     estava causando um pequeno problema durante a execução.
        if(is_null($cache))
            $cache = (defined('BRACP_CACHE_EXPIRE') ? BRACP_CACHE_EXPIRE : 600);

        // [2016-08-02, CHLFZ] Adicionado teste porque quando o brACP não está instado a variável não está definida.
        if(!defined('BRACP_CACHE') || !BRACP_CACHE || BRACP_DEVELOP_MODE)
            return ((is_callable($defaultValue)) ? $defaultValue() : $defaultValue);

        return self::$cache->parse($index, $defaultValue, $cache, $force);
    }

    /**
     * Deleta os indices de memória do memcache.
     *
     * @param mixed $index
     */
    public static function delete($index)
    {
        // [2016-08-02, CHLFZ] Adicionado teste porque quando o brACP não está instado a variável não está definida.
        if(!defined('BRACP_CACHE') || !BRACP_CACHE|| BRACP_DEVELOP_MODE)
            return;

        return self::$cache->erase($index);
    }
}
