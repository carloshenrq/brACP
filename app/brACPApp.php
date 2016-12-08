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
    private $em = [];

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
     * Estado do servidor.
     *
     * @var Model\ServerStatus
     */
    private $server_status;

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
            ],
            'notFoundHandler' => function($c) {
                return function($request, $response) {

                    brACPApp::getInstance()->display('error.404');

                    return $response;
                };
            }
        ];

        // Inicializa o sistema de cache.
        Cache::init();

        // Variavel de sessão para definir a linguagem padrão do painel de controle.
        if(!isset($this->session->BRACP_LANGUAGE))
            $this->session->BRACP_LANGUAGE = BRACP_DEFAULT_LANGUAGE;

        // Inicializa a linguagem.
        $this->language = new Language($this->session->BRACP_LANGUAGE);

        // Servidor de banco de dados selecionado pelo usuário.
        if(!isset($this->session->BRACP_SVR_SELECTED)
            || $this->session->BRACP_SVR_SELECTED < 0 && $this->session->BRACP_SVR_SELECTED >= BRACP_SRV_COUNT)
            $this->session->BRACP_SVR_SELECTED = BRACP_SRV_DEFAULT;

        // Verifica se a variavel de tema para a sessão está definida.
        // Se não estiver, define como a do tema padrão.
        if(!isset($this->session->BRACP_THEME))
            $this->session->BRACP_THEME = BRACP_DEFAULT_THEME;

        // Verifica se existe a necessidade de chamar o reCaptcha
        //  para o jogador, armazena as informações na sessão. Após 3 atualizações
        //  passa a chamar o reCaptcha ao jogador.
        // -> Só depois de 5 acertos de reCaptcha que a var é zerada.
        if(!isset($this->session->BRACP_RECAPTCHA_ERROR_REQUEST))
            $this->session->BRACP_RECAPTCHA_ERROR_REQUEST = 0;

        if(!isset($this->session->BRACP_RECAPTCHA_SOLVED_REQUEST))
            $this->session->BRACP_RECAPTCHA_SOLVED_REQUEST = 0;

        // Loads the default settings for this app.
        parent::__construct($configs);

        // Adiciona os middlewares na rota para serem executados.
        $this->add(new Route($this));
        $this->add(new ServerPing($this));
        $this->add(new Database($this));

        // Define a instância global como sendo o proprio.
        self::$app = $this;

        // Cria a instância do smarty.
        $this->view = new brACPSmarty;
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
        if(!BRACP_RECAPTCHA_ENABLED || empty($response))
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

        // Atualiza a quantidade de soluções realizadas para que as próximas requisições do usuário sejam validadas
        //  com sucesso e que a necessidade do captcha seja removida.
        if($this->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3 && $captchaResponse->success == 1)
        {
            // Cada erro de reCaptcha custará mais duas requisições para o usuário.
            // -> Acertou 4 errou 1, Precisa validar +3 pra passar
            //    Se errar +1, precisa acertar +5 pra validar, e assim vai...
            if($captchaResponse->success == 1)
                $this->getSession()->BRACP_RECAPTCHA_SOLVED_REQUEST++;
            else
                $this->getSession()->BRACP_RECAPTCHA_SOLVED_REQUEST -= 2;

            // Caso tenha resolvido 5 captchas seguidos, então remove a necessidade do captcha para a sessão.
            if($this->getSession()->BRACP_RECAPTCHA_SOLVED_REQUEST >= 5)
            {
                $this->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST =
                $this->getSession()->BRACP_RECAPTCHA_SOLVED_REQUEST = 0;
            }
        }

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
        $message = \Swift_Message::newInstance($this->getLanguage()->getTranslate($subject))
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
    public function display($template, $data = [], $ajax = false)
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
    public function render($template, $data = [], $ajax = false)
    {
        try
        {
            // Verifica se o usuário está logado no sistema.
            if(Account::isLoggedIn())
            {
                // obtém o objeto da conta do jogador.
                $account = Account::loggedUser();

                // Obtém o tempo que o jogador está sem realizar a alteração de senha.
                $password_change = time() - $account->getLast_password_change();

                // Envia os dados padrões caso o usuário esteja logado.
                $data = array_merge(['userid' => $account->getUserId(),
                                     'account' => $account,
                                     'gravatar' => 'https://www.gravatar.com/avatar/' . hash('md5', strtolower(trim($account->getEmail()))),
                                     'password_change' => $password_change ], $data);
            }
        }
        catch(\Exception $ex)
        {
            // Irá acontecer quando existir um erro de conexão com o banco de dados.
            if(!isset( $data['exception']))
                $data['exception'] = $ex;
        }

        // Adicionado estado do servidor que está selecionado para
        //  os dados. [CHLFZ, 2016-06-16]
        $data = array_merge([
            'serverStatus' => $this->getServerStatus()
        ], $data);

        // Correção: Quando não há conexão com o banco de dados, é impossível
        //  fazer a leitura dos temas. [CHLFZ, 2016-05-20]
        try
        {
            // Obtém todos os temas que estão em cache no banco de dados.
            $themes = Cache::get('BRACP_THEMES', function() {
                return brACPApp::getInstance()->getEm('cp')->getRepository('Model\Theme')->findAll();
            });
        }
        catch(\Exception $ex)
        {
            echo '<span style="color: white">', $ex->getMessage(), '</span>';
            $themes = [];
        }

        // Adiciona o navegador aos dados para o template.
        $data = array_merge($data, [
            'themes' => $themes,
            'langs' => Language::readAll(),
            'session' => $this->getSession(),
            'navigator' => Navigator::getBrowser($this->getUserAgent()),
            'ipAddress' => $this->getIpAddress(),

            'userNameFormat' => (((BRACP_REGEXP_FORMAT&0x10) == 0x10) ? 'NORMAL' : (((BRACP_REGEXP_FORMAT&0x20) == 0x20) ? 'SPECIAL':'ALL')),
            'passWordFormat' => (((BRACP_REGEXP_FORMAT&0x01) == 0x01) ? 'NORMAL' : (((BRACP_REGEXP_FORMAT&0x02) == 0x02) ? 'SPECIAL':'ALL')),

            'needRecaptcha' =>  $this->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3,
        ]);

        // Atribui os dados ao smarty.
        $this->view->assign($data);

        // Se for uma requisição do tipo ajax, adiciona o sulfixo ajax ao nome do template.
        if($ajax && $this->getContainer()->get('request')->isXhr())
            $template .= '.ajax';

        // Renderiza o template.
        return $this->view->fetch($template . '.tpl');
    }

    /**
     * Método para criar os logs necessários para informações do IP como:
     * -> Cidade, pais, etc... (Os dados podem não ser completamente precisos, é mais para estatistica de regiões e etc...)
     */
    public function logIpDetails()
    {
        // Configuração desativada, não é necessário finalizar informações de log.
        if(!BRACP_LOG_IP_DETAILS)
            return;

        // Obtém o endereço ip do jogador.
        $ipAddress = $this->getIpAddress();
        $userAgent = $this->getUserAgent();

        // Verifica no banco de dados se o ip já foi cadastrado e se já é maior que
        // 1 dia para realizar o log novamente.
        $log = $this->getCpEm()
                    ->createQuery('
                        SELECT
                            log 
                        FROM
                            Model\IpAddress log
                        WHERE
                            log.ipAddress = :ipAddress
                                AND
                            log.userAgent = :userAgent
                                AND
                            DATE_DIFF(CURRENT_DATE(), log.dtLog) = 0
                    ')
                    ->setParameter(':ipAddress', $ipAddress)
                    ->setParameter(':userAgent', $userAgent)
                    ->getOneOrNullResult();
        
        // Se está NULL (não foi encontrado ou o prazo de 1 dia já passou..., então é necessário criar o registro do
        // ip no banco de dados...
        if(is_null($log))
        {
            // Obtém os dados do webservice para gravar no banco de dados.
            $ipDetails = json_decode(Request::create('http://ipinfo.io/')
                ->get($ipAddress)->getBody()->getContents());

            $log = new \Model\IpAddress;

            $log->setIpAddress($ipDetails->ip);
            $log->setUserAgent($userAgent);
            if(!isset($ipDetails->bogon))
            {
                $log->setHostname($ipDetails->hostname);
                $log->setCity($ipDetails->city);
                $log->setRegion($ipDetails->region);
                $log->setCountry($ipDetails->country);
                $log->setLocation($ipDetails->loc);
                $log->setOrigin($ipDetails->org);
            }
            else
            {
                $log->setHostname($ipDetails->ip);
                $log->setCity('intranet');
                $log->setRegion('intranet');
                $log->setCountry('??');
                $log->setLocation('intranet');
                $log->setOrigin('intranet');
            }
            $log->setDtLog(date('Y-m-d H:i:s'));

            $this->getCpEm()->persist($log);
            $this->getCpEm()->flush();
        }

        return;
    }

    /**
     * Recebe o endereço IP do cliente que está realizando a requisição.
     *
     * @return string
     */
    private function getIpAddress()
    {
        // Possiveis variaveis para se obter o endereço ip do cliente.
        // issue #10: HTTP_CF_CONNECTING_IP-> Usuário usando proteção do cloudfire.
        $_vars = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
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
     * Obtém o user agent para a requisição atual.
     *
     * @return string
     */
    private function getUserAgent()
    {
        return $this->getContainer()->get('request')->getHeader('user-agent')[0];
    }

    /**
     * Define o entitymanager.
     *
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em, $name = 'cp')
    {
        $this->em[$name] = $em;
        return $this;
    }

    /**
     * Obtém o EntityManager.
     *
     * @return EntityManager
     */
    public function getEm($name = 'cp', $throw = true)
    {
        // Se não houver EntityManager, emite uma exceção para tratamento
        //  do erro.
        if(!isset($this->em[$name]))
            if($throw === true)
                throw new \Exception('EntityManager not defined.');
            else
                return null;

        return $this->em[$name];
    }

    /**
     * Obtém o conector com o banco de dados para os monstros, itens etc...
     *
     * @return object
     */
    public function getDbEm()
    {
        return $this->getEm('db');
    }

    /**
     * Obtém o conector com o banco de dados para o servidor da CP.
     *
     * @return object
     */
    public function getCpEm()
    {
        return $this->getEm('cp');
    }

    /**
     * Obtém o conector com o banco de dados para o servidor selecionado pelo jogador.
     *
     * @return object
     */
    public function getSvrEm()
    {
        return $this->getEm('SV' . $this->getSession()->BRACP_SVR_SELECTED);
    }

    /**
     * Obtém o conector com o banco de dados para o servidor de contas.
     *
     * @return object
     */
    public function getSvrDftEm()
    {
        return $this->getEm('SV' . BRACP_SRV_DEFAULT);
    }

    /**
     * Obtém o define do servidor para o jogador conectado.
     */
    public function setServerStatus($server_status)
    {
        $this->server_status = $server_status;
        return $this;
    }

    /**
     * Obtém o estado do servidor para o jogador conectado.
     */
    public function getServerStatus()
    {
        return $this->server_status;
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
     * Obtém o objeto de linguagem que contém os dados de tradução para o painel.
     *
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Verifica a necessidade de testar o recaptcha.
     *
     * @return boolean Se está tudo certo.
     */
    public function testRecaptcha($post)
    {
        // Verifica se o recaptcha está habilitado e ok.
        if(!BRACP_RECAPTCHA_ENABLED)
            return true;

        // Verifica a necessidade do teste.
        $needRecaptcha = $this->getSession()->BRACP_RECAPTCHA_ERROR_REQUEST >= 3;

        // Se o recaptcha for necessário então realiza o teste
        if($needRecaptcha && isset($post['recaptcha']))
            return $this->checkReCaptcha($post['recaptcha']);

        return !$needRecaptcha;
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

