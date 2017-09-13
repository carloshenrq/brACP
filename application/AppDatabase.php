<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Configuration;

/**
 * Classe para inicializar a conexão com o banco de dados
 *  da aplicação.
 */
class AppDatabase extends AppMiddleware
{
    /**
     * Obtém a configuração para o banco de dados.
     *
     * @var Doctrine\ORM\Configuration
     */
    private $config;


    /**
     * Adiciona novas funções para o banco de dados.
     * -> Para adicionar novas funções através dos plugins,
     *    basta utilizar a funcionalidade 'exec' do próprio plugin
     *
     * @param Configuration $emConfig Configurações do doctrine.
     */
    private function appendFunctions(Configuration $config)
    {

        // Adiciona a função para realizar o tratamento
        // Para o código de personagem inserido em item.
        $config->addCustomNumericFunction('CharIDParser', 'Model\FNC_CharIDParser');

    }

    /**
     * Obtém a instância de configuração para o banco de dados.
     *
     * @return Doctrine\ORM\Configuration
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @see AppMiddleware::__invoke()
     */
    public function __invoke($request, $response, $next)
    {
        // Inicializa os parametros de conexão.
        $connectionParams = [
            'driver'        => APP_SQL_DRIVER,
            'host'          => APP_SQL_HOST,
            'user'          => APP_SQL_USER,
            'password'      => APP_SQL_PASS,
            'dbname'        => APP_SQL_DATA,
        ];

        // Se estiver configurado para uma conexão persistente com o banco de dados
        // Então, Cria uma conexão pdo em separado, e adiciona ao vetor
        // Informando a conexão persistente.
        if(APP_SQL_PERSISTENT)
        {
            $tryCount = 0;

            do
            {
                $pdo = null;
                try
                {
                    // Cria a conexão persistente com o banco de dados.
                    $pdo = new PDO(APP_SQL_CONNECTION_STRING, APP_SQL_USER, APP_SQL_PASS, [
                        PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_PERSISTENT    => true,
                    ]);
                }
                catch(Exception $ex)
                {
                    $tryCount++;
                    $pdo = null;
                }
            }
            while(is_null($pdo) && $tryCount < 3);

            // Mescla as configurações com o atributo pdo
            $connectionParams = array_merge($connectionParams, [
                'pdo' => $pdo,
            ]);
        }

        // Configurações para o doctrine.
        $this->config = Setup::createAnnotationMetadataConfiguration([APP_MODEL_DIR], true);

        // Faz o carregamento de funções personalizadas para o brACP.
        $this->appendFunctions($this->config);

        // Faz o carregamento de funções personalizadas para os plugins.
        $this->pluginExec();

        // Cria a conexão com o banco de dados e devolve ao application.
        $appEm = EntityManager::create($connectionParams, $this->config);

        // Define o entity manager da aplicação.
        $this->getApp()->setEntityManager($appEm);

        // Obtém todas as conexões com o banco de dados para
        // O servidor de ragnarok.
        $repoServer = $appEm->getRepository('Model\Server');
        $serverList = $repoServer->findBy([
            'enabled'   => true
        ]);

        // Verifica o servidor que está tentando fazer a conexão.
        $serverSelected = false;
        if(isset($this->getApp()->getSession()->BRACP_SERVER_SELECTED))
            $serverSelected = $this->getApp()->getSession()->BRACP_SERVER_SELECTED;

        // Varre todos os sub-servidores encontrados para realizar
        // as conexões necessárias.
        foreach($serverList as $server)
        {
            // Se não houver servidor selecionado, então irá utilizar
            // O Servidor master.
            if($serverSelected === false && $server->sqlType != 'S')
                continue;

            // Somente irá utilizar o servidor selecionado...
            if($serverSelected !== false && $server->id !== $serverSelected)
                continue;

            // Obtém define a sessão como sendo o ID do principal.
            if($serverSelected === false)
                $this->getApp()->getSession()->BRACP_SERVER_SELECTED = $server->id;

            // Define os parametros de conexão com o servidor.
            $_serverParams = [
                'driver'        => 'pdo_mysql',
                'host'          => $server->sqlHost,
                'user'          => $server->sqlUser,
                'password'      => $server->sqlPass,
                'dbname'        => $server->sqlData,
            ];

            // Se houver definição para conexão persistente
            // com o banco de dados...
            if(BRACP_SERVER_SQL_PERSISTENT)
            {
                // Cria a conexão persistente com o banco de dados.
                $pdo = new PDO('mysql:host=' . $server->sqlHost . ';dbname=' . $server->sqlData,
                    $server->sqlUser, $server->sqlPass, [
                        PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_PERSISTENT    => true,
                    ]);

                // Mescla as configurações com o atributo pdo
                $_serverParams = array_merge($_serverParams, [
                    'pdo' => $pdo,
                ]);
            }

            // Cria o configurador da conexão com o banco de dados.
            $_config = Setup::createAnnotationMetadataConfiguration([APP_MODEL_DIR], true);
            $this->appendFunctions($_config);

            // Cria a conexão com o servidor.
            $serverEm = EntityManager::create($_serverParams, $_config);

            // Define a conexão com o servidor do jogador.
            $this->getApp()->setUserEntityManager($serverEm);
            break;
        }

        // Define a conexão com o servidor de login para todas as contas.
        $loginServer = null;
        foreach($serverList as $server)
        {
            if($server->sqlType == 'S')
            {
                $loginServer = $server;
                break;
            }
        }

        // Define os parametros de conexão com o servidor.
        $_serverParams = [
            'driver'        => 'pdo_mysql',
            'host'          => $loginServer->sqlHost,
            'user'          => $loginServer->sqlUser,
            'password'      => $loginServer->sqlPass,
            'dbname'        => $loginServer->sqlData,
        ];

        // Se houver definição para conexão persistente
        // com o banco de dados...
        if(BRACP_SERVER_SQL_PERSISTENT)
        {
            // Cria a conexão persistente com o banco de dados.
            $pdo = new PDO('mysql:host=' . $loginServer->sqlHost . ';dbname=' . $loginServer->sqlData,
                $loginServer->sqlUser, $loginServer->sqlPass, [
                    PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT    => true,
                ]);

            // Mescla as configurações com o atributo pdo
            $_serverParams = array_merge($_serverParams, [
                'pdo' => $pdo,
            ]);
        }

        // Cria o configurador da conexão com o banco de dados.
        $_config = Setup::createAnnotationMetadataConfiguration([APP_MODEL_DIR], true);
        $this->appendFunctions($_config);

        // Cria a conexão com o servidor.
        $serverEm = EntityManager::create($_serverParams, $_config);

        // Define a conexão com o servidor de login.
        $this->getApp()->setLoginEntityManager($serverEm);

        // Move para a próxima execução.
        return parent::__invoke($request, $response, $next);
    }
}
