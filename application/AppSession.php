<?php

/**
 * Classe controladora de sessão da application
 */
class AppSession extends AppComponent
{
    /**
     * Inicializa os dados de sessão para serem tratados
     * Pela classe de session.
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        session_cache_limiter(false);
        if(session_status() == PHP_SESSION_NONE)
            session_start();

        // A Cada 5 minutos de sessão ativa, irá gerar um novo ID de sessão
        // Para o usuário.
        if(isset($this->APP_SESSION_CREATED))
        {
            // Verifica o timer da sessão.
            $sessionCreated = intval($this->APP_SESSION_CREATED);
            $sessionTimeNow = time();

            // Se a diferença de tempo for superior a 300s (5 minutos)
            // Irá recriar o código da sessão mantendo os dados atuais.
            if(($sessionTimeNow - $sessionCreated) >= 300)
            {
                $this->reGenerate();
                $this->APP_SESSION_CREATED = $sessionTimeNow;
            }
        }
        else
        {
            // Caso não tenha os dados de sessão, então os cria apartir do
            // momento atual.
            $this->APP_SESSION_CREATED = time();
        }
    }

    /**
     * Destroi e recria os dados da sessão atual.
     *
     * @return boolean Verdadeiro se fizer com sucesso.
     */
    public function reGenerate($eraseData = false)
    {
        return session_regenerate_id($eraseData);
    }

    /**
     * Obtém o código da sessão para o usuário.
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Obtém os dados que estão na sessão.
     *
     * @param string $name
     *
     * @return string Dados em sessão para o indice informado.
     */
    public function __get($name)
    {
        if(!$this->__isset($name))
            return null;
        
        return $this->decrypt($_SESSION[$this->encrypt($name)]);
    }

    /**
     * Define os dados na sessão do usuário.
     *
     * @param string $name
     * @param string $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $_SESSION[$this->encrypt($name)] = $this->encrypt($value);
    }

    /**
     * Verifica se o indice informado existe na sessão.
     *
     * @param string $name Nome do indice.
     *
     * @return bool Retorna verdadeiro caso exista.
     */
    public function __isset($name)
    {
        return isset($_SESSION[$this->encrypt($name)]);
    }

    /**
     * Remove o indice da sessão, caso necessário.
     *
     * @param string $name
     */
    public function __unset($name)
    {
        if(!$this->__isset($name))
            return;

        unset($_SESSION[$this->encrypt($name)]);
    }

    /**
     * Criptografa os dados enviados com os dados de session.
     *
     * @param string $data
     *
     * @return string Dados criptografados.
     */
    public function encrypt($data)
    {
        // Se não estiver configurado para
        // Proteger os dados com criptografia de session...
        if(!APP_SESSION_SECURE)
            return $data;
        
        return $this->getApp()
                    ->encrypt($data, APP_SESSION_ALGO, APP_SESSION_KEY, APP_SESSION_IV);
    }

    /**
     * Decriptografa os dados enviados com informações da session.
     *
     * @param string $data
     *
     * @return string Dados decriptografados.
     */
    public function decrypt($data)
    {
        // Se não estiver configurado para
        // Proteger os dados com criptografia de session...
        if(!APP_SESSION_SECURE)
            return $data;
        
        return $this->getApp()
                    ->decrypt($data, APP_SESSION_ALGO, APP_SESSION_KEY, APP_SESSION_IV);
    }
}
