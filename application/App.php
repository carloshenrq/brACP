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

        // Array de configurações locais.
        $configs = [
            'settings' => [
                'displayErrorDetails' => true
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
        $this->add(new AppFacebook($this));
        $this->add(new AppHttpClient($this));
        $this->add(new AppDatabase($this));
        $this->add(new AppCache($this));
        $this->add(new AppLanguage($this));
        $this->add(new AppFirewall($this));

        // Executa o método para instalar os plugins.
        $this->installPlugins();
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

