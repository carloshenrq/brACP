<?php

/**
 * Classe para gerênciar todas as conexões e requisições realizadas
 *  na aplicaçao.
 */
class AppFirewall extends AppMiddleware
{
    /**
     * Endereço ip de quem está realizando o acesso.
     * @var string
     */
    private $ipAddress;

    /**
     * UserAgent de quem está realizando o acesso.
     * @var string
     */
    private $userAgent;

    /**
     * Regras presentes no no firewall para realizar tratamento.
     * @var array
     */
    private $rules;

    /**
     * Código da requisição que foi gravada no banco de dados. 
     * @var int
     */
    private $requestId;

    /**
     * @see AppMiddleware::init()
     */
    protected function init()
    {
        // Define o objeto de firewall como sendo
        // Este objeto.
        $this->getApp()->setFirewall($this);

        // Instalação das tabelas em AppSQLite->installFirewall()

        $this->ipAddress = null;
        $this->rules = [];

        // Realiza um select nas regras para criar as entradas necessárias.

    }

    /**
     * Obtém o UserAgent do naveador do usuário.
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
     * Obtém o endereço ip do usuário.
     *
     * @return string
     */
    public function getIpAddress()
    {
        // Se o endereço ip já tiver sido obtido alguma vez
        // Retorna o endereço ip.
        if(!empty($this->ipAddress))
            return $this->ipAddress;

        // Define o endereço ip como padrão de '?.?.?.?'
        $this->ipAddress = '?.?.?.?';

        // Possiveis variaveis para se obter o endereço ip do cliente.
        // issue #10: HTTP_CF_CONNECTING_IP-> Usuário usando proteção do cloudfire.
        $_vars = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                  'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

        // Varre as opções para retornar os dados ao painel de controle.
        foreach($_vars as $ip)
        {
            if(getenv($ip) !== false)
            {
                $this->ipAddress = getenv($ip);
                break;
            }
        }

        // Retorna o endereço 
        return $this->getIpAddress();
    }

    /**
     * Obtém os detalhes do endereço ip solicitado.
     *
     * @param string $ipAddress Endereço ip a ser verificado. 
     *
     * @return object Informações do ip solicitado.
     */
    public function getIpDetails($ipAddress = null)
    {
        // Caso não seja informado os detalhes para os endereços de ip
        // Será utilizado os da requisição atual.
        if(is_null($ipAddress)) $ipAddress = $this->getIpAddress();

        $stmt_data = $this->getApp()->getSqlite()->prepare('
            SELECT
                Address,
                Hostname,
                City,
                Region,
                Country,
                Location,
                Origin
            FROM
                firewall_ipdata
            WHERE
                Address = :Address
                    AND
                ServerTime > :Yesterday
        ');
        $stmt_data->execute([
            ':Address'      => $ipAddress,
            ':Yesterday'    => (time() - 86400)
        ]);

        // Não foram encontrados dados relacionados ao endereço ip solicitado.
        if(($obj_data = $stmt_data->fetchObject()) === false)
        {
            // Prepara os parametros para inserir informações
            // no banco de dados.
            $params = [
                ':Address'      => $ipAddress,
                ':Hostname'     => 'intranet',
                ':City'         => 'intranet',
                ':Region'       => 'intranet',
                ':Country'      => 'intranet',
                ':Location'     => 'intranet',
                ':Origin'       => 'intranet',
                ':ServerTime'   => time(),
                ':GMT'          => date_default_timezone_get(),
            ];

            // Obtém os detalhes de endereço ip.
            $ipDetails = json_decode($this->getApp()->getHttpClient()
                                ->createClient()
                                ->get('http://ipinfo.io/'.$ipAddress.'/json')
                                ->getBody()
                                ->getContents());
            
            // Verifica se não é um ip de rede interna.
            if(!isset($ipDetails->bogon) || $ipDetails->bogon == false)
            {
                $params = array_merge($params, [
                    ':Hostname' => $ipDetails->hostname,
                    ':City'     => $ipDetails->city,
                    ':Region'   => $ipDetails->region,
                    ':Country'  => $ipDetails->country,
                    ':Location' => $ipDetails->loc,
                    ':Origin'   => $ipDetails->org
                ]);
            }

            // Insere no banco de dados os dados solicitados.
            $stmt_ipdata = $this->getApp()->getSqlite()->prepare('
                REPLACE INTO
                    firewall_ipdata
                VALUES
                    (NULL, :Address, :Hostname, :City, :Region, :Country, :Location, :Origin, :ServerTime, :GMT)
            ');
            $stmt_ipdata->execute($params);

            // Como realizou o insert e agora existem parametros para retornar,
            // Faz uma chamada recursiva para obter os dados.
            return $this->getIpDetails($ipAddress);
        }

        // Retorna os dados do endereço ip informado.
        return $obj_data;
    }

    /**
     * Verifica se o endereço ip está na lista negra de acesso
     * ao sistema. 
     *
     * @param string $ipAddress (Se enviado NULL, usa o da conexão atual)
     *
     * @return boolean|array Falso caso não esteja listado ou um array com todas as ocorrências.
     */
    public function isBlackListed($ipAddress = null)
    {
        // Se enviado null, usará o endereço ip da conexão atual.
        if(is_null($ipAddress)) $ipAddress = $this->getIpAddress();

        // Faz o select na tabela para verificar a ocorrência
        // dos bloqueios.
        $stmt_blacklist = $this->getApp()->getSqlite()->prepare('
            SELECT
                Address,
                Reason,
                TimeBlocked,
                TimeExpire,
                Permanent
            FROM
                firewall_blacklist
            WHERE
                Address = :Address
                    AND
                (Permanent = 1 OR TimeExpire > :TimeNow)
            ORDER BY
                Permanent DESC,
                TimeBlocked ASC,
                TimeExpire ASC
        ');
        $stmt_blacklist->execute([
            ':Address'  => $ipAddress,
            ':TimeNow'  => time(),
        ]);
        $blackListData = $stmt_blacklist->fetchAll(PDO::FETCH_OBJ);

        // Retorna falso para informar que não foi listado.
        // Retorna o array dos bloqueios.
        return (count($blackListData) == 0 ? false : $blackListData);
    }

    /**
     * @see AppMiddleware::__invoke()
     */
    public function __invoke($request, $response, $next)
    {
        // Define a data e hora inicial para o inicio da requisição.
        $this->getApp()->setStartRequestTime(floatval($request->getServerParams()['REQUEST_TIME_FLOAT']));

        // Verifica se o firewall está permitido a gravar ações
        // O Firewall ligado, pode reduzir o tempo de resposta o brACP.
        if(APP_FIREWALL_ALLOWED)
        {
            // Verifica se o endereço ip está na listagem de bloqueios.
            if(($blackList = $this->isBlackListed()) !== false)
            {
                // Exibe informaçoes de bloqueio do firewall.
                $this->getApp()->getView()->show('firewall.blacklist.tpl',[
                    'ipAddress' => $this->getIpAddress(),
                    'blackList' => $blackList
                ]);
                exit;
            }

            // Se o endereço ip não estiver na lista negra, então, obtém dados
            // Do endereço ip para serem verificados contra as regras colocadas
            // No banco de dados.
            $ipData = $this->getIpDetails();

            // Faz uma varredura das regras encontradas para verificações e dependendo
            // De configurações relacionadas a regra, o endereço ip é adicionado ao blacklist.
            foreach($this->rules as $rule)
            {
                // Encontra obtém os dados de callback para execução.
                $callString = $rule->Rule;
                $callback = $result = null;
                eval('$callback = ' . $callString . ';');

                // PHP 7+, Throwable só existe em versões de php 7 ou mais
                // E o Eval emite um throwable quando está em versões php7
                if(interface_exists('\Throwable'))
                {
                    try
                    {
                        eval('$callback = ' . $code . ';');
                    }
                    catch(\Throwable $exp)
                    {
                        $callback = null;
                    }
                }
                else
                {
                    if(@eval('$func = ' . $code . ';') == false)
                        $callback = null;
                }

                // Se for um callback válido, executa de acordo com as informações
                // passadas.
                if(is_callback($callback))
                {
                    // Cria o closure de função para realizar a chamada.
                    $closure = Closure::bind($callback, $this);
                    $result = $closure($ipData, $rule);

                    // Se o resultado for diferente da configuração, então
                    // Será adicionado uma nota de bloqueio. 
                    if(APP_FIREWALL_RULE_CONFIG !== $result)
                    {
                        $this->addBlackList($ipData->Address, $rule->RuleReason, $rule->RuleExpire, $rule);

                        // Exibe informaçoes de bloqueio do firewall.
                        $this->getApp()->getView()->show('firewall.blacklist.tpl',[
                            'ipAddress' => $ipData->Address,
                            'blackList' => $this->isBlackListed($ipData->Address),
                        ]);
                        exit;
                    }
                }
            }

            $time = microtime(true);

            // Conta quantas requisições o usuário fez nos últimos 5 segundos.
            $stmt_rq = $this->getApp()->getSqlite()->prepare('
                SELECT
                    COUNT(*) as CountRequest
                FROM
                    firewall_request
                WHERE
                    Address     = :Address
                        AND
                    UserAgent   = :UserAgent
                        AND
                    ServerTime  >= :ServerTime
                        AND
                    UseToBan    = 1
            ');
            $stmt_rq->execute([
                ':Address'      => $this->getIpAddress(),
                ':UserAgent'    => $this->getUserAgent(),
                ':ServerTime'   => $time - 5
            ]);
            $obj_rq = $stmt_rq->fetchObject();

            // Se o número de requisições vindas do endereço ip + userAgent for
            // Superior a 8, então adiciona o endereço ip a lista negra.
            if(intval($obj_rq->CountRequest) >= 40)
            {
                // Adiciona 1 hora de ban ao usuário.
                // 0.125s entre requisições. 
                // Acima disso, ele receberá punição e será adicionado a lista negra.
                $this->addBlackList($this->getIpAddress(), 'Muitas requisições em pouco intervalo de tempo.', 3600, null);

                // Exibe informaçoes de bloqueio do firewall.
                $this->getApp()->getView()->show('firewall.blacklist.tpl',[
                    'ipAddress' => $this->getIpAddress(),
                    'blackList' => $this->isBlackListed(),
                ]);
                exit;
            }

            // Obtém todos os dados enviados tipo get. 
            $method         = $request->getMethod();
            $path           = $request->getUri()->getPath();
            $uri            = $request->getUri()->getBasePath() . (($path !== '/') ? '/':'') . $path;
            $file           = $request->getServerParams()['SCRIPT_NAME'];
            $scheme         = $request->getServerParams()['REQUEST_SCHEME'];
            $get            = $request->getQueryParams();
            $post           = $request->getParsedBody();
            $session        = $_SESSION;
            $sessionId      = $this->getApp()->getSession()->getId();
            $requestTime    = $request->getServerParams()['REQUEST_TIME_FLOAT'];
            $useToBan       = !preg_match('/asset/i', $uri);

            // Registra a requisição na tabela de requisições. 
            $stmt_req = $this->getApp()->getSqlite()->prepare('
                INSERT INTO
                    firewall_request
                VALUES
                    (NULL, :Address, :UserAgent, :RequestTime, :ServerTime, :GMT, :Method,
                        :Scheme, :URI, :Filename, :PHPSession, :Length, :ResponseLength, :GET, :POST, :SESSION, :UseToBan)
            ');
            $stmt_req->execute([
                ':Address'          => $this->getIpAddress(),
                ':UserAgent'        => $this->getUserAgent(),
                ':RequestTime'      => $requestTime,
                ':ServerTime'       => microtime(true),
                ':GMT'              => date_default_timezone_get(),
                ':Method'           => $method,
                ':Scheme'           => $scheme,
                ':URI'              => $uri,
                ':Filename'         => $file,
                ':PHPSession'       => $sessionId,
                ':Length'           => $request->getBody()->getSize(),
                ':ResponseLength'   => 0,
                ':GET'              => base64_encode(serialize($get)),
                ':POST'             => base64_encode(serialize($post)),
                ':SESSION'          => base64_encode(serialize($session)),
                ':UseToBan'         => ($useToBan ? 1:0)
            ]);

            // Obtém o código da requisição
            $this->requestId = $this->getApp()->getSqlite()->lastInsertId();
        }

        // Move para a próxima execução.
        return parent::__invoke($request, $response, $next);
    }

    /**
     * Define e grava o tamanho da resposta para o pedido em tela. 
     *
     * @param int $responseLength
     */
    public function setResponseLength($responseLength)
    {
        // Se o firewall não estiver habilitado esta função nada fará.
        if(!APP_FIREWALL_ALLOWED)
            return;

        $stmt_req = $this->getApp()->getSqlite()->prepare('
            UPDATE
                firewall_request
            SET
                ResponseLength = :ResponseLength
            WHERE
                RequestID = :RequestID
        ');
        $stmt_req->execute([
            ':ResponseLength'   => $responseLength,
            ':RequestID'        => $this->requestId
        ]);
    }

    /**
     * Adiciona um endereço ip a lista negra.
     *
     * @param string $address Endereço ip a ser bloqueado.
     * @param string $reason Motivo de bloqueio.
     * @param int $expire Tempo em segundos para expirar. (Se -1, permanente)
     * @param object $rule Regra que causou o bloqueio.
     */
    public function addBlackList($address, $reason, $expire, $rule = null)
    {
        // Se o firewall não estiver habilitado esta função nada fará.
        if(!APP_FIREWALL_ALLOWED)
            return;

        $time = microtime(true);

        $stmt_bl = $this->getApp()->getSqlite()->prepare('
            INSERT INTO
                firewall_blacklist
            VALUES
                (NULL, :Address, :Reason, :TimeBlocked, :TimeExpire, :Permanent, :RuleID)
        ');
        $stmt_bl->execute([
            ':Address'      => $address,
            ':Reason'       => $reason,
            ':TimeBlocked'  => $time,
            ':TimeExpire'   => ($expire == -1 ? 0 : ($time + $expire)),
            ':Permanent'    => ($expire == -1 ? 1 : 0),
            ':RuleID'       => (is_null($rule) ? null : $rule->RuleID),
        ]);

        return;
    }

    /**
     * Desabilita uma entrada do black list.
     *
     * @param int $blackListId
     *
     * @return boolean Verdadeiro caso removido com sucesso.
     */
    public function removeBlackList($blackListId)
    {
        // Se o firewall não estiver habilitado esta função nada fará.
        if(!APP_FIREWALL_ALLOWED)
            return;

        $stmt_bl = $this->getApp()->getSqlite()->prepare('
            UPDATE
                firewall_blacklist
            SET
                TimeExpire = :TimeExpire,
                Permanent = 0
            WHERE
                BlackListID = :BlackListID                
        ');
        $stmt_bl->execute([
            ':TimeExpire'   => microtime(true) - 1,
            ':BlackListID'  => $blackListId
        ]);

        return ($stmt_bl->rowCount() > 0);
    }

}

