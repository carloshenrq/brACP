<?php

/**
 * Classe para execução da aplicação.
 */
class App extends Slim\App
{
    /**
     * Instância para o app
     * @var App
     */
    private static $staticInstance = null;

    /**
     * Objeto para instanciar os views.
     *
     * @var AppSmarty
     */
    private $view;

    /**
     * Objeto para instanciar a session.
     *
     * @var AppSession
     */
    private $session;

    /**
     * Atributo para o mailer da aplicação.
     * @var AppMailer
     */
    private $mailer;

    /**
     * Atributo para o formatador de campos da aplicação.
     * @var AppFormatter
     */
    private $formatter;

    /**
     * Atributo para o EntityManager da aplicação.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Atributo para o EntityManager do servidor de rag a ser utilizado.
     * @var Doctrine\ORM\EntityManager
     */
    private $userEntityManager;

    /**
     * Atributo para o entitymanager do servidor de login para o ragnarok.
     * @var Doctrine\ORM\EntityManager
     */
    private $loginEntityManager;

    /**
     * Atributo para o AppCache da aplicação.
     * @var AppCache
     */
    private $cache;

    /**
     * Atributo para o AppFirewall da aplicação.
     * @var AppFirewall
     */
    private $firewall;

    /**
     * Atributo para o AppLanguage da aplicação.
     * @var AppLanguage
     */
    private $language;

    /**
     * Atributo para AppRequest da aplicação.
     * @var AppHttpClient
     */
    private $httpClient;

    /**
     * Atributo para o AppSchemaValidator
     * @var AppSchemaValidator
     */
    private $schemaValidator;

    /**
     * Obtém informações do api de facebook. 
     * @var AppFacebook
     */
    private $facebook;

    /**
     * Atributo para armazenar os dados de algumas configurações
     * relacionadas aos plungins e linguagens
     * @var PDO
     */
    private $sqlite;

    /**
     * Atributo para armazenar o tempo interno da requisição.
     * @var float
     */
    private $startTime;

    /**
     * Atributo para armazear o tempo interno que a requisição foi realizada.
     * @var float
     */
    private $startRequestTime;

    /**
     * Construtor para os dados de aplicação.
     */
    public function __construct()
    {
        // Marca o horario de para a construção do item.
        $this->startTime = microtime(true);

        // Inicializa a aplicação carregando informações de configuração.
        $this->init();

        // Array de configurações locais.
        $configs = [
            'settings' => [
                'displayErrorDetails' => APP_DEVELOPER_MODE
            ],
        ];

        // Chama o construtor antigo.
        parent::__construct($configs);

        // Define o app estatico.
        self::$staticInstance = $this;

        // Define a view que será utilizada para exibição com o
        // Framework.
        $this->setView(new AppSmarty($this));

        // Inicializa a sessão para o client.
        $this->sqlite = new AppSQLite($this);
        $this->session = new AppSession($this);
        $this->mailer = new AppMailer($this);
        $this->schemaValidator = new AppSchemaValidator($this);
        $this->formatter = new AppFormatter($this);

        // Define os dados de sessão para o tema.
        if(!isset($this->getSession()->APP_THEME))
            $this->getSession()->APP_THEME = APP_DEFAULT_THEME;

        // Adiciona os middlewares para a execução.
        $this->add(new AppRoute($this));
        $this->add(new AppHttpClient($this));
        $this->add(new AppCache($this));
        $this->add(new AppLanguage($this));

        // Caso não esteja em modo de instalação, então, permite a execução
        if(!defined('APP_INSTALL_MODE') || !constant('APP_INSTALL_MODE'))
        {
            $this->add(new AppFacebook($this));
            $this->add(new AppDatabase($this));
            $this->add(new AppFirewall($this));

            // Executa o método para instalar os plugins.
            $this->installPlugins();
        }
    }

    /**
     * Executa configurações iniciais do brACP e permite a inicialização da aplicação.
     */
    protected function init()
    {
        // Verifica se existe configurações que já foram instaladas.
        $config = realpath(join(DIRECTORY_SEPARATOR, [
            __DIR__, '..', 'config.php'
        ]));

        // Variável temporaria para configuração da aplicação.
        $_tmpConfig = [];

        // Caso não seja possível localizar o arquivo, então irá carregar configurações
        // Para instalação apenas.
        if($config === false || file_exists($config) === false)
        {
            // Obtém o URL_PATH atual.
            $urlPath = substr($_SERVER['PHP_SELF'], 0, -9);
            if(strlen($urlPath) > 1 && $urlPath !== '/')
                $urlPath = substr($urlPath, 0, -1);

            // Carrega configurações padrões de instalação para conseguir seguir a instalação do sistema.
            $_tmpConfig = [
                // Configurações de localização (pasta) e timezone  
                'APP_DEVELOPER_MODE'        => true,
                'APP_DEFAULT_TIMEZONE'      => 'America/Sao_Paulo',
                'APP_URL_PATH'              => $urlPath,
                'APP_INSTALL_MODE'          => true,

                // Definição de criptografia de sessão  
                'APP_SESSION_SECURE'        => true,
                'APP_SESSION_ALGO'          => 'AES-256-ECB',
                'APP_SESSION_KEY'           => 'fjPY131yohICvDj5JszAFIgGajZcZ7c3p4EIECbb0ac=',
                'APP_SESSION_IV'            => '',

                // Definições de configuração de e-mail
                'APP_MAILER_ALLOWED'        => false,
                'APP_MAILER_HOST'           => '',
                'APP_MAILER_PORT'           => 25,
                'APP_MAILER_ENCRYPT'        => '',
                'APP_MAILER_USER'           => '',
                'APP_MAILER_PASS'           => '',
                'APP_MAILER_FROM'           => '',
                'APP_MAILER_NAME'           => '',

                // Configuração de diretórios
                'APP_TEMPLATE_DIR'          => join(DIRECTORY_SEPARATOR, [
                    __DIR__, '..', 'application', 'View',
                ]),
                'APP_MODEL_DIR'             => join(DIRECTORY_SEPARATOR, [
                    __DIR__, '..', 'application', 'Model',
                ]),
                'APP_CACHE_DIR'             => join(DIRECTORY_SEPARATOR, [
                    __DIR__, '..', 'cache'
                ]),
                'APP_PLUGIN_DIR'            => join(DIRECTORY_SEPARATOR, [
                    __DIR__, '..', 'plugins'
                ]),
                'APP_SCHEMA_DIR'            => join(DIRECTORY_SEPARATOR, [
                    __DIR__, '..', 'schemas'
                ]),

                // Configurações para conexão com o banco de dados. (ELOQUENT)
                'APP_SQL_DRIVER'            => 'pdo_mysql',
                'APP_SQL_HOST'              => '127.0.0.1',
                'APP_SQL_USER'              => 'bracp',
                'APP_SQL_PASS'              => 'bracp',
                'APP_SQL_DATA'              => 'bracp',
                'APP_SQL_PERSISTENT'        => false,
                'APP_SQL_CONNECTION_STRING' => 'mysql:host=%s;dbname=%s', // Montar

                // Configurações de cache local
                'APP_CACHE_ENABLED'         => false,
                'APP_CACHE_TIMEOUT'         => 600,

                // Configurações de linguagem, temas e plugins
                'APP_DEFAULT_LANGUAGE'      => 'pt-BR',
                'APP_DEFAULT_THEME'         => 'classic',
                'APP_PLUGIN_ALLOWED'        => true,

                // Configurações de RECAPTCHA
                'APP_RECAPTCHA_ENABLED'     => false,
                'APP_RECAPTCHA_SITE_KEY'    => '',
                'APP_RECAPTCHA_PRIV_KEY'    => '',

                // Configurações de firewall
                'APP_FIREWALL_ALLOWED'      => false,
                'APP_FIREWALL_RULE_CONFIG'  => false,
                'APP_FIREWALL_MANAGER'      => false,

                // Configurações de API para o facebook
                'APP_FACEBOOK_ENABLED'      => false,
                'APP_FACEBOOK_APP_ID'       => '',
                'APP_FACEBOOK_APP_SECRET'   => '',

                // Configurações para o google authenticator
                'APP_GOOGLE_AUTH_MAX_ERRORS'    => 3,
                'APP_GOOGLE_AUTH_NAME'          => 'brACP',

                // ---------- CONFIGURAÇÕES PARA O RAGNAROK ---------- //

                'BRACP_ACCOUNT_CREATE'                  => true,
                'BRACP_ACCOUNT_PASSWORD_HASH'           => 'sha512',
                'BRACP_ACCOUNT_VERIFY'                  => true,
                'BRACP_ACCOUNT_VERIFY_EXPIRE'           => 7200,
                'BRACP_ACCOUNT_WRONGPASS_BLOCKCOUNT'    => 5,
                'BRACP_ACCOUNT_WRONGPASS_BLOCKTIME'     => 900,

                'BRACP_REGEXP_NAME'                     => '^[a-zA-ZÀ-ú0-9\s]{5,256}$',
                'BRACP_REGEXP_MAIL'                     => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                'BRACP_REGEXP_PASS'                     => '^((?=.*\d)(?=.*[a-zA-Z\s])(?=.*[@#$%])[a-zA-Z0-9\s@$$%]{6,})$',

                'BRACP_SERVER_PING'                     => 500,
                'BRACP_SERVER_SQL_PERSISTENT'           => false,

                'BRACP_RAG_ACCOUNT_CREATE'              => true,
                'BRACP_RAG_ACCOUNT_LIMIT'               => 5,
                'BRACP_RAG_ACCOUNT_PASSWORD_HASH'       => true,
                'BRACP_RAG_ACCOUNT_PASSWORD_ALGO'       => 'md5',
            ];
        }
        else
        {
            $_tmpConfig = (include $config);
        }

        // Aplica as constantes de configuração.
        foreach($_tmpConfig as $k => $v)
            DEFINE($k, $v, false);
    }

    /**
     * Define o timer inicial para a requisição realizada pelo client.
     *
     * @param float $startRequestTime
     */
    public function setStartRequestTime($startRequestTime)
    {
        $this->startRequestTime = $startRequestTime;
    }

    /**
     * Obtém o tempo incial de execução do sistema.
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Obtém os dados relacionados a sessão do usuário.
     *
     * @return AppSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Obtém o mailer para envio dos e-mails.
     * 
     * @return AppMailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Obtém a conexão com o sqlite das configurações de plugins, linguagens etc...
     * @return PDO
     */
    public function getSqlite()
    {
        return $this->sqlite->getConnection();
    }

    /**
     * Obtém o EntityManager para a aplicação.
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Define o entitymanager para a aplicação.
     *
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(Doctrine\ORM\EntityManager $entityManager)
    {
        $entityManager->getConnection()->connect();
        $this->entityManager = $entityManager;
    }

    /**
     * Obtém o EntityManager para o servidor do jogo.
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getUserEntityManager()
    {
        return $this->userEntityManager;
    }

    /**
     * Define o entitymanager para a aplicação.
     *
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function setUserEntityManager(Doctrine\ORM\EntityManager $entityManager)
    {
        $entityManager->getConnection()->connect();
        $this->userEntityManager = $entityManager;
    }

    /**
     * Obtém o EntityManager para o servidor do jogo.
     *
     * @return Doctrine\ORM\EntityManager
     */
    public function getLoginEntityManager()
    {
        return $this->loginEntityManager;
    }

    /**
     * Define o entitymanager para a aplicação.
     *
     * @param Doctrine\ORM\EntityManager $entityManager
     */
    public function setLoginEntityManager(Doctrine\ORM\EntityManager $entityManager)
    {
        $entityManager->getConnection()->connect();
        $this->loginEntityManager = $entityManager;
    }

    /**
     * Obtém o cache da aplicação.
     * @return AppCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Define o cache para a aplicação.
     * @param AppCache $cache
     */
    public function setCache(AppCache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Obtém o objeto de firewall da aplicação.
     * @var AppFirewall
     */
    public function getFirewall()
    {
        return $this->firewall;
    }

    /**
     * Define o objeto de firewall para a aplicação.
     * @param AppFirewall $firewall
     */
    public function setFirewall(AppFirewall $firewall)
    {
        $this->firewall = $firewall;
    }

    /**
     * Retorna o objeto de linguagem para a aplicação.
     * @return AppLanguage
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Define o objeto de linguagem para a aplicação.
     * @param AppLanguage $language
     */
    public function setLanguage(AppLanguage $language)
    {
        $this->language = $language;
    }

    /**
     * Obtém o objeto de requisições http para a aplicação.
     * @var AppHttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Define o client de requisições http para a aplicação.
     * 
     * @param AppHttpClient $httpClient
     */
    public function setHttpClient(AppHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Obtém o validador de schemas para a aplicação.
     *
     * @return AppSchemaValidator
     */
    public function getSchemaValidator()
    {
        return $this->schemaValidator;
    }

    /**
     * Define o validador de schemas para a aplicação.
     *
     * @param AppSchemaValidator $schemaValidator
     */
    public function setSchemaValidator(AppSchemaValidator $schemaValidator)
    {
        $this->schemaValidator = $schemaValidator;
    }

    /**
     * Obtém o wrapper de facebook. 
     *
     * @return AppFacebook
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Define informações sobre o wrapper para facebook. 
     *
     * @param AppFacebook $facebook
     */
    public function setFacebook(AppFacebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Obtém o formatador de campos da aplicação.
     *
     * @return AppFormatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Define o formatador de campos da aplicação.
     *
     * @param AppFormatter $formatter
     */
    public function setFormatter(AppFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Obtém instância do objeto da view a ser utilizado.
     *
     * @return AppSmarty
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Define instância do objeto da view a ser utilizado.
     *
     * @param AppSmarty $view
     */
    public function setView(AppSmarty $view)
    {
        return $this->view = $view;
    }

    /**
     * Criptografa o texto enviado com o algoritmo e chave.
     *
     * @param string $text
     * @param string $algo
     * @param string $key
     * @param string $iv
     *
     * @return string String criptografada com openssl em base64
     */
    public function encrypt($text, $algo, $key, $iv)
    {
        // Se a extensão não estiver carregada, então é impossível
        // Fazer uso da criptografia e retorna o proprio texto.
        if(!extension_loaded('openssl'))
            return $text;

        return openssl_encrypt($text, $algo, $key, false, $iv);
    }

    /**
     * Decriptografa o texto enviado com o algoritmo e chave.
     *
     * @param string $text
     * @param string $algo
     * @param string $key
     * @param string $iv
     *
     * @return string String decriptografa com openssl em base64
     */
    public function decrypt($text, $algo, $key, $iv)
    {
        // Se a extensão não estiver carregada, então é impossível
        // Fazer uso da criptografia e retorna o proprio texto.
        if(!extension_loaded('openssl'))
            return $text;

        return openssl_decrypt($text, $algo, $key, false, $iv);
    }

    /**
     * Obtém a instância estatica para o objeto de app.
     * @return App
     */
    public static function getInstance()
    {
        return self::$staticInstance;
    }

    /**
     * Método usado para instalar plugins que não são modificações das
     * classes já existentes.
     */
    private function installPlugins()
    {
        // Se configuração não permitir instalar os plugins
        // Cancela e retorna.
        if(!APP_PLUGIN_ALLOWED)
            return;

        // Varre os arquivos do diretório procurando algum para realizar a instalação.
        $installFiles = array_filter(scandir(APP_PLUGIN_DIR), function($file) {
            return preg_match('/^install\..*\.php$/', $file) > 0;
        });

        // Se o número de plugins para instalar é 0, então
        // Retorna a execução normal.
        if(count($installFiles) == 0)
            return;

        // Varre todos os arquivos para realizar a instalação
        // E logo após realiza a criação dos scripts de desinstalação.
        foreach($installFiles as $installFile)
        {
            // Obtém o arquivo que será instalado.
            $file = APP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $installFile;
            $install = (include $file);

            // Se o arquivo não possui dados de instalação
            // Apaga o arquivo e continua o varrimento dos próximos.
            if(!isset($install['install']) && !isset($install['install_data']))
            {
                unlink($file);
                continue;
            }

            // Se não houver função de instalação, será instalado
            // O Mod sem execução de funções de instalação.
            if(($canInstall = (!isset($install['install']))) == false)
            {

                $closure = Closure::bind($install['install'], $this);
                $canInstall = $closure();
            }

            // Caso seja possível a instalação (a função foi executada com sucesso ou não tem função)
            // Começa a extração dos arquivos.
            if($canInstall && isset($install['install_data']))
            {
                // Diretório base para instalação dos arquivos.
                $basePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);

                // Obtém todos os arquivos de instalação para o mod.
                $files = $install['install_data'];

                foreach($files as $fileName => $fileData)
                {
                    // Caminho completo para o arquivo que será extraido.
                    $fileFullPath = str_replace('/', DIRECTORY_SEPARATOR,
                                    str_replace('\\', DIRECTORY_SEPARATOR, $basePath . DIRECTORY_SEPARATOR . $fileName));

                    // Extrai o conteúdo do arquivo para o arquivo informado.
                    file_put_contents($fileFullPath, base64_decode($fileData));
                }

                // Serializa os dados para realizar a desinstalação futura.
                file_put_contents(APP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'un'.$installFile,
                    base64_encode(serialize($install['install_data'])));
            }

            // Apaga o arquivo de instalação.
            unlink($file);
        }
    }
}

