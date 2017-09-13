<?php

/**
 * Middle para carregar os dados de SQLite.
 */
class AppSQLite extends AppComponent
{
    /**
     * Dados de conexão com SQLite.
     * @var \PDO
     */
    private $connection;

    /**
     * Array com as tabelas instaladas ou que serão instaladas.
     * @var Array
     */
    private $installedTables;

    /**
     * Obtém os dados e informações de SQLite
     */
    public function __construct(App $app)
    {
        // Chama a herança do construtor para realizar
        // a parametrização inicial.
        parent::__construct($app);

        // Caminho para o banco de dados do sqlite.
        $sqlite_db = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR . 'system.db';

        // Define as informações para conexão com o SQLite
        // Das configurações atuais.
        $this->connection = new PDO('sqlite:' . $sqlite_db, null, null, [
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT    => true,
        ]);

        // Inicia a instalação de todas as tabelas que não estão em banco de dados.
        $this->performInstall();
    }

    /**
     * Carrega todas as tabelas que estão instaladas no banco de SQLite.
     */
    private function loadInstalledTables()
    {
        // Inicializa o vetor com array vazio.
        $this->installedTables = [];

        // Executa o comando no banco de dados para
        // ver se encontra as tabelas já instaladas.
        $stmt_installTables = $this->connection->query('
            SELECT
                tbl_name
            FROM
                sqlite_master
            WHERE
                type="table"
        ');
        $ds_installTables = $stmt_installTables->fetchAll(\PDO::FETCH_OBJ);

        // Carrega todas as tabelas instaladas em memória para tentar realizar a instalação das tabelas no sistema.
        foreach($ds_installTables as $rs_installTables)
            $this->installedTables[] = $rs_installTables->tbl_name;
    }

    /**
     * Realiza a instalação das tabelas de firewall.
     */
    private function installFirewall()
    {
        // Verifica se a tabela de regras de firewall está instalada.
        if(!in_array('firewall_rules', $this->installedTables))
        {
            // Query para criação da tabela
            $qry = '
                CREATE TABLE firewall_rules (
                    RuleID      INTEGER     PRIMARY KEY     AUTOINCREMENT   NOT NULL,
                    Rule        TEXT        NOT NULL,
                    RuleReason  STRING      NOT NULL,
                    RuleExpire  INTEGER     NOT NULL,
                    RuleEnabled BOOLEAN     DEFAULT false   NOT NULL
                );

                CREATE INDEX firewall_rules_i01 on firewall_rules (
                    RuleEnabled
                );
            ';

            // Quebra e instala os querys.
            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }

        // Verifica se a tabela firewall_request existe
        if(!in_array('firewall_request', $this->installedTables))
        {
            $qry = '
                CREATE TABLE firewall_request (
                    RequestID       INTEGER     PRIMARY KEY     AUTOINCREMENT   NOT NULL,
                    Address         VARCHAR(50)     DEFAULT(\'0.0.0.0\')        NOT NULL,
                    UserAgent       VARCHAR(100)    DEFAULT(\'\')               NOT NULL,
                    RequestTime     DECIMAL(17, 4)  DEFAULT(0)                  NOT NULL,
                    ServerTime      DECIMAL(17, 4)  DEFAULT(0)                  NOT NULL,
                    GMT             VARCHAR(50)                                 NOT NULL,
                    Method          VARCHAR(10)                                 NOT NULL,
                    Scheme          VARCHAR(20)                                 NOT NULL,
                    URI             VARCHAR(200)                                NOT NULL,
                    Filename        VARCHAR(200)                                NOT NULL,
                    PHPSession      VARCHAR(200)                                NOT NULL,
                    Length          INTEGER                                     NOT NULL,
                    ResponseLength  INTEGER                                     NOT NULL,
                    GET             STRING                                      NOT NULL,
                    POST            STRING                                      NOT NULL,
                    SESSION         STRING                                      NOT NULL,
                    UseToBan        BOOLEAN         DEFAULT false               NOT NULL
                );

                CREATE INDEX firewall_request_i01 on firewall_request (
                    Address,
                    UserAgent,
                    ServerTime
                );

                CREATE INDEX firewall_request_i02 ON firewall_request (
                    Length ASC,
                    ResponseLength ASC
                );
            ';

            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }

        // Verifica se a tabela de lista negra está instalada.
        if(!in_array('firewall_blacklist', $this->installedTables))
        {
            $qry = '
                CREATE TABLE firewall_blacklist (
                    BlacklistID     INTEGER         PRIMARY KEY     AUTOINCREMENT   NOT NULL,
                    Address         VARCHAR(50)                                     NOT NULL,
                    Reason          STRING                                          NOT NULL,
                    TimeBlocked     DECIMAL(17, 4)  DEFAULT(0)                      NOT NULL,
                    TimeExpire      DECIMAL(17, 4)  DEFAULT(0)                      NOT NULL,
                    Permanent       BOOLEAN         DEFAULT false                   NOT NULL,
                    RuleID          INTEGER                                         NULL
                );

                CREATE INDEX firewall_blacklist_i01 on firewall_blacklist (
                    Address
                );
            ';

            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }

        // Verifica se a tabela firewall_ipdata está instalada.
        if(!in_array('firewall_ipdata', $this->installedTables))
        {
            $qry = '
                CREATE TABLE firewall_ipdata (
                    LogID           INTEGER       PRIMARY KEY AUTOINCREMENT
                                                                NOT NULL,
                    Address         VARCHAR (50)                NOT NULL
                                            DEFAULT (\'000.000.000.000\'),
                    Hostname        VARCHAR (100)               NOT NULL,
                    City            VARCHAR (100)               NOT NULL,
                    Region          VARCHAR (100)               NOT NULL,
                    Country         VARCHAR (10)                NOT NULL,
                    Location        VARCHAR (100)               NOT NULL,
                    Origin          VARCHAR (200)               NOT NULL,
                    ServerTime      INTEGER         DEFAULT(0)  NOT NULL,
                    GMT             VARCHAR (50)                NOT NULL
                );

                CREATE INDEX firewall_ipdata_i01 on firewall_ipdata (
                    Address,
                    ServerTime
                );

                CREATE UNIQUE INDEX firewall_ipdata_u01 on firewall_ipdata (
                    Address,
                    ServerTime
                );
            ';
        
            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }

        // Tabela de dados para os usuários de firewall
        if(!in_array('firewall_users', $this->installedTables))
        {
            $qry = '
                CREATE TABLE firewall_users (
                    UserID      INTEGER         PRIMARY KEY AUTOINCREMENT,
                    User        VARCHAR(30)     NOT NULL,
                    UserPass    VARCHAR(32)     NOT NULL,
                    LoginCount  INTEGER         NOT NULL,
                    LoginEnabled BOOLEAN        DEFAULT false NOT NULL
                );

                CREATE INDEX firewall_users_i01 on firewall_users (
                    User,
                    UserPass,
                    LoginEnabled
                );

                CREATE UNIQUE INDEX firewall_users_u01 on firewall_users (
                    User
                );

                INSERT INTO firewall_users VALUES (NULL, \'admin\', \''.hash('md5', 'admin@12').'\', 0, 1);
            ';

            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }
    }

    /**
     * Realiza a instalação do módulo de linguas no banco de dados.
     */
    private function installLanguage()
    {
        if(!in_array('languages', $this->installedTables))
        {
            $this->connection->query("
                CREATE TABLE languages (
                    LanguageID VARCHAR(5) NOT NULL,
                    LanguageName VARCHAR(100) NOT NULL,
                    LanguageFile VARCHAR(255) NOT NULL
                );
            ");
        }
    }

    /**
     * Realiza a instalação das tabelas de endereço de mail.
     */
    private function installMailer()
    {
        // Tabela: mail_send
        if(!in_array('mail_send', $this->installedTables))
        {
            $this->connection->query("
                CREATE TABLE mail_send (
                    MailID      INTEGER     PRIMARY KEY     AUTOINCREMENT   NOT NULL,
                    Subject     TEXT        NOT NULL,
                    Body        STRING      NOT NULL,
                    TimeSend    INTEGER     NOT NULL,
                    SuccessSend BOOLEAN     DEFAULT false NOT NULL
                );
            ");
        }

        // Tabela: mail_send_destination
        if(!in_array('mail_send_destination', $this->installedTables))
        {
            $qry = '
                CREATE TABLE mail_send_destination (
                    MailID      INTEGER     NOT NULL,
                    Email       STRING      NOT NULL
                );

                CREATE UNIQUE INDEX mail_send_destination_u01 on mail_send_destination (
                    MailID,
                    Email
                );
            ';

            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }

        // Tabela: mail_send_attach
        if(!in_array('mail_send_attach', $this->installedTables))
        {
            $qry = '
                CREATE TABLE mail_send_attach (
                    MailID      INTEGER     NOT NULL,
                    FilePath    STRING      NOT NULL,
                    File        STRING      NOT NULL
                );

                CREATE UNIQUE INDEX mail_send_attach_u01 on mail_send_attach (
                    MailID,
                    FilePath
                );
            ';

            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }
    }

    /**
     * Instala tudo referente a assets.
     */
    private function installAsset()
    {
        // Tabela: asset_cache
        if(!in_array('asset_cache', $this->installedTables))
        {
            $qry = '
                CREATE TABLE asset_cache (
                    Filename STRING NOT NULL,
                    Filehash STRING NOT NULL,
                    FileOutput STRING NOT NULL
                );
                
                CREATE UNIQUE INDEX asset_cache_u01 on asset_cache (
                    Filename
                );
            ';

            foreach(explode(';', $qry) as $query)
                $this->connection->query($query);
        }
    }

    /**
     * Realiza a instalação de tabelas SQLite que não estão dentro
     * do banco de dados.
     */
    private function performInstall()
    {
        // Começa a realizar a execução das tabelas instaladas.
        $this->loadInstalledTables();

        // Inicializa as transações com o SQLite.
        $this->connection->beginTransaction();

        // Começa a instalação das tabelas.
        $this->installFirewall();
        $this->installLanguage();
        $this->installMailer();
        $this->installAsset();

        // Finaliza as alterações commitando tudo na tabela.
        $this->connection->commit();
    }

    /**
     * Obtém os dados de conexão com o SQLite.
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

}

