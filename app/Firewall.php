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
 * Classe para tratamento de informações relacionadas ao firewall.
 */
class Firewall extends brACPMiddleware
{
    /**
     * String que armazena o useragent.
     *
     * @return string
     */
    private $userAgent = null;

    /**
     * String que armazena o endereço ip do jogador.
     *
     * @return string
     */
    private $ipAddress = null;

    /**
     * Conexão com o sqlite local. Não será usado doctrine, aqui precisamos de speed.
     *
     * @var \PDO
     */
    private $sqlite = null;

    /**
     * Inicializa o middleware de firewall
     */
    protected function init()
    {
        // Define o objeto de firewall da aplicação.
        $this->getApp()->setFirewall($this);

        $needImport = !file_exists('firewall.db'); // Verifica se é necessário importar as tabelas.

        try
        {
            // Inicializa a conexão com o banco de dados local do sqlite.
            // -> Conexão é persistente (gerenciado pelo apache, apenas e abertura do arquivo)
            $this->sqlite = new \PDO('sqlite:firewall.db', null, null, [
                PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT    => true,
            ]);

            // Verifica se é necessarío importar os dados.
            if($needImport)
            {
                // Inicializa as transações.
                $this->sqlite->beginTransaction();

                // Dados de firewall
                $sqlite_db = file_get_contents(__DIR__ . '/../sql-files/bracp-firewall-sqlite.sql');
                $sqlite_queries = explode(';', $sqlite_db);

                // Varre as querys e cria o banco de dados necessário.
                foreach($sqlite_queries as $query)
                    $this->sqlite->query($query);

                // Salva as alterações no banco de dados.
                $this->sqlite->commit();
            }

            // Apaga todas as requests feitas a mais de 7 dias para não encher o banco de dados.
            $stmt = $this->sqlite->prepare('DELETE FROM request WHERE RequestTime < :RequestTime');
            $stmt->execute([
                ':RequestTime' => time() - (60*60*24*7)
            ]);
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
        }

        return;
    }

    /**
     * Adiciona um endereço ip a lista negra do painel de controle.
     *
     * @param string $ipAddress Endereço ip para adicionar na lista negra.
     * @param string $reason Motivo para entrar na lista negra.
     * @param int $expire Tempo para expirar. (-1 para eterno)
     *
     * @return void
     */
    public function addBlackList($ipAddress, $reason, $expire = 3600)
    {
        try
        {
            // Executa a query para inserir um endereço ip na lista negra.
            $stmt_blacklist = $this->sqlite->prepare('
                INSERT INTO
                    blacklist
                (Address, Reason, TimeBlocked, TimeExpire, Permanent)
                    VALUES
                (:Address, :Reason, :TimeBlocked, :TimeExpire, :Permanent)
            ');
            $stmt_blacklist->execute([
                ':Address'      => $ipAddress,
                ':Reason'       => $reason,
                ':TimeBlocked'  => time(),
                ':TimeExpire'   => (($expire == -1) ? 0 : time() + $expire),
                ':Permanent'    => ($expire == -1)
            ]);
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
        }

        return;
    }

    /**
     * Verifica se o ip do cliente está em lista negra.
     *
     * @param string $ipAddress
     *
     * @return bool Verdadeiro se estiver em lista negra.
     */
    public function isBlackListed($ipAddress)
    {
        try
        {
            // Select para verificar o endereço ip do jogador.
            $stmt_blacklist = $this->sqlite->prepare('
                SELECT
                    COUNT(AddressID) as IsListed
                FROM
                    blacklist
                WHERE
                    Address = :Address
                        AND
                    ((TimeExpire > 0 AND TimeExpire < :TimeExpire)
                        OR 
                    (Permanent = 1))
            ');
            $stmt_blacklist->execute([
                ':Address'      => $ipAddress,
                ':TimeExpire'   => time() + 3600
            ]);
            $obj_blacklist = $stmt_blacklist->fetchObject();

            // Retorna informações se o endereço ip está listado no banco de dados.
            return ($obj_blacklist->IsListed > 0);
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
            return false;
        }
    }

    /**
     * Obtém o useragent utilizado para as requisições.
     *
     * @return string
     */
    public function getUserAgent()
    {
        if(!is_null($this->userAgent)) // Se estiver prenchido, não é necessário ler novamente...
            return $this->userAgent;
        
        return ($this->userAgent = $this->getApp()->getContainer()->get('request')->getHeader('user-agent')[0]);
    }

    /**
     * Obtém o endereço ip do jogador.
     *
     * @return string
     */
    public function getIpAddress()
    {
        if(!is_null($this->ipAddress)) // Se estiver prenchido, não é necessário ler novamente...
            return $this->ipAddress;

        // Inicializa o endereço de ip do jogador como
        // Desconhecido.
        $ipAddress = '?.?.?.?';

        // Possiveis variaveis para se obter o endereço ip do cliente.
        // issue #10: HTTP_CF_CONNECTING_IP-> Usuário usando proteção do cloudfire.
        $_vars = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                  'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

        // Varre as opções para retornar os dados ao painel de controle.
        foreach($_vars as $ip)
        {
            if(getenv($ip) !== false)
            {
                $ipAddress = getenv($ip);
                break;
            }
        }

        return ($this->ipAddress = $ipAddress);
    }

    /**
     * Método para criar os logs necessários para informações do IP como:
     * -> Cidade, pais, etc... (Os dados podem não ser completamente precisos, é mais para estatistica de regiões e etc...)
     */
    private function logIpDetails()
    {
        // Configuração desativada, não é necessário finalizar informações de log.
        if(!BRACP_LOG_IP_DETAILS)
            return;

        // Obtém o endereço ip do jogador.
        $ipAddress = $this->getIpAddress();
        $userAgent = $this->getUserAgent();

        // @Todo: Salvar informações do endereço ip para o jogador.

        return;
    }

    /**
     * @see brACPMiddleware::__invoke()
     */
    public function __invoke($request, $response, $next)
    {
        try
        {
            $ipAddress = $this->getIpAddress();
            $userAgent = $this->getUserAgent();

            // Se o endereço de ip está na lista negra, retorna mensagem de erro
            // Informando que não se pode conectar devido a restrição.
            if($this->isBlackListed($ipAddress))
            {
                $this->getApp()->display('error.403');
                return $response;
            }

            // Realiza uma verificação para saber se o endereço ip da requisição está
            // Fazendo requisições com tempo inferior a 5s, neste caso, ao passar de
            // 30 Requisições na contagem, o ip será adicionado a lista de banidos.
            $serverTime     = microtime(true);
            $serverCompare  = $serverTime - 5;

            // Query para verificar a quantidade de requisições executadas.
            $stmt_request = $this->sqlite->prepare('
                SELECT
                    COUNT(RequestID) as CountRequest
                FROM
                    request
                WHERE
                    Address = :Address
                        AND
                    ServerTime >= :ServerTimeLess
            ');
            $stmt_request->execute([
                ':Address'          => $ipAddress,
                ':ServerTimeLess'   => $serverCompare,
            ]);
            $obj_request = $stmt_request->fetchObject();

            // Se o count estiver acima de 40, então, adiciona o ip a lista negra.
            if($obj_request->CountRequest >= 40)
            {
                $this->addBlackList($ipAddress, 'Too many requests for to short time.');
                $this->getApp()->display('error.403');
                return $response;
            }

            // Requisições enviadas aos assets não serão gravadas.
            if(!preg_match('/asset/i', $_SERVER['REQUEST_URI']))
            {
                // Salva a requisição atual na tabela de requisições.
                $stmt = $this->sqlite->prepare('
                    INSERT INTO
                        request
                    (Address, UserAgent, RequestTime, ServerTime, Method, Scheme, URI, Filename, PHPSession)
                        VALUES
                    (:Address, :UserAgent, :RequestTime, :ServerTime, :Method, :Scheme, :URI, :Filename, :PHPSession)
                ');
                $stmt->execute([
                    ':Address'      => $ipAddress,
                    ':UserAgent'    => $userAgent,
                    ':RequestTime'  => $_SERVER['REQUEST_TIME'],
                    ':ServerTime'   => $serverTime,
                    ':Method'       => $_SERVER['REQUEST_METHOD'],
                    ':Scheme'       => $_SERVER['REQUEST_SCHEME'],
                    ':URI'          => $_SERVER['REQUEST_URI'],
                    ':Filename'     => $_SERVER['SCRIPT_FILENAME'],
                    ':PHPSession'   => $this->getApp()->getSession()->getId(),
                ]);
            }

            // Grava os detalhamentos de endereço ip.
            $this->logIpDetails();
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
        }

        return parent::__invoke($request, $response, $next);
    }
}



