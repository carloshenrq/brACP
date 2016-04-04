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
 * Classe para a escrita dos logs em arquivo local.
 */
class LogWriter
{
    /**
     * Escreve um log do tipo informações. (0)
     *
     * @param string $message
     *
     * @return boolean
     */
    private static function info($message)
    {
        return self::write2file($message, 'info.log');
    }

    /**
     * Escreve log do tipo alerta. (1)
     *
     * @param string $message
     *
     * @return boolean
     */
    private static function warning($message)
    {
        return self::write2file($message, 'warning.log');
    }

    /**
     * Escreve log do tipo erro. (2)
     *
     * @param string $message
     *
     * @return boolean
     */
    private static function error($message)
    {
        return self::write2file($message, 'error.log');
    }

    /**
     * Método para escrever os dados em um arquivo.
     *
     * @param string $message
     * @param string $file
     *
     * @return boolean
     */
    private static function write2file($message, $file)
    {
        $content = '';

        if(file_exists($file))
            $content = file_get_contents($file);

        return file_put_contents($file, $message . "\n" . $content) > 0;
    }

    /**
     * Escreve informações do log em um arquivo local para auditoria.
     *
     * @param string $message
     * @param int $type
     * @param string $file
     *
     * @return boolean
     */
    public static function write($message, $type = 0, $file = 'global.log')
    {
        // Verifica o modo desenvolvedor. Somente escreve os logs se estiver em modo desenvolvedor.
        if(!BRACP_DEVELOP_MODE)
            return false;

        $formatedLog = date('Y-m-d H:i:s') . "    -    {$type}    -    " . PHP_VERSION . "\n";
        $formatedLog .= "PHP_INI_LOADED : ".php_ini_loaded_file() . "\n";
        $formatedLog .= "PHP_INI_SCANNED: ".php_ini_scanned_files() . "\n";
        $formatedLog .= "LOADED_EXTENSIONS: ".implode(', ', get_loaded_extensions()) . "\n";
        $formatedLog .= "CURRENT_USER: " . get_current_user() . "\n";
        $formatedLog .= "MEMORY_USAGE: " . memory_get_peak_usage() . "\n";
        $formatedLog .= "MEMORY_REAL_USAGE: " . memory_get_peak_usage(true) . "\n";
        $formatedLog .= "MEMORY_USAGE_SCRIPT: " . memory_get_usage() . "\n";
        $formatedLog .= "MEMORY_REAL_USAGE_SCRIPT: " . memory_get_usage(true) . "\n";
        $formatedLog .= "\n";
        $formatedLog .= "---> MESSAGE:\n";
        $formatedLog .= "{$message}\n";
        $formatedLog .= "=====================================================================================\n";

        // Se conseguir escrever o log global, então tenta escrever o log defivo.
        if(self::write2file($formatedLog, $file))
        {
            switch($type)
            {
                case 1: return self::warning($formatedLog);
                    break;
                case 2: return self::error($formatedLog);
                case 0:
                default: return self::info($formatedLog);
            }
        }

    }
}


