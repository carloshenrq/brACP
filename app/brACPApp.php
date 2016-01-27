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
use Model\Login;
use Model\Donation;
use Model\Compensate;
use Model\Recover;
use Model\EmailLog;

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
        $this->view->setCaching(BRACP_TEMPLATE_CACHE);

        // Adiciona os middlewares na rota para serem executados.
        $this->add(new Route());

        // Define a instância global como sendo o proprio.
        self::$app = $this;
    }

    /**
     * Exibe o template a ser chamado.
     *
     * @param string $template Nome do arquivo a ser chamado.
     * @param array $data Dados a serem enviados ao template.
     */
    public function display($template, $data = [], $isAjax = false)
    {
        echo $this->render($template, $data);
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
        // Atribui os dados ao view.
        foreach($data as $key => $value)
        {
            $this->view->assign($key, $value);
        }

        // Se for uma requisição do tipo ajax, adiciona o sulfixo ajax ao nome do template.
        if($ajax && $this->getContainer()->get('request')->isXhr())
            $template .= '.ajax';

        // Renderiza o template.
        return $this->view->fetch($template . '.tpl');
    }

    /**
     * Obtém a instância do smarty para o painel de controle.
     * @return Smarty
     */
    public function getView()
    {
        return $this->view;
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

