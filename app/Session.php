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
 * Classe para controlar os dados de sessão do painel de controle.
 *
 * @author CarlosHenrq
 */
class Session implements ArrayAccess
{
    /**
     * Inicializador da classe de sessões.
     */
    public function __construct()
    {
        session_cache_limiter(false);
        session_start();
    }

    /**
     * Obtém o atributo de acordo com a sessão do usuário.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return (($this->__isset($name)) ? $_SESSION[$name] : null);
    }

    public function offsetGet($name)
    {
        return $this->__get($name);
    }

    /**
     * Define o atributo de acordo com a sessão do usuário.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }

    /**
     * Verifica se a váriavle de sessão está definida.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }

    public function offsetExists($name)
    {
        return $this->__isset($name);
    }

    /**
     * Remove uma variavel da sessão.
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if($this->__isset($name))
            unset($_SESSION[$name]);
    }

    public function offsetUnset($name)
    {
        $this->__unset($name);
    }
}
