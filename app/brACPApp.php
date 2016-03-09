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
     * Construtor e inicializador para o painel de controle.
     */
    public function __construct()
    {
        // Initialize session for this app.
        session_cache_limiter(false);
        session_start();

        // Loads the default settings for this app.
        parent::__construct();

        // Cria a instância do smarty.
        $this->view = new Smarty;
        $this->view->setTemplateDir(BRACP_TEMPLATE_DIR);
        $this->view->setCaching(false);

        // Adiciona os middlewares na rota para serem executados.
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

        // Mescla o array atual com os dados de envio.
        $data = array_merge(['ipAddress' => $this->getContainer()->get('request')->getAttribute('ip_address')], $data);

        // Transporte para o email.
        $transport = \Swift_SmtpTransport::newInstance(BRACP_MAIL_HOST, BRACP_MAIL_PORT)
                                            ->setUsername(BRACP_MAIL_USER)
                                            ->setPassword(BRACP_MAIL_PASS);
        // Mailer para envio dos dados.
        $mailer = \Swift_Mailer::newInstance($transport);
        // Mensagem para enviar.
        $message = \Swift_Message::newInstance($subject)
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
                $data = array_merge(['account' => Account::loggedUser()], $data);
        }
        catch(\Exception $ex)
        {
            // Irá acontecer quando existir um erro de conexão com o banco de dados.
            if(!isset( $data['exception']))
                $data['exception'] = $ex;
        }

        // Obtém o endereço de ip do cliente.
        $ip_address = $this->getContainer()->get('request')->getAttribute('ip_address');

        // Adiciona o navegador aos dados para o template.
        $data = array_merge($data, [
            'navigator' => Navigator::getBrowser($this->getContainer()->get('request')->getHeader('user-agent')[0]),
            'ipAddress' => ((is_null($ip_address)) ? $_SERVER['REMOTE_ADDR'] : $ip_address)
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
     * Obtém a instância de aplicação para o brACP.
     *
     * @return brACPApp
     */
    public static function getInstance()
    {
        return self::$app;
    }
}

