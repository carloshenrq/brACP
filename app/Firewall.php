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
                PDO::ATTR_PERSISTENT    => !BRACP_DEVELOP_MODE,
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

            // Apaga todas as entradas dentro do safelist que já estão "vencidas"
            $stmt = $this->sqlite->prepare('DELETE FROM safelist WHERE ExpireTime < :ExpireTime');
            $stmt->execute([
                ':ExpireTime' => time()
            ]);
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
        }

        return;
    }

    /**
     * Adiciona uma regra ao firewall
     *
     * @param int $type (0: Regra para endereço IP, 1: Regra para UserAgent, 2: Regra para Paises)
     * @param string $rule Dados a serem validados.
     *                     Se type = 0, enviar endereço ip.
     *                     Se type = 1, enviar useragent
     *                     Se type = 2, enviar pais.
     * @param bool $enabled Define se a regra estará habilitada.
     */
    public function ruleAdd($type, $rule)
    {
        try
        {
            $stmt = $this->sqlite->prepare('
                INSERT INTO
                    rules
                VALUES
                    (NULL, :Type, 1, :Rule)
            ');
            $stmt->execute([
                ':Type'     => $type,
                ':Rule'     => preg_quote($rule),
            ]);

            $this->sqlite->query('DELETE FROM safelist;');
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
        }

        return;
    }

    /**
     * Habilita ou desabilita uma regra no firewall.
     *
     * @param int $RuleID
     */
    public function ruleEnable($RuleID, $Enable)
    {
        try
        {
            $stmt = $this->sqlite->prepare('
                UPDATE
                    rules
                SET
                    Enabled = :Enabled
                WHERE
                    RuleID = :RuleID
            ');
            $stmt->execute([
                ':Enabled'      => $Enable,
                ':RuleID'       => $RuleID
            ]);

            $this->sqlite->query('DELETE FROM safelist;');
       }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
        }
    }

    /**
     * Verifica se o endereço está na safelist para não ser necessário
     * Realizar os testes a toda requisição de regras.
     *
     * @param string $ipAddress
     *
     * @return bool Se verdadeiro, está na safelist.
     */
    public function isSafeList($ipAddress)
    {
        $stmt = $this->sqlite->prepare('
            SELECT COUNT(Address) as IsSafeList FROM safelist WHERE Address = :Address AND ExpireTime > :ExpireTime
        ');
        $stmt->execute([
            ':Address'      => $ipAddress,
            ':ExpireTime'   => time(),
        ]);
        $obj_count = $stmt->fetchObject();

        return ($obj_count->IsSafeList > 0);
    }

    /**
     * Adiciona um endereço ip aos endereços seguros por 10 minutos.
     *
     * @param string $ipAddress
     */
    public function addToSafeList($ipAddress)
    {
        $stmt = $this->sqlite->prepare('
            INSERT INTO
                safelist
            VALUES
                (:Address, :ServerTime, :ExpireTime)
        ');
        $stmt->execute([
            ':Address'      => $ipAddress,
            ':ServerTime'   => time(),
            ':ExpireTime'   => time() + (60*10),
        ]);
    }

    /**
     * Verifica se o endereço ip ou useragent ou até mesmo informações
     * De endereço do jogador estão nas regras do firewall.
     *
     * @param string $ipAddress
     * @param string $userAgent
     *
     * @return boolean Retorna verdadeiro se a regra para o endereço/useragent foi encontrada.
     */
    public function checkRules($ipAddress, $userAgent)
    {
        try
        {
            // Seleciona todas as regras presentes no firewall para
            // Ir testando de acordo com a necessidade.
            $stmt = $this->sqlite->query('
                SELECT
                    RuleID,
                    Type,
                    Rule
                FROM
                    rules
                WHERE
                    Enabled = 1
            ');
            $ds_rules = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Obtém todas as regras para testes de endereço ip e useragent.
            $rulesForIpAg = array_filter($ds_rules, function($obj) {
                return ($obj->Type == 0 || $obj->Type == 1);
            });
            // Obtém todas as regras para testes de endereços.
            $rulesForAd = array_filter($ds_rules, function($obj) {
                return ($obj->Type == 2);
            });

            // Começa realizando os testes para endereço ip do jogador.
            // Caso o endereço ip seja encontrado na lista, é retornado
            foreach($rulesForIpAg as $obj)
            {
                if(($obj->Type == 0 && preg_match($obj->Rule, $ipAddress))
                    || ($obj->Type == 1 && preg_match($obj->Rule, $userAgent)))
                    return $obj->RuleID;
            }

            // Verifica se existem entradas na tabela de endeçamento para gravar
            // Os detalhes de endereços do ip solicitado.
            $stmt = $this->sqlite->prepare('
                SELECT
                    *
                FROM
                    ip_data
                WHERE
                    Address         = :Address AND
                    ServerTime      > :ServerTime
            ');
            $stmt->execute([
                ':Address'      => $ipAddress,
                ':ServerTime'   => time() - (60*60*24),
            ]);
            $obj_data = $stmt->fetchObject();

            // Não foram encontrados dados para o endereço de ip solictado,
            // Então, uma requisição é realizada ao ipinfo.io Para salvar na
            // Tabela os dados do endereço ip.
            if($obj_data === false)
            {
                $aParams = [
                    ':IpAddress'    => $ipAddress,
                    ':Hostname'     => 'intranet',
                    ':City'         => 'intranet',
                    ':Region'       => 'intranet',
                    ':Country'      => 'intranet',
                    ':Location'     => 'intranet',
                    ':Origin'       => 'intranet',
                    ':ServerTime'   => time(),
                    ':GMT'          => date_default_timezone_get(),
                ];

                // Obtém os dados do webservice para gravar no banco de dados.
                $ipDetails = json_decode(Request::create('http://ipinfo.io/')
                    ->get($ipAddress)->getBody()->getContents());

                if(!isset($ipDetails->bogon) || $ipDetails->bogon != 1)
                {
                    $aParams = array_merge($aParams, [
                        ':Hostname'     => $ipDetails->hostname,
                        ':City'         => $ipDetails->city,
                        ':Region'       => $ipDetails->region,
                        ':Country'      => $ipDetails->country,
                        ':Location'     => $ipDetails->loc,
                        ':Origin'       => $ipDetails->org
                    ]);
                }

                $stmt = $this->sqlite->prepare('
                    INSERT INTO
                        ip_data
                    VALUES
                        (NULL, :IpAddress, :Hostname, :City, :Region, :Country, :Location, :Origin, :ServerTime, :GMT)
                ');
                $stmt->execute($aParams);
                $LogID = $this->sqlite->lastInsertId();

                // Verifica se existem entradas na tabela de endeçamento para gravar
                // Os detalhes de endereços do ip solicitado.
                $stmt = $this->sqlite->prepare('
                    SELECT
                        *
                    FROM
                        ip_data
                    WHERE
                        LogID         = :LogID
                ');
                $stmt->execute([
                    ':LogID'      => $LogID,
                ]);
                $obj_data = $stmt->fetchObject();
            }

            // Se houver registros para teste de endereço, então,
            // Procura nos dados retornados a regra informada.
            if(count($rulesForAd) > 0)
            {
                foreach($rulesForAd as $rule)
                {
                    // Procura a regra informada nos campos:
                    // Country, Region, City e Hostname
                    // Se encontrar, retorna como regra validada.
                    if(preg_match($rule->Rule, $obj_data->Country)
                        || preg_match($rule->Rule, $obj_data->Region)
                        || preg_match($rule->Rule, $obj_data->City)
                        || preg_match($rule->Rule, $obj_data->Hostname))
                        return true;
                }
            }

            return false;
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
            echo $ex->getMessage();
            exit;
            return false;      
        }
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
    public function addToBlackList($ipAddress, $reason, $expire = 3600, $rule = null)
    {
        try
        {
            // Executa a query para inserir um endereço ip na lista negra.
            $stmt_blacklist = $this->sqlite->prepare('
                INSERT INTO
                    blacklist
                (Address, Reason, TimeBlocked, TimeExpire, Permanent, RuleID)
                    VALUES
                (:Address, :Reason, :TimeBlocked, :TimeExpire, :Permanent, :RuleID)
            ');
            $stmt_blacklist->execute([
                ':Address'      => $ipAddress,
                ':Reason'       => $reason,
                ':TimeBlocked'  => time(),
                ':TimeExpire'   => (($expire == -1) ? 0 : time() + $expire),
                ':Permanent'    => ($expire == -1),
                ':RuleID'       => $rule,
            ]);
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
        }

        return;
    }

    /**
     * Remove uma regra da blacklist apenas se ela não tiver sido adicionada
     * Através de uma regra.
     *
     * @param int $AddressID Código incremento do endereço na blacklist.
     */
    public function delFromBlackList($AddressID)
    {
        try
        {
            $stmt = $this->sqlite->prepare('
                DELETE FROM
                    blacklist
                WHERE
                    AddressID = :AddressID AND
                    RuleID IS NULL
            ');
            $stmt->execute([
                ':AddressID'    => $AddressID
            ]);

            return true;
        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
            return false;
        }
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
        // Obtém o endereço ip do jogador.
        $ipAddress = $this->getIpAddress();
        $userAgent = $this->getUserAgent();

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
                $this->getApp()->display('error.403', [
                    'reason'    => 'you address (' . $ipAddress . ') is blacklisted here.'
                ]);
                return $response;
            }

            // Verifica se o endereço ip está numa safelist
            // Se estiver, não é necessário fazer os testes de regras novamente.
            // O Safelist somente deve testado a cada 10m ou quando as regras forem alteradas.
            if(!$this->isSafeList($ipAddress))
            {
                // Verifica as regras de firewall para os dados que o jogador
                // Está acessando.
                $ruleCheck = $this->checkRules($ipAddress, $userAgent);

                //  -> BRACP_FIREWALL_RULEMODE
                //  Se a configuração estiver definida em 0:
                //      Usará as regras somente para bloquear o acesso.
                //      No caso, quando os dados enviados encontrarem uma regra,
                //      será usada para bloquear.
                //   Se a configuração estiver definida em 1:
                //      Usará as regras somente para permitir o acesso.
                //      No caso, quando os dados enviados emcpmtrar, uma regra,
                //      será usada para permitir.
                if(($ruleCheck && !BRACP_FIREWALL_RULEMODE) || (!$ruleCheck && BRACP_FIREWALL_RULEMODE))
                {
                    // Adiciona o endereço ip ao blacklist.
                    // Na próxima tentativa do endereço ip com o firewall
                    $this->addToBlackList($ipAddress, (BRACP_FIREWALL_RULEMODE == 0 ? 'Rule mached!' : 'No rules to allow this connection.'), -1, $ruleCheck);

                    $this->getApp()->display('error.403', [
                        'reason'    => 'you address (' . $ipAddress . ') is blacklisted here.'
                    ]);
                    return $response;
                }
                else
                {
                    $this->addToSafeList($ipAddress);
                }
            }

            // @Todo: Adicionar os testes necessários para tratamento de requisições.

        }
        catch(Exception $ex)
        {
            $this->getApp()->logException($ex);
            return $response;
        }

        return parent::__invoke($request, $response, $next);
    }
}



