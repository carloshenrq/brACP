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

use Doctrine\ORM\EntityManager;
use Controller\Account;
use RKA\Middleware\IpAddress;

/**
 * Classe para gerenciar a aplicação
 */
class brACPApp extends Slim\App
{
    /**
     * Instância de aplicação para o primeiro brACPApp criado.
     *
     * @var brACPApp
     */
    private static $app = null;

    /**
     * Classe para os templates do sistema.
     * @var Smarty
     */
    private $view;

    /**
     * Entitymanager para gerênciamento do banco de dados.
     *
     * @var EntityManager
     */
    private $em;

    /**
     * Tratamento de sessão como objeto.
     * @var Session
     */
    private $session;

    /**
     * Tratamento de traduções do painel de controle.
     * @var Language
     */
    private $language;

    /**
     * Construtor e inicializador para o painel de controle.
     */
    public function __construct()
    {
        // Initialize session for this app.
        $this->session = new Session();


        // Configurações alternativas.
        $configs = [
            'settings' => [
                'displayErrorDetails' => BRACP_DEVELOP_MODE
            ]
        ];

        // Inicializa o sistema de cache.
        Cache::init();

        // Variavel de sessão para definir a linguagem padrão do painel de controle.
        if(!isset($this->session->BRACP_LANGUAGE))
            $this->session->BRACP_LANGUAGE = BRACP_DEFAULT_LANGUAGE;

        // Inicializa a linguagem em modo PORTUGUÊS BR.
        //  @Temporario, pois será alterado para variavel de sessão.
        Language::load($this->session->BRACP_LANGUAGE);

        // Verifica se a variavel de tema para a sessão está definida.
        // Se não estiver, define como a do tema padrão.
        if(!isset($this->session->BRACP_THEME))
            $this->session->BRACP_THEME = BRACP_DEFAULT_THEME;

        // Loads the default settings for this app.
        parent::__construct($configs);

        // Cria a instância do smarty.
        $this->view = new Smarty;
        $this->view->setTemplateDir(BRACP_TEMPLATE_DIR);
        $this->view->setCaching(false);

        // Adiciona os middlewares na rota para serem executados.
        $this->add(new RouteCustom());
        $this->add(new Route());
        $this->add(new Database());

        // Define a instância global como sendo o proprio.
        self::$app = $this;
    }

    /**
     * Código re-captcha para a verificação no link de envio.
     *
     * @param string $response Código re-captcha para verificação.
     *
     * @return boolean Se verdadeiro a requisição foi verificada com sucesso.
     */
    public function checkReCaptcha($response)
    {
        // Se a configuração estiver desabilitada para o captcha,
        //  não enviar requisição, apenas retornar false.
        if(!BRACP_RECAPTCHA_ENABLED)
            return false;

        // Realiza a requisição da key no servido reCaptcha do google
        //  para testar se a requisição é verdadeira ou não.
        $captchaResponse = json_decode(Request::create('')
            ->post(BRACP_RECAPTCHA_PRIVATE_URL, [
                'form_params' => [
                    'secret' => BRACP_RECAPTCHA_PRIVATE_KEY,
                    'response' => $response
                ]
            ])->getBody()->getContents());

        // Se a validação for realizada com sucesso, então, retorna verdadeiro,
        //  se não, falso.
        return $captchaResponse->success == 1;
    }

    /**
     * Realiza o envio de e-mails para os usuários.
     *
     * @param string $subject
     * @param string $to
     * @param string $template
     * @param array $data
     *
     * @return boolean
     */
    public function sendMail($subject, $to, $template, $data = [])
    {
        // Verifica se a configuração de envio para e-mails está habilitada.
        //  Se não estiver, retorna false.
        if(!BRACP_ALLOW_MAIL_SEND)
            return false;

        // Transporte para o email.
        $transport = \Swift_SmtpTransport::newInstance(BRACP_MAIL_HOST, BRACP_MAIL_PORT)
                                            ->setUsername(BRACP_MAIL_USER)
                                            ->setPassword(BRACP_MAIL_PASS);
        // Mailer para envio dos dados.
        $mailer = \Swift_Mailer::newInstance($transport);
        // Mensagem para enviar.
        $message = \Swift_Message::newInstance(Language::parse($subject))
                                    ->setFrom([BRACP_MAIL_FROM => BRACP_MAIL_FROM_NAME])
                                    ->setTo($to)
                                    ->setBody($this->render($template, $data, false), 'text/html');

        // Retorna informando que o envio foi realizado com sucesso.
        return $mailer->send($message) > 0;
    }

    /**
     * Gera uma string aleatória.
     *
     * @param int $length
     * @param string $string
     *
     * @return string
     */
    public function randomString($length, $string)
    {
        return substr(str_shuffle($string), 0, $length);
    }

    /**
     * Exibe o template a ser chamado.
     *
     * @param string $template Nome do arquivo a ser chamado.
     * @param array $data Dados a serem enviados ao template.
     */
    public function display($template, $data = [], $ajax = true)
    {
        echo $this->render($template, $data, $ajax);
    }

    /**
     * Renderiza o template e retorna a string dos dados para a solicitação.
     *
     * @param string $template Nome do arquivo a ser chamado.
     * @param array $data Dados a serem enviados ao template.
     *
     * @return string
     */
    public function render($template, $data = [], $ajax = true)
    {
        try
        {
            // Verifica se o usuário está logado no sistema.
            if(Account::isLoggedIn())
                $data = array_merge(['userid' => Account::loggedUser()->getUserId(),
                                     'account' => Account::loggedUser()], $data);
        }
        catch(\Exception $ex)
        {
            // Irá acontecer quando existir um erro de conexão com o banco de dados.
            if(!isset( $data['exception']))
                $data['exception'] = $ex;
        }

        // Obtém todos os temas que estão em cache no banco de dados.
        $themes = Cache::get('BRACP_THEMES', function() {
            return brACPApp::getInstance()->getEm()->getRepository('Model\Theme')->findAll();
        });

        // Adiciona o navegador aos dados para o template.
        $data = array_merge($data, [
            'themes' => $themes,
            'langs' => Language::readAll(),
            'session' => $this->getSession(),
            'navigator' => Navigator::getBrowser($this->getContainer()->get('request')->getHeader('user-agent')[0]),
            'ipAddress' => $this->getIpAddress(),
        ]);

        // Atribui os dados ao smarty.
        $this->view->assign($data);

        // Se for uma requisição do tipo ajax, adiciona o sulfixo ajax ao nome do template.
        if($ajax && $this->getContainer()->get('request')->isXhr())
            $template .= '.ajax';

        // Renderiza o template.
        return Language::parse($this->view->fetch($template . '.tpl'));
    }

    /**
     * Recebe o endereço IP do cliente que está realizando a requisição.
     *
     * @return string
     */
    private function getIpAddress()
    {
        // Possiveis variaveis para se obter o endereço ip do cliente.
        $_vars = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                  'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

        // Varre as opções para retornar os dados ao painel de controle.
        foreach( $_vars as $ip )
        {
            if(getenv($ip) !== false)
                return getenv($ip);
        }

        // Devolve o endereço ip do cliente.
        return '?.?.?.?';
    }

    /**
     * Define o entitymanager.
     *
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }

    /**
     * Obtém o EntityManager.
     *
     * @return EntityManager
     */
    public function getEm()
    {
        // Se não houver EntityManager, emite uma exceção para tratamento
        //  do erro.
        if(is_null($this->em))
            throw new \Exception('EntityManager not defined.');

        return $this->em;
    }

    /**
     * Obtém os dados de sessão.
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Método para criar um backup do painel de controle.
     * NOTA.: Este backup não é um backup do banco de dados. É somente de todos os arquivos
     *        contidos na pasta painel de controle. É um backup completo, incluindo as pastas de cache e vendor.
     */
    public function createBackup()
    {
        // Caminho completo da pasta do sistema.
        $realpath = realpath(__DIR__ . '/../');

        // Obtém todas as entradas para os arquivos.
        $entries = $this->readDir($realpath);

        // Nome do arquivo de backup que fora criado.
        $backupFile = $realpath . '/backup/' . date('Ymd_His') . '_brACP-' . BRACP_VERSION . '_'.substr(hash('md5', microtime(true)), 0, 7).'.zip';

        // Cria o arquivo ZIP e adiciona as entradas ao arquivo.
        $zipArchive = new ZipArchive;
        $zipArchive->open($backupFile, ZIPARCHIVE::CREATE);
        foreach($entries as $entry)
        {
            $full = $realpath . '/' . $entry;

            if(is_file($full))
            {
                $zipArchive->addFile($full, $entry);
            }
            else if(is_dir($full))
            {
                $zipArchive->addEmptyDir($entry);
            }
        }

        $fileCount = $zipArchive->numFiles;

        $zipArchive->close();

        return [
            'fileName' => $backupFile,
            'fileSize' => filesize($backupFile),
            'fileCount' => $fileCount,
            'fileHashMD5' => hash_file('md5', $backupFile),
            'fileHashSHA1' => hash_file('sha1', $backupFile),
            'fileHashSHA512' => hash_file('sha512', $backupFile),
        ];
    }

    /**
     * Retorna a lista de todos os arquivos dentro do diretório de forma recursiva.
     *
     * @param string $path Caminho real a ser verificado.
     * @param string $relative Caminho relativo a ser adicionado no retorno.
     *
     * @return Array
     */
    private function readDir($path, $relative = '')
    {
        $entries = [];

        $dir = new DirectoryIterator($path);

        foreach($dir as $entry)
        {
            if($entry->isDot() || $entry->getFilename() == 'backup' || $entry->getFilename() == 'updates')
                continue;

            $entries[] = $relative . $entry->getFilename();

            if($entry->isDir())
            {
                $entries = array_merge($entries,
                                    $this->readDir($path . '/' . $entry->getFilename(),
                                        $relative . $entry->getFilename() . '/')
                            );
            }
        }

        return $entries;
    }

    /**
     * Obtém a instância de aplicação para o brACP.
     *
     * @return brACPApp
     */
    public static function getInstance()
    {
        return self::$app;
    }
}

