<?php
/**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2016  brAthena, CHLFZ
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

// Espertinhos poderiam mandar uma requisição para cá para trocar dados de instalação,
// Este arquivo é exclusivo para quando não existe dados de instalação ainda realizados.
if(file_exists('config.php'))
    exit;

// Define o retorno como json.
header('Content-Type: application/json');

// Recebe os dados de instalação.
$post = json_decode(file_get_contents('php://input'));

// Recebeu os dados de instalação do outro lado. Inicializa testes de instalação e
//  devolve os retornos especificos.
if(!empty($post))
{
    // Valida o teste de conexão com o banco de dados do brACP.
    // Caso não seja possível realizar a conexão, será devolvido a tela
    // A Mensagem de erro de conexão com o banco de dados.
    if(($msg = pdo_test($post->BRACP_SQL_CP_HOST, $post->BRACP_SQL_CP_DBNAME, $post->BRACP_SQL_CP_USER, $post->BRACP_SQL_CP_PASS)) !== true)
    {
        echo json_encode([
            'status'    => '1',
            'local'     => 'SQL - brACP',
            'message'   => $msg,
        ]);
    }
    // Valida o teste de conexão com o banco de dados dos itens do servidor.
    // Caso não seja possível realizar a conexão, será devolvido a tela
    // A Mensagem de erro de conexão com o banco de dados.
    else if(($msg = pdo_test($post->BRACP_SQL_DB_HOST, $post->BRACP_SQL_DB_DBNAME, $post->BRACP_SQL_DB_USER, $post->BRACP_SQL_DB_PASS)) !== true)
    {
        echo json_encode([
            'status'    => '1',
            'local'     => 'SQL - Database',
            'message'   => $msg,
        ]);
    }
    else
    {
        $bServersPast = true;
        foreach($post->BRACP_SERVERS as $i => $server)
        {
            if(($msg = pdo_test($server->sql->host, $server->sql->dbname, $server->sql->user, $server->sql->pass)) !== true)
            {
                $bServersPast = false;
                echo json_encode([
                    'status'    => '1',
                    'local'     => "SQL - Servidores ({$i} - {$server->name})",
                    'message'   => $msg,
                ]);
                break;
            }
        }

        // Se todos os testes de servidores de banco derem sucesso inicia a gravação do arquivo
        // De configuração no disco local.
        if($bServersPast)
        {
            // Inicializa a variavel de configuração.
            $_config = [];

            $_config[] = '<?php';
            $_config[] = '/**';
            $_config[] = ' * Este arquivo de instalação foi gerado pelo programa de instalação do brACP ';
            $_config[] = ' * Esta cópia do brACP foi instalada em ' . date( 'Y-m-d H:i:s' );
            $_config[] = ' * ---- Versão do brACP:   ' . $post->BRACP_VERSION;
            $_config[] = ' * ---- URL de Instalação: ' . $post->BRACP_URL;
            $_config[] = ' */';
            $_config[] = '';

            $_tmpServerDefault = 0;
            $_tmpServerCount = 0;

            foreach($post as $k => $data)
            {
                // Se a variavel de instalação não for do brACP, então
                // Ignora o indice.
                // if(!preg_match('/^BRACP_', $k))
                //     continue;

                if(!is_array($data))
                {
                    if(preg_match('/^([0-9]+)$/', $data) || preg_match('/^(true|false)$/i', $data))
                        $_config[]   = "DEFINE('{$k}', {$data}, false);";
                    else
                        $_config[]   = "DEFINE('{$k}', '" . addslashes($data) . "', false);";
                }
                else
                {
                    // @Todo: Gravação de dados de array como por exemplo o BRACP_SERVERS.
                    if($k == 'BRACP_SERVERS')
                    {
                        foreach($data as $i => $server)
                        {
                            $sql = $server->sql;
                            $sub = $server->servers;

                            $_config[] = "DEFINE('BRACP_SRV_{$i}_NAME', '" .         addslashes($server->name) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_LOGIN_IP', '" .     addslashes($sub->login->address) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_LOGIN_PORT', '" .   addslashes($sub->login->port) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_CHAR_IP', '" .      addslashes($sub->char->address) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_CHAR_PORT', '" .    addslashes($sub->char->port) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_MAP_IP', '" .       addslashes($sub->map->address) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_MAP_PORT', '" .     addslashes($sub->map->port) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_SQL_DRIVER', '" .   addslashes($sql->driver) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_SQL_HOST', '" .     addslashes($sql->host) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_SQL_USER', '" .     addslashes($sql->user) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_SQL_PASS', '" .     addslashes($sql->pass) . "', false);";
                            $_config[] = "DEFINE('BRACP_SRV_{$i}_SQL_DBNAME', '" .   addslashes($sql->dbname) . "', false);";

                            if(intval($server->default) == 1)
                                $_tmpServerDefault = $i;

                            $_tmpServerCount++;
                        }
                    }
                }
            }

            $_config[] = '';
            $_config[] = "DEFINE('BRACP_SRV_DEFAULT', {$_tmpServerDefault}, false);";
            $_config[] = "DEFINE('BRACP_SRV_COUNT', {$_tmpServerCount}, false);";
            $_config[] = '';
            $_config[] = '// Fim da instalação do brACP.';
            $_config[] = '';

            file_put_contents('config.php', implode(PHP_EOL, $_config));

            $install_config = file_get_contents('config.php');

            echo json_encode([
                'status'    => 0,
                'message'   => 'Instalação realizada com sucesso! Seu navegador será atualizado.',
                'install'   => highlight_string($install_config, true),
            ]);
            exit;

        }
    }
}
else
{
    echo json_encode([
        'status'    => '1',
        'local'     => 'Dados de Instalação',
        'message'   => 'Nenhum dado de instalação foi recebido.',
    ]);
    exit;
}

/**
 * Realiza o teste de conexão com os dados de pdo informados.
 *
 * @param string $host
 * @param string $db
 * @param string $user
 * @param string $pass
 *
 * @return bool Verdadeiro caso consiga conectar com sucesso.
 */
function pdo_test($host, $db, $user, $pass)
{
    try
    {
        $pdo = new PDO("mysql:dbname={$db};host={$host}", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo = null;
        return true;
    }
    catch(Exception $ex)
    {
        return $ex->getMessage();
    }
}
