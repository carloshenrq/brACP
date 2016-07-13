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
interface ICache
{
    /**
     * Realiza a leitura de uma chave no cache.
     *
     * @param string $key Chave no cache.
     *
     * @return mixed Retorna os dados em cache. Caso não exista a chave, retorna false.
     */
    public function read($key);

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
    public function write($key, $value, $time = 600, $force = false);

    /**
     * Apaga um arquivo de acordo com a chave informada no cache.
     *
     * @param string $key
     *
     * @return void
     */
    public function erase($key);

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
    public function parse($key, $value, $time = 600, $force = false);
}
