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

        // Se a sessão estiver desligada, então ativa a mesma.
        if(session_status() == PHP_SESSION_NONE)
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
        return (($this->__isset($name)) ? $this->decrypt($_SESSION[$this->encrypt($name)]) : null);
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
        $_SESSION[$this->encrypt($name)] = $this->encrypt($value);
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
        return isset($_SESSION[$this->encrypt($name)]);
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
            unset($_SESSION[$this->encrypt($name)]);
    }

    public function offsetUnset($name)
    {
        $this->__unset($name);
    }

    /**
     * Remove a criptografia da string aplicada.
     *
     * @param string $data Dados criptografados
     *
     * @return string
     */
    private function decrypt($data)
    {
        return ((BRACP_SESSION_SECURE && extension_loaded("openssl"))
            ? @openssl_decrypt($data, BRACP_SESSION_ALGO, base64_decode(BRACP_SESSION_KEY), 0, base64_decode(BRACP_SESSION_IV))
                : $data);
    }

    /**
     * Criptografa os dados utilizando informações do openssl.
     *
     * @param string $data Dados a serem criptografados.
     *
     * @return string
     */
    private function encrypt($data)
    {
        return ((BRACP_SESSION_SECURE && extension_loaded("openssl"))
            ? @openssl_encrypt($data, BRACP_SESSION_ALGO, base64_decode(BRACP_SESSION_KEY), 0, base64_decode(BRACP_SESSION_IV))
                : $data);
    }
}
