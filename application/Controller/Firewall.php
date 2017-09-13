<?php

namespace Controller;

/**
 * Controlador para as rotas de firewall. 
 */
class Firewall extends AppController
{
    /**
     * Objeto do usuário logado.
     * @var object
     */
    private $user = null;

    private $navigators = [
            // Teste para navegador google chrome
            [
                'name' => 'Google Chrome',
                'regex' => [
                    '/Chrome\/([^\s]+) Safari\/(?:[a-zA-Z0-9.]+)$/i',
                ]
            ],
            // Teste para navegador firefox
            [
                'name' => 'Mozilla Firefox',
                'regex' => [
                    '/(?:Firefox|Firebird)\/([a-zA-Z0-9.]+)$/i',
                ]
            ],
            // Teste para navegador Internet Explorer
            [
                'name' => 'Internet Explorer',
                'regex' => [
                    '/MSIE ([a-zA-Z0-9.]+)$/i',
                    '/rv\:([a-zA-Z0-9.]+)\) like Gecko$/i',
                ]
            ],
            // Teste para navegador Safari
            [
                'name' => 'Safari',
                'icon' => 'nav-safari',
                'regex' => [
                    '/Version\/([a-zA-Z0-9.]+) (?:(?:Mobile\/(?:[a-zA-Z0-9.]+)\s)?)Safari\/(?:[a-zA-Z0-9.]+)$/i',
                ]
            ],
            // Teste para navegador Opera
            [
                'name' => 'Opera',
                'regex' => [
                    '/^(?:Opera|Mozilla).*(?:Version\/|Opera\s)([a-zA-Z0-9.]+)$/i',
                ]
            ],
        ];


    /**
     * @see AppController::init()
     */
    protected function init()
    {
        // Se o gerênciador do firewall estiver habilitado
        // para ser utilizado, então, será adicionado os restrições de rota
        // Normalmente a cada rota, caso contrário as restrições de rotas
        // Serão para impedir o acesso as páginas de administração e gerenciamento.
        if(!APP_FIREWALL_MANAGER)
        {
            // Rotas que serão tratadas para não aceitarem
            // as funções administrativas quando o manager estiver desligado.
            $routes = ['admin_GET', 'admin_login_POST', 'admin_dashboard_GET', 'admin_dashboard_logout_POST',
                        'admin_dashboard_blacklist_GET', 'admin_dashboard_blacklist_add_POST',
                        'admin_dashboard_blacklist_free_POST', 'admin_dashboard_rules_GET',
                        'admin_dashboard_rules_add_POST'];
            foreach($routes as $route)
            {
                // Impede o acesso completo as rotas informadas.
                $this->addRouteRestriction($route, function() {
                    return false;
                });
            }

            // Impede o resto da execução do programa.
            return;
        }

        // Rotas que necessitam não pode estar logado para
        // realizar o acesso.
        $routes = ['admin_login_POST'];
        foreach($routes as $route)
        {
            $this->addRouteRestriction($route, function() {
                return !$this->isLoggedIn();
            });
        }

        // Rotas que necessitam que o usuário esteja logado para
        // realizar o acesso.
        $routes = ['admin_dashboard_GET', 'admin_dashboard_logout_POST', 'admin_dashboard_blacklist_GET',
                    'admin_dashboard_blacklist_add_POST', 'admin_dashboard_blacklist_free_POST',
                    'admin_dashboard_rules_GET', 'admin_dashboard_rules_add_POST'];
        foreach($routes as $route)
        {
            $this->addRouteRestriction($route, function() {
                return $this->isLoggedIn();
            });
        }

        // Carrega o objeto do usuário logado.
        if($this->isLoggedIn())
        {
            $stmt_user = $this->getApp()->getSqlite()->prepare('
                SELECT
                    *
                FROM
                    firewall_users
                WHERE
                    UserID = :UserID
            ');
            $stmt_user->execute([
                'UserID'    => $this->getApp()->getSession()->APP_FIREWALL_LOGGED
            ]);
            $this->user = $stmt_user->fetchObject();
        }

        return;
    }

    /**
     * Método de rota para administrar iniciar o login de administração do firewall.
     *
     * @param object $response
     * @param array $args 
     *
     * @return object Dados de resposta.
     */
    public function admin_GET($response, $args)
    {
        // Verifica se existe um usuário logado, se existir,
        // redireciona para a página de detalhes.
        if($this->isLoggedIn())
        {
            // Redireciona para o dashboard.
            return $response->withRedirect(implode('/', [
                APP_URL_PATH,
                'firewall',
                'admin',
                'dashboard'
            ]));
        }

        // Exibe a tela de login para o firewall
        return $this->render($response, 'firewall.login.tpl');
    }

    /**
     * Adicionado rota para exibição do dashboard.
     */
    public function admin_dashboard_GET($response, $args)
    {
        // Faz um select na tabela de dados de ips por região para fazer uma contagem
        // De quais regiões acessam mais o painel de controle.
        $qry_ipdata = $this->getApp()->getSqlite()->query('
            SELECT
                Country,
                COUNT(*) as CountryAccess
            FROM
                firewall_ipdata
            GROUP BY
                Country
            ORDER BY
                Country ASC
        ');
        $ds_ipdata = $qry_ipdata->fetchAll(\PDO::FETCH_OBJ);
        $ipdata = [];
        foreach($ds_ipdata as $rs_ipdata)
            $ipdata[$rs_ipdata->Country] = intval($rs_ipdata->CountryAccess);
        unset($ds_ipdata);

        return $this->render($response, 'firewall.dashboard.tpl', [
            'graph_labels'  => base64_encode(json_encode(array_keys($ipdata))),
            'graph_data'    => base64_encode(json_encode(array_values($ipdata))),
        ]);
    }

    /**
     * Rota para as configurações do banco de dados do firewall (zerar tabelas e etc...)
     */
    public function admin_dashboard_config_GET($response, $args)
    {
        $stmt_tables = $this->getApp()->getSqlite()->query('SELECT tbl_name FROM sqlite_master WHERE type = "table";');
        $ds_tables = $stmt_tables->fetchAll(\PDO::FETCH_OBJ);
        $tables_size = []; $tables_totalSize = 0;

        foreach($ds_tables as $rs_tables)
        {
            $stmt_columns = $this->getApp()->getSqlite()->query("PRAGMA table_info({$rs_tables->tbl_name});");
            $ds_columns = $stmt_columns->fetchAll(\PDO::FETCH_OBJ);
            $_tmp = [];

            foreach($ds_columns as $rs_column)
                $_tmp[] = "LENGTH(HEX({$rs_column->name}))";
            
            // Monta o select para 
            $select = '
                SELECT
                    SUM('.implode('+', $_tmp).') as totalBytes
                FROM
                    '.$rs_tables->tbl_name.'
            ';
            $stmt_size = $this->getApp()->getSqlite()->query($select);
            $obj = $stmt_size->fetchObject();

            $tables_size[$rs_tables->tbl_name] = intval($obj->totalBytes);
        }
        $tables_totalSize = array_sum($tables_size);

        // Chama o view com os dados das tabelas.
        return $this->render($response, 'firewall.dashboard.config.tpl', [
            'tables'            => $tables_size,
            'tables_totalSize'  => $tables_totalSize,
        ]);
    }

    /**
     * Rota para efetuar a limpeza de dados das tabelas.
     *
     * @param object $response
     * @param $args
     */
    public function admin_dashboard_config_clean_POST($response, $args)
    {
        $table = $this->post['table'];

        // Não se pode limpar a tabela de usuários do firewall.
        // Também não pode limpar tabelas internas do sqlite.
        if($table == 'firewall_users' || preg_match('/^sqlite/i', $table))
            return $response->withJson(['error' => true]);

        // Deleta todos os dados da tabela.
        $this->getApp()->getSqlite()->query("DELETE FROM {$table};");
        $this->getApp()->getSqlite()->query("vacuum");

        // Retorna verdadeiro na limpeza de tabela.
        return $response->withJson(['success' => true]);
    }

    /**
     * Rota para as requisições relacionadas aos usuários do painel administrativo
     * do firewall 
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function admin_dashboard_users_GET($response, $args)
    {
        // Obtém todos os usuários para o dashboard
        $stmt_users = $this->getApp()->getSqlite()->query('
            SELECT
                UserID,
                User,
                UserPass,
                LoginCount,
                LoginEnabled
            FROM
                firewall_users
            ORDER BY
                LoginEnabled DESC,
                LoginCount DESC
        ');
        $ds_users = $stmt_users->fetchAll(\PDO::FETCH_OBJ);

        // Exibe em tela os dados de usuários.
        return $this->render($response, 'firewall.dashboard.users.tpl', [
            'users'         => base64_encode(json_encode($ds_users)),
            'loggedUserID'  => $this->user->UserID
        ]);
    }

    /**
     * Método para ativar/desativar um usuário.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function admin_dashboard_users_change_POST($response, $args)
    {
        $UserID = $this->post['UserID'];
        $LoginEnabled = $this->post['LoginEnabled'];

        try
        {
            $stmt_user = $this->getApp()->getSqlite()->prepare('
                UPDATE
                    firewall_users
                SET
                    LoginEnabled = :LoginEnabled
                WHERE
                    UserID = :UserID
            ');
            $stmt_user->execute([
                ':LoginEnabled' => $LoginEnabled,
                ':UserID'       => $UserID
            ]);

            return $response->withJson(['success' => true]);
        }
        catch(\Exception $ex)
        {
            return $response->withJson(['error' => true]);
        }
    }

    /**
     * Método para atualizar/inserir um novo usuário. 
     *
     * @param object $response
     * @param object $args 
     *
     * @return object
     */
    public function admin_dashboard_users_add_POST($response, $args)
    {
        $userId = intval($this->post['id']);
        $user = $this->post['user'];
        $pass = hash('md5', $this->post['pass']);

        // Inserindo um novo usuário no banco de dados.
        if($userId == -1)
        {
            try
            {
                $stmt_user = $this->getApp()->getSqlite()->prepare('
                    INSERT INTO firewall_users VALUES (NULL, :User, :UserPass, 0, 1)
                ');
                $stmt_user->execute([
                    ':User'     => $user,
                    ':UserPass' => $pass
                ]);

                return $response->withJson(['success' => true]);
            }
            catch(\Exception $ex)
            {
                return $response->withJson(['error' => true]);
            }
        }
        else
        {
            try
            {
                $stmt_user = $this->getApp()->getSqlite()->prepare('
                    UPDATE
                        firewall_users
                    SET
                        User = :User,
                        UserPass = :UserPass
                    WHERE
                        UserID = :UserID
                ');
                $stmt_user->execute([
                    ':User'     => $user,
                    ':UserPass' => $pass,
                    ':UserID'   => $userId
                ]);

                return $response->withJson(['success' => true]);
            }
            catch(\Exception $ex)
            {
                return $response->withJson(['error' => true]);
            }
        }
    }

    /**
     * Rota para as visualizações de requisições + pesquisa de endereços ips.
     *
     * @param object $request
     * @param object $args
     *
     * @return object
     */
    public function admin_dashboard_requests_GET($response, $args)
    {
        // Conta quantos bytes já foram transferidos pelas requisições até o momento.
        $stmt_count = $this->getApp()->getSqlite()->query('
            SELECT
                SUM(Length+ResponseLength) as trafficBytes
            FROM
                firewall_request
        ');
        $obj_count = $stmt_count->fetchObject();

        // Faz uma contagem dos 10 endereços que mais fazem requisições
        // no painel de controle.
        $stmt_country = $this->getApp()->getSqlite()->query('
            SELECT
                fi.Country,
                (COUNT(fi.Country) /
                (SELECT COUNT(*) FROM firewall_ipdata where firewall_ipdata.Address = fi.Address)) as CountCountry
            FROM
                firewall_ipdata as fi
            INNER JOIN
                firewall_request as fr
                    ON (fr.Address = fi.Address and
                        fr.UseToBan = 1)
            GROUP BY
                fi.Address,
                fi.Country
            ORDER BY
                CountCountry DESC
        ');
        $ds_country = $stmt_country->fetchAll(\PDO::FETCH_OBJ);
        $graph_country = [];

        // Popula informações sobre os 10 paises que fazem mais
        // requisições ao sistema.
        foreach($ds_country as $rs_country)
        {
            if(!isset($graph_country[$rs_country->Country]))
            {
                if(count($graph_country) > 10)
                    continue;

                $graph_country[$rs_country->Country] = 0;
            }

            $graph_country[$rs_country->Country] += intval($rs_country->CountCountry);
        }

        // Faz uma contagem dos 10 endereços que mais fazem requisições
        // no painel de controle.
        $stmt_request = $this->getApp()->getSqlite()->query('
            SELECT
                Address,
                COUNT(*) as QtdeRequest
            FROM
                firewall_request
            WHERE
                usetoban = 1
            GROUP BY
                Address
            ORDER BY
                QtdeRequest DESC
            LIMIT 10
        ');
        $ds_request = $stmt_request->fetchAll(\PDO::FETCH_OBJ);
        $graph_data = [];

        // Popula informações sobre os 10 ultimos endereços ips com
        // mais requisições realizadas.
        foreach($ds_request as $rs_request)
            $graph_data[$rs_request->Address] = $rs_request->QtdeRequest;

        // Query para as 20 caminhos mais acessados do sistema.
        $stmt_uri = $this->getApp()->getSqlite()->query('
            SELECT
                URI,
                COUNT(*) as CountURI
            FROM
                firewall_request
            WHERE
                usetoban = 1
            GROUP BY
                URI
            ORDER BY
                CountURI DESC
            LIMIT
                20
        ');
        $ds_uri = $stmt_uri->fetchAll(\PDO::FETCH_OBJ);
        $graph_uri = [];
        foreach($ds_uri as $rs_uri)
            $graph_uri[$rs_uri->URI] = $rs_uri->CountURI;

        // Query para obter os navegadores mais utilizados para acessar o sistema.
        $stmt_userAgent = $this->getApp()->getSqlite()->query('
            SELECT
                UserAgent,
                COUNT(*) as CountUserAgent
            FROM
                firewall_request
            WHERE
                usetoban = 1
            GROUP BY
                UserAgent
            ORDER BY
                CountUserAgent DESC
        ');
        $ds_userAgent = $stmt_userAgent->fetchAll(\PDO::FETCH_OBJ);
        $graph_userAgent = [];

        $total_userAgent = $total_userAgentParsed = 0;
        foreach($ds_userAgent as $rs_userAgent)
        {
            $total_userAgent += $rs_userAgent->CountUserAgent;
            foreach($this->navigators as $navigator)
            {
                foreach($navigator['regex'] as $regexp)
                {
                    if(preg_match($regexp, $rs_userAgent->UserAgent))
                    {
                        if(!isset($graph_userAgent[$navigator['name']]))
                            $graph_userAgent[$navigator['name']] = 0;

                        $graph_userAgent[$navigator['name']] += $rs_userAgent->CountUserAgent;
                        $total_userAgentParsed += $rs_userAgent->CountUserAgent;
                        break;
                    }
                }
            }
        }

        if($total_userAgent < $total_userAgentParsed)
            $graph_userAgent['Outros'] = $total_userAgent - $total_userAgentParsed;

        return $this->render($response, 'firewall.dashboard.requests.tpl', [
            'graph_country'     => $graph_country,
            'graph_data'        => $graph_data,
            'graph_uri'         => $graph_uri,
            'graph_userAgent'   => $graph_userAgent,
            'trafficBytes'      => $obj_count->trafficBytes
        ]);
    }

    /**
     * Rota para as regras de firewall presentes.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function admin_dashboard_rules_GET($response, $args)
    {
        // Obtém do banco de dados, todas as regras criadas e retorna para a tela.
        $stmt_rules = $this->getApp()->getSqlite()->query('
            SELECT
                RuleID,
                Rule,
                RuleReason,
                RuleExpire,
                RuleEnabled
            FROM
                firewall_rules
            ORDER BY
                RuleID DESC
        ');
        $ds_rules = $stmt_rules->fetchAll(\PDO::FETCH_OBJ);

        foreach($ds_rules as &$rs_rules)
            $rs_rules->Rule = base64_encode($rs_rules->Rule);

        return $this->render($response, 'firewall.dashboard.rules.tpl', [
            'rules'     => base64_encode(json_encode($ds_rules))
        ]);
    }

    /**
     * Caminho para adicionar novas regras ao firewall.
     *
     * @param object $response
     * @param object $args
     *
     * @return object
     */
    public function admin_dashboard_rules_add_POST($response, $args)
    {
        $id = intval($this->post['id']);
        $code = $this->post['code'];
        $reason = $this->post['reason'];
        $expire = intval($this->post['expire']);
        $enabled = intval($this->post['enabled']);

        $func = null;

        // PHP 7+, Throwable só existe em versões de php 7 ou mais
        // E o Eval emite um throwable quando está em versões php7
        if(interface_exists('\Throwable'))
        {
            try
            {
                eval('$func = ' . $code . ';');
            }
            catch(\Throwable $exp)
            {
                return $response->withJson(['error' => true]);
            }
        }
        else
        {
            if(@eval('$func = ' . $code . ';') == false)
                return $response->withJson(['error' => true]);
        }

        // Se for -1, então está sendo 
        if($id == -1)
        {
            $stmt_rule = $this->getApp()->getSqlite()->prepare('
                INSERT INTO
                    firewall_rules
                VALUES
                    (NULL, :Rule, :RuleReason, :RuleExpire, :RuleEnabled)
            ');
            $stmt_rule->execute([
                ':Rule'         => $code,
                ':RuleReason'   => $reason,
                ':RuleExpire'   => $expire,
                ':RuleEnabled'  => ($enabled ? 1:0)
            ]);
        }
        else
        {
            $stmt_rule = $this->getApp()->getSqlite()->prepare('
                UPDATE
                    firewall_rules
                SET 
                    Rule = :Rule,
                    RuleReason = :RuleReason,
                    RuleExpire = :RuleExpire,
                    RuleEnabled = :RuleEnabled
                WHERE
                    RuleID = :RuleID
            ');
            $stmt_rule->execute([
                ':RuleID'       => $id,
                ':Rule'         => $code,
                ':RuleReason'   => $reason,
                ':RuleExpire'   => $expire,
                ':RuleEnabled'  => ($enabled ? 1:0)
            ]);
        }

        return $response->withJson(['success' => true]);
    }

    /**
     * Rota para visualizar todos os endereços atualmente bloqueados.
     *
     * @param object $response
     * @param array $args 
     *
     * @return object
     */
    public function admin_dashboard_blacklist_GET($response, $args)
    {
        // Faz o select para descobrir quantos endereços de ip 
        // Estão na lista negra.
        $stmt_bl = $this->getApp()->getSqlite()->prepare('
            SELECT
                BlacklistID,
                Address,
                Reason,
                TimeBlocked,
                TimeExpire,
                Permanent
            FROM
                firewall_blacklist
            WHERE
                Permanent = 1 OR
                TimeExpire > :TimeExpire
            ORDER BY
                TimeExpire DESC,
                Permanent DESC
        ');
        $stmt_bl->execute([
            ':TimeExpire'   => microtime(true),
        ]);
        $ds_bl = $stmt_bl->fetchAll(\PDO::FETCH_OBJ);

        // Formata quando irá expirar o bloqueio.
        foreach($ds_bl as &$rs_bl)
        {
            $rs_bl->TimeBlocked     = $this->getApp()
                                            ->getFormatter()
                                            ->date(date('Y-m-d H:i:s', intval($rs_bl->TimeBlocked)));
            $rs_bl->TimeExpire      = ($rs_bl->Permanent ? 'Nunca' : $this->getApp()
                                                                            ->getFormatter()
                                                                            ->date(date('Y-m-d H:i:s', intval($rs_bl->TimeExpire))));
        }

        // Chama a tela de exibição para todos os itens em blacklist.
        return $this->render($response, 'firewall.dashboard.blacklist.tpl', [
            'blackList' => base64_encode(json_encode($ds_bl))
        ]);
    }

    /**
     * Adiciona uma nova entrada de endereço ip ao blacklist.
     *
     * @param object $response
     * @param array $args 
     *
     * @return object
     */
    public function admin_dashboard_blacklist_add_POST($response, $args)
    {
        // Adiciona um endereço ip a lista de bloqueados.
        $this->getApp()->getFirewall()->addBlackList(
            $this->post['ipAddress'],
            $this->post['reason'],
            intval($this->post['time'])
        );

        return $response->withJson([
            'success' => true
        ]);
    }

    /**
     * Libera um endereço da blacklist. 
     *
     * @param object $response
     * @param array $args 
     *
     * @return object
     */
    public function admin_dashboard_blacklist_free_POST($response, $args)
    {
        // Obtém o código da blacklist para remover os acessos.
        $BlackListID = $this->post['BlackListID'];

        // Se houve sucesso na remoção, retorna verdadeiro.
        if($this->getApp()->getFirewall()->removeBlackList($BlackListID))
            return $response->withJson([
                'success' => true,
            ]);

        // Caso de erro na remoção, informa o erro.
        return $response->withJson([
            'error' => true
        ]);
    }

    /**
     * Adicionado rota para logout do usuário de dentro do painel
     * de controle relacionado ao firewall.
     */
    public function admin_dashboard_logout_POST($response, $args)
    {
        unset($this->getApp()->getSession()->APP_FIREWALL_LOGGED);
        return $response;
    }

    /**
     * Informações para realizar login no painel de controle.
     */
    public function admin_login_POST($response, $args)
    {
        // Cria o statement para realizar a consulta no banco de dados
        // e saber se existe o usuário solicitado.
        $stmt_user = $this->getApp()->getSqlite()->prepare('
            SELECT
                UserID
            FROM
                firewall_users
            WHERE
                User = :User
                    AND
                UserPass = :UserPass
                    AND
                LoginEnabled = 1
        ');
        $stmt_user->execute([
            ':User'         => $this->post['username'],
            ':UserPass'     => hash('md5', $this->post['password']),
        ]);
        $obj_user = $stmt_user->fetchObject();

        // Se não houver usuários com a combinação de usuário
        // E senha informados, então retorna error = true.
        if($obj_user === false)
        {
            $error = ['error' => true];

            if(!isset($this->getApp()->getSession()->APP_FIREWALL_ERROR))
                $this->getApp()->getSession()->APP_FIREWALL_ERROR = 0;

            // Se houve mais de 5 tentativas inválidas de tentativa de login
            // O endereço ip de conexão será bloqueado pelo firewall.
            if((++$this->getApp()->getSession()->APP_FIREWALL_ERROR) >= 5)
            {
                $firewall = $this->getApp()->getFirewall();
                $firewall->addBlackList($firewall->getIpAddress(), '[FIREWALL] Muitas tentativas de logins incorretas.', 7200);

                $error['blackListed'] = true;

                unset($this->getApp()->getSession()->APP_FIREWALL_ERROR);
            }

            // Retorna o erro em memória.
            return $response->withJson($error);
        }

        // Remove a informação de erro na tela.
        unset($this->getApp()->getSession()->APP_FIREWALL_ERROR);

        // Define o ID do usuário logado.
        $this->getApp()->getSession()->APP_FIREWALL_LOGGED = $obj_user->UserID;

        // Atualiza informações de loginCount para o usuário 
        $stmt_userCount = $this->getApp()->getSqlite()->prepare('
            UPDATE
                firewall_users
            SET
                LoginCount = LoginCount + 1
            WHERE
                UserID = :UserID
        ');
        $stmt_userCount->execute([
            ':UserID'   => $obj_user->UserID,
        ]);

        // Caso encontre o usuário, retorna o success = true.
        return $response->withJson([
            'success'   => true,
        ]);
    }

    /**
     * Exibe todas as requisições realizadas pelo endereço ip que está
     * se conectando a esta rota.
     *
     * -> Mesmo que o gerênciador de firewall esteja desligado, esta rota sempre estará ativa
     *    Entendo que é direito do usuário ter noção do que está sendo gravado sobre as requisições deles.
     *    É claro que não se pode mostrar o conteúdo completo para uma pessoa com más intenções, mas já é um modo 
     *    Dela ter noção do que o firewall é capaz de fazer.
     */
    public function index_GET($response, $args)
    {
        // Obtém o endereço ip que está conectado a rota atual.
        $ipAddress = $this->getApp()->getFirewall()->getIpAddress();

        // Seleciona as 100 últimas requisições para o endereço ip atual.
        $stmt = $this->getApp()->getSqlite()->prepare('
            SELECT
                Address,
                UserAgent,
                RequestTime,
                Method,
                URI,
                PHPSession
            FROM 
                firewall_request
            WHERE
                Address = :Address
                    AND
                UseToBan = 1
            ORDER BY
                ServerTime DESC
            LIMIT
                100
        ');
        $stmt->execute([
            ':Address' => $ipAddress,
        ]);

        // Obtém todos os registros para exibição dos dados em tela.
        $ds_req = $stmt->fetchAll(\PDO::FETCH_OBJ);

        // Chama o view de template para exibição dos dados em tela.
        return $this->render($response, 'firewall.index.tpl', [
            'ipAddress'     => $ipAddress,
            'ipLogDetails'  => $ds_req,
        ]);
    }

    /**
     * Verifica se o usuário está logado dentro do firewall. 
     *
     * @return boolean Verdadeiro caso esteja, falso caso não esteja.
     */
    protected function isLoggedIn()
    {
        return isset($this->getApp()->getSession()->APP_FIREWALL_LOGGED);
    }
}

