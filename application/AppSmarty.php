<?php

/**
 * Classe para execução da aplicação.
 */
class AppSmarty extends Smarty
{
    /**
     * Obtém a instância app usando este objeto.
     * @var App
     */
    private $app;

    /**
     * Construtor para o objeto do smarty.
     */
    public function __construct(App $app)
    {
        parent::__construct();

        // Define o app sendo utilizado.
        $this->setApp($app);

        // Define configurações iniciais dos tratamentos de templates
        $this->setTemplateDir(APP_TEMPLATE_DIR);

        if(APP_DEVELOPER_MODE) // Modo desenvolvedor, desativa o uso de cache.
            $this->setCaching(Smarty::CACHING_OFF);

        // Registra o plugin de tradução para os templates.
        $this->registerPlugin('block', 'translate', [$this, '__translate'], false, null);
        $this->registerPlugin('block', 'keygen', [$this, '__keygen'], false, null);
    }

    /**
     * Plugin do smarty para gerar o tag de keygen.
     */
    public function __keygen($params, $content, $template, &$repeat)
    {
        if(empty($content))
            return "";

        // Calcula o desafio e o hash do key
        $keygenChallenge = $content . microtime(true);
        $hashChallenge = hash('sha512', $keygenChallenge);

        // Define no sessão os dados de desafio.
        $this->getApp()->getSession()->APP_KEYGEN_CHALLENGE = $keygenChallenge;

        return "<keygen name='keygen' style='display: none;' keytype='rsa' challenge='{$hashChallenge}'/>";
    }

    /**
     * Plugin do smarty para obter a tradução de alguns
     * Dados informados pelo template.
     */
    public function __translate($params, $content, $template, &$repeat)
    {
        if(empty($content)) // Sem conteudo no tag = retorna do jeito que está.
            return "";
        
        // Organiza por chave de forma alfabética e retorna os dados
        // Traduzidos
        asort($params);
        return $this->getApp()
                    ->getLanguage()
                    ->getTranslate($content, array_values($params));
    }

    /**
     * Método para compilar o layout e definir corretamente as variaveis.
     * 
     * -> Lembrar também, que é necessário colocar todos os arquivos dependentes
     *    dentro da pasta de temas.
     * -> Se não colocar, irá dar erro de não conseguir encontrar o arquivo.
     *
     * @param string $template Arquivo que será compilado.
     * @param string $cache_id Dados de cache
     * @param string $compile_id Código de compilação.
     * @param object $parent Uso interno
     *
     * @return string Template compilado.
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        // Verifica o diretório de templates.
        $templateThemeDir = join(DIRECTORY_SEPARATOR, [
            APP_TEMPLATE_DIR, $this->getApp()->getSession()->APP_THEME,
        ]);
        $templateThemeFile = join(DIRECTORY_SEPARATOR, [$templateThemeDir, $template]);

        // Verifica se o template existe no diretório do tema.
        if(file_exists($templateThemeFile))
            $this->setTemplateDir($templateThemeDir);

        // Retorna os dados de de template e compila.
        return parent::fetch($template, $cache_id, $compile_id, $parent);
    }

    /**
     * Renderiza o template informado com os dados necessários.
     *
     * @param string $template Arquivo que será utilizado.
     * @param array $data Dados que serão utilizados para tratamento.
     *
     * @return string Dados renderizados.
     */
    public function render($template, $data = [], $cache = false, $expire = APP_CACHE_TIMEOUT, $force = false)
    {
        $announces = [];
        $ipAddress = '127.0.0.1';

        // Verifica se existem anuncios a serem avisados na tela do usuários
        // Estes anuncios são os anuncios sem perfil vinculado.
        if(!defined('APP_INSTALL_MODE') || !constant('APP_INSTALL_MODE'))
        {
            $announces = $this->getApp()->getEntityManager()->getRepository('Model\Announce')->getAllActiveGlobal();
            $ipAddress = $this->getApp()->getFirewall()->getIpAddress();
        }

        // Adiciona o formatador de campos ao dados de render.
        $data = array_merge($data, [
            // Formatador de campos
            'formatter'     => $this->getApp()->getFormatter(),
            // Endereço ip do usuário.
            'ipAddress'     => $ipAddress,
            // Idiomas que foram carregados. 
            'languages'     => $this->getApp()->getLanguage()->getLangs(),
            'langSelected'  => $this->getApp()->getSession()->APP_LANGUAGE,
            // Dados de sessão
            'session'       => $this->getApp()->getSession(),
            // Dados de anuncio global
            'announces'     => $announces,
        ]);

        // Verifica se o usuário está logado e o adiciona os dados que serão exibidos.
        if(\Controller\Profile::isLoggedIn() && (!defined('APP_INSTALL_MODE') || !constant('APP_INSTALL_MODE')))
        {
            $data = array_merge($data, [
                'loggedUser'    => \Controller\Profile::getLoggedUser()
            ]);
        }

        // Caso não seja para renderizar algo e colocar em cache,
        // Então, realiza a execução e retorna o render.
        if(!$cache)
        {
            $this->assign($data);
            return $this->fetch($template);
        }

        // Renderiza os dados e cria/devolve informações do cache
        // Para o template informado.
        return $this->getApp()
                    ->getCache()
                    ->parse(hash('md5', $template) . '_' . $this->getApp()->getSession()->APP_LANGUAGE, function() use ($template, $data) {
                        return App::getInstance()->getView()->render($template, $data);
                    }, $expire, $force);
    }

    /**
     * Obtém a instância do app que está utilizando este objeto.
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Define a instância do app que está utilizando este objeto.
     * 
     * @param App $app
     */
    public function setApp(App $app)
    {
        return $this->app = $app;
    }
}

