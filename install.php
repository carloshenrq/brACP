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

// Se o arquivo de configurações existe, não existe motivos para entrar na tela de instalação
//  do painel.
// @issue 5
if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config.php'))
    exit;

if(!defined('PHP_VERSION'))
    define('PHP_VERSION', phpversion(), false);

// Adicionado leitura da classe principal para teste dos temas.
require_once __DIR__ . '/app/Themes.php';
require_once __DIR__ . '/app/Language.php';
require_once __DIR__ . '/app/Cache.php';

// Obtém todos os temas contidos na pasta
$themes = Themes::readAll();

// Obtém todas as linguagens disponiveis.
$langs = Language::readAll();

// Obtém as permissões de arquivo para notificar o usuário sobre as informações.
$writeable = is_writable(__DIR__);

// Configurações padrão.
$config = [
    // Configurações Gerais
    'BRACP_DEFAULT_TIMEZONE'                => @date_default_timezone_get(),
    'BRACP_URL'                             => 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
    'BRACP_DIR_INSTALL_URL'                 => $_SERVER['REQUEST_URI'],
    'BRACP_TEMPLATE_DIR'                    => __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
    'BRACP_ENTITY_DIR'                      => __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'Entity',
    'BRACP_MD5_PASSWORD_HASH'               => true,
    'BRACP_MAIL_REGISTER_ONCE'              => true,
    'BRACP_CHANGE_MAIL_DELAY'               => 60,
    'BRACP_ALLOW_CHANGE_MAIL'               => true,
    'BRACP_ALLOW_CREATE_ACCOUNT'            => true,
    'BRACP_CONFIRM_ACCOUNT'                 => false,
    'BRACP_ALLOW_ADMIN'                     => true,
    'BRACP_ALLOW_ADMIN_GMLEVEL'             => 99,
    'BRACP_ALLOW_LOGIN_GMLEVEL'             => false,
    'BRACP_ALLOW_ADMIN_CHANGE_PASSWORD'     => false,
    'BRACP_ALLOW_RANKING'                   => true,
    'BRACP_ALLOW_SHOW_CHAR_STATUS'          => true,
    'BRACP_ALLOW_RANKING_ZENY'              => true,
    'BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY'    => true,
    'BRACP_DEVELOP_MODE'                    => false,
    'BRACP_MAINTENCE'                       => false,
    'BRACP_VERSION'                         =>'0.2.1-beta',

    // MySQL
    'BRACP_SQL_DRIVER'                      => 'pdo_mysql',
    'BRACP_SQL_HOST'                        => '127.0.0.1:3306',
    'BRACP_SQL_USER'                        => 'ragnarok',
    'BRACP_SQL_PASS'                        => 'ragnarok',
    'BRACP_SQL_DBNAME'                      => 'ragnarok',

    // Servidor de E-mail
    'BRACP_ALLOW_MAIL_SEND'                 => true,
    'BRACP_MAIL_HOST'                       => '127.0.0.1',
    'BRACP_MAIL_PORT'                       => 25,
    'BRACP_MAIL_USER'                       => 'ragnarok',
    'BRACP_MAIL_PASS'                       => 'ragnarok',
    'BRACP_MAIL_FROM'                       => 'noreply@127.0.0.1',
    'BRACP_MAIL_FROM_NAME'                  => 'noreply',
    'BRACP_NOTIFY_CHANGE_PASSWORD'          => true,
    'BRACP_NOTIFY_CHANGE_MAIL'              => true,
    'BRACP_ALLOW_RECOVER'                   => true,
    'BRACP_RECOVER_BY_CODE'                 => true,
    'BRACP_RECOVER_CODE_EXPIRE'             => 120,
    'BRACP_RECOVER_STRING_LENGTH'           => 8,
    'BRACP_RECOVER_RANDOM_STRING'           => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',

    // reCAPTCHA
    'BRACP_RECAPTCHA_ENABLED'               => true,
    'BRACP_RECAPTCHA_PUBLIC_KEY'            => '',
    'BRACP_RECAPTCHA_PRIVATE_KEY'           => '',
    'BRACP_RECAPTCHA_PRIVATE_URL'           => 'https://www.google.com/recaptcha/api/siteverify',

    // Doações - PayPal
    'PAYPAL_INSTALL'                        => true,
    'PAYPAL_ACCOUNT'                        => '',
    'PAYPAL_CURRENCY'                       => 'BRL',
    'DONATION_AMOUNT_MULTIPLY'              => 100,
    'DONATION_SHOW_NEXT_PROMO'              => true,
    'DONATION_INTERVAL_DAYS'                => 3,

    // Outros
    'BRACP_ALLOW_RESET_APPEAR'              => true,
    'BRACP_ALLOW_RESET_POSIT'               => true,
    'BRACP_ALLOW_RESET_EQUIP'               => true,
    'BRACP_REGEXP_USERNAME'                 => '[a-zA-Z0-9]{4,24}',
    'BRACP_REGEXP_PASSWORD'                 => '[a-zA-Z0-9]{4,20}',
    'BRACP_REGEXP_EMAIL'                    => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}',
    'BRACP_ALLOW_CHOOSE_THEME'              => true,
    'BRACP_DEFAULT_THEME'                   => 'default',
    'BRACP_DEFAULT_LANGUAGE'                => 'pt_BR',

    // Memcache
    'BRACP_MEMCACHE'                        => ((extension_loaded('memcache')) ? 1:0),
    'BRACP_MEMCACHE_SERVER'                 => '127.0.0.1',
    'BRACP_MEMCACHE_PORT'                   => 11211,
    'BRACP_MEMCACHE_EXPIRE'                 => 600,

    // Mods a serem aplicados no painel de controle. (Recomenda-se uso do xdiff, sem isso, tera de ser aplicado manualmente o diff)
    'BRACP_ALLOW_MODS'                      => ((extension_loaded('xdiff')) ? 1:0),
]; 

// Moedas padrões aceitas pelo PayPal.
// @link https://developer.paypal.com/docs/classic/api/currency_codes/
$PPCurrency =  ['AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD',
                'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD',
                'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF',
                'TWD', 'THB', 'TRY', 'USD'];


// Verifica se dados de instalação foram recebidos e pode escrever o arquivo
//  de configuração no disco sem problemas.
if($writeable && isset($_POST) && !empty($_POST))
{
    // Inicializa o cabeçalho do arquivo de configurações que será escrito.
    $configFile = "<?php\n";
    $configFile .= "/**\n";
    $configFile .= " * Arquivo de configuração gerado pela instalação do sistema.\n";
    $configFile .= " */\n";
    $configFile .= "\n";

    // Varre todas as variaveis de configuração para gravar no arquivo.
    foreach($config as $k => $v)
    {
        // Verifica se a chave enviada pelo post existe no arquivo de configuração
        //  se existir, substitui o valor e grava a configuração no arquivo.
        if(array_key_exists($k, $_POST))
            $v = $_POST[$k];

        // Caso necessário adiciona o escape ao valor.
        $v = addslashes($v);

        // Se for apenas valores númericos, então converte para inteiro.
        if(preg_match('/^([0-9]+)$/', $v))
            $configFile .= "DEFINE('{$k}', {$v}, false);\n";
        else
            $configFile .= "DEFINE('{$k}', '{$v}', false);\n";
    }

    $configFile .= "\n";

    // Finaliza o arquivo e escreve os dados no arquivo de configuração.
    file_put_contents('config.php', $configFile);

    header('Refresh: 3');
    header('Content-Type: text/php');
    header('Content-Disposition: attachment; filename="config.php"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize('config.php'));
    readfile('config.php');

    exit;
}

// Definição das mensagens de erro.
$BRACP_ERROR_CODE = 0;
if(version_compare(PHP_VERSION, '5.4.0', '<'))
    $BRACP_ERROR_CODE = 1;
else if (!file_exists('vendor') || !is_dir('vendor') || file_exists('vendor') && !file_exists('composer.lock'))
    $BRACP_ERROR_CODE = 2;
else if(!$writeable)
    $BRACP_ERROR_CODE = 3;

?>
<!DOCTYPE html>
<html>
    <head>
        <title>brACP - Instalação do Painel de Controle</title>

        <!--
            2016-04-14, CHLFZ: Problemas de CHARSET identificado por pelo Sir Will e postado no fórum.
                                -> @issue 7
        -->
        <meta charset="UTF-8">

        <link rel="stylesheet" type="text/css" href="themes/default/css/install.css"/>
        <link rel="stylesheet" type="text/css" href="themes/default/css/button.css"/>

        <script src="js/angular.min.js"></script>
        <script>
            var install = angular.module('brACP', []);

            install.controller('install', ['$scope', function($scope) {

                $scope.Math = window.Math;
                $scope.BRACP_ERROR_CODE = <?php echo $BRACP_ERROR_CODE; ?>;
                $scope.BRACP_ALLOW_INSTALL = $scope.BRACP_ERROR_CODE == 0;
                $scope.BRACP_ALL_TIMEZONE = <?php echo json_encode(timezone_identifiers_list()); ?>;
                $scope.BRACP_ALL_THEMES = <?php echo json_encode($themes->getArrayCopy()); ?>;
                $scope.BRACP_ALL_LANGS = <?php echo json_encode($langs); ?>;
                $scope.BRACP_PAYPAL_CURRENCY = <?php echo json_encode($PPCurrency); ?>;
                $scope.BRACP_ALLOW_MEMCACHE = <?php echo intval(extension_loaded('memcache')); ?>;

                if($scope.BRACP_ALLOW_INSTALL == false)
                {
                    $scope.BRACP_SWITCH = 'error';
                }
                else
                {
                    $scope.BRACP_SWITCH = 'cache';
                }
                $scope.config = <?php echo json_encode($config); ?>;
            }]);

        </script>

    </head>
    <body ng-app="brACP" ng-controller="install">

        <!-- Corpo para dados de instalação -->
        <div class="bracp-install-body">

            <div class="install-title">
                brACP - Programa de Instalação e Configuração
            </div>

            <input type="checkbox" ng-model="BRACP_ALLOW_INSTALL" class="install-cfg-allow"/>
            <!-- Menu de exibição das opções de seleção para a configuração -->
            <div class="bracp-install-menu">
                <ul>
                    <li>
                        <input id="config.home" type="radio" ng-model="BRACP_SWITCH" value="home" class="install-cfg-radio"/>
                        <label for="config.home">Inicio</label>
                    </li>
                    <li>
                        <input id="config.mysql" type="radio" ng-model="BRACP_SWITCH" value="mysql" class="install-cfg-radio"/>
                        <label for="config.mysql">MySQL</label>
                    </li>
                    <li>
                        <input id="config.mail" type="radio" ng-model="BRACP_SWITCH" value="mail" class="install-cfg-radio"/>
                        <label for="config.mail">SMTP</label>
                    </li>
                    <li>
                        <input id="config.recaptcha" type="radio" ng-model="BRACP_SWITCH" value="recaptcha" class="install-cfg-radio"/>
                        <label for="config.recaptcha">reCAPTCHA</label>
                    </li>
                    <li>
                        <input id="config.donation" type="radio" ng-model="BRACP_SWITCH" value="donation" class="install-cfg-radio"/>
                        <label for="config.donation">Doações</label>
                    </li>
                    <li>
                        <input id="config.cache" type="radio" ng-model="BRACP_SWITCH" value="cache" class="install-cfg-radio"/>
                        <label for="config.cache">Cache</label>
                    </li>
                    <li>
                        <input id="config.other" type="radio" ng-model="BRACP_SWITCH" value="other" class="install-cfg-radio"/>
                        <label for="config.other">Outras</label>
                    </li>
                </ul>
            </div>


            <div ng-switch on="BRACP_SWITCH" class="install-body">

                <!-- Bem vindo a instalação do painel de controle. -->
                <div ng-switch-when="home" class="install-content">
                    <h1>Instalação do Painel de Controle</h1>

                    <p>Para que o brACP seja executado de forma correta, você deve informar algumas configurações para tudo seja executado
                    de acordo com o esperado.</p>

                    <p>A Versão que você está desejando instalar é a <strong>{{config.BRACP_VERSION}}</strong> caso existam novas versões,
                        é recomendado usa-las, pois, podem possuir correções de problemas que nesta versão ainda não existe.</p>

                    <div class="install-data">

                        <label class="input-align">
                            <input type="checkbox" ng-model="config.BRACP_MAINTENCE"/>
                            Habilitar Manutenção
                            <span>Durante o modo de manutenção, o acesso ao painel de controle será negado.</span>
                        </label>

                        <label class="input-align">
                            <input type="checkbox" ng-model="config.BRACP_DEVELOP_MODE"/>
                            Modo Desenvolvedor
                            <span>Algumas configurações irão entrar em sandbox, como o paypal e pagseguro.</span>
                        </label>

                        <label class="input-align">
                            <input type="checkbox" ng-model="config.BRACP_MD5_PASSWORD_HASH"/>
                            Usar hash md5 nas senhas.
                            <span>Quando está configuração está habilitada, não é possível recuperar uma senha, apenas gerar novamente.</span>
                            <div ng-switch on="config.BRACP_MD5_PASSWORD_HASH">
                                <span ng-switch-when="true"><strong>É Extremamente recomendado que habilite e configure corretamente o envio de e-mails.</strong></span>
                            </div>
                        </label>

                        <label class="input-align">
                            Configurações de Horário:
                            <select ng-model="config.BRACP_DEFAULT_TIMEZONE">
                                <option ng-repeat="zone in BRACP_ALL_TIMEZONE">{{zone}}</option>
                            </select>
                            <span>Todas as datas e horas salvas no banco de dados serão com base neste campo, escolha com cuidado.</span>
                        </label>

                        <label class="input-align">
                            URL Caminho de Instalação:
                            <input type="text" ng-model="config.BRACP_DIR_INSTALL_URL"/>
                            <span>O Caminho de instalação é como ele aparece no seu domínio. Ex. http://seuro.com<strong><u>/cp/</u></strong></span>
                        </label>

                        <label class="input-align">
                            URL Endereço de Instalação:
                            <input type="text" ng-model="config.BRACP_URL" size="60"/>
                            <span>O Endereço de instalação é o endereço completo com o domínio. Ex. <strong><u>http://seuro.com/cp/</u></strong></span>
                        </label>

                        <label class="input-align">
                            Caminho arquivos de template:
                            <input type="text" ng-model="config.BRACP_TEMPLATE_DIR" size="60"/>
                            <span>O Caminho de arquivos de template é onde estão localizados os arquivos <strong><u>*.tpl</u></strong></span>
                        </label>

                        <label class="input-align">
                            Caminho arquivos de entidade:
                            <input type="text" ng-model="config.BRACP_ENTITY_DIR" size="60"/>
                            <span>O Caminho de arquivos de entidade é onde estão localizados os arquivos para entidades do banco de dados</span>
                        </label>

                        <label class="input-align">
                            <input type="checkbox" ng-model="config.BRACP_ALLOW_MODS"/>
                            Habilita aplicação de MODs
                            <span>Habilita aplicação de modificações customizadas por arquivo ao painel de controle.</span>
                        </label>

                        <label class="input-align">
                            <input type="checkbox" ng-model="config.BRACP_ALLOW_CHOOSE_THEME"/>
                            Habilita usuário mudar o tema
                            <span>Permite que o usuário mude o tema atual.</span>
                        </label>

                        <label class="input-align">
                            Tema padrão:
                            <select ng-model="config.BRACP_DEFAULT_THEME">
                                <option ng-repeat="theme in BRACP_ALL_THEMES" value="{{theme.folder}}">{{theme.name}}</option>
                            </select>
                            <span>Tema que será carregado por padrão ao usuário.</span>
                        </label>

                        <label class="input-align">
                            Idioma padrão:
                            <select ng-model="config.BRACP_DEFAULT_LANGUAGE">
                                <option ng-repeat="lang in BRACP_ALL_LANGS">{{lang}}</option>
                            </select>
                            <span>Tema que será carregado por padrão ao usuário.</span>
                        </label>

                    </div>

                </div>

               <!-- Configurações para o banco de dados -->
                <div ng-switch-when="mysql" class="install-content">
                    <h1>Configurações de acesso ao servidor de Banco de Dados</h1>

                    <p>O brACP trabalha com tabelas em apenas um banco de dados, isso significa que o banco de dados do seu servidor,
                        deve estar junto ao banco de dados de itens, monstros, etc... incluindo as tabelas do brACP.</p>

                    <div class="install-data">

                        <label class="input-align">
                            Servidor:
                            <input type="text" ng-model="config.BRACP_SQL_HOST" size="40"/>
                            <span>Endereço IP ou DNS do servidor de banco de dados que será utilizado.</span>
                        </label>

                        <label class="input-align">
                            Usuário:
                            <input type="text" ng-model="config.BRACP_SQL_USER" size="30"/>
                            <span>Nome de usuário para se conectar ao servidor de banco de dados.</span>
                        </label>

                        <label class="input-align">
                            Senha:
                            <input type="password" ng-model="config.BRACP_SQL_PASS" size="30"/>
                            <span>Senha para o nome de usuário do banco de dados. (Valor padrão: <strong>ragnarok</strong>)</span>
                        </label>

                        <label class="input-align">
                            Banco de Dados:
                            <input type="text" ng-model="config.BRACP_SQL_DBNAME" size="30"/>
                            <span>Nome do banco de dados que será conectado pelo painel de controle.</span>
                        </label>

                    </div>
                </div>

                <!-- Configurações do servidor de e-mail. -->
                <div ng-switch-when="mail" class="install-content">

                    <h1>Configurações de acesso ao servidor SMTP</h1>

                    <div ng-switch on="config.BRACP_MD5_PASSWORD_HASH">
                        <div ng-switch-when="true" class="bracp-message error">
                            <h1>O Uso de md5 nas senhas está habilitado</h1>
                            É extremamente recomendado que você configure o envio de e-mails pois você habilitou o uso de md5 nas senhas.<br>
                            As senhas cadastradas com md5, não podem ser recuperadas, apenas resetadas.
                        </div>
                    </div>

                    <p>Algumas configurações como criação de contas, recuperação de senha, notificação de alterações (senha, e-mail), 
                        dependem de um servidor SMTP configurado para que os e-mails sejam enviados.</p>

                    <div class="install-data">


                        <label class="input-align">
                            <input type="checkbox" ng-model="config.BRACP_ALLOW_MAIL_SEND"/>
                            Habilita o envio de e-mails
                            <span>Permite que o painel de controle use as configurações de SMTP para envio de e-mails.</span>
                        </label>

                        <div ng-switch on="config.BRACP_ALLOW_MAIL_SEND">
                            <div ng-switch-when="true">
                                <br>
                                <label class="input-align">
                                    Servidor:
                                    <input type="text" ng-model="config.BRACP_MAIL_HOST" size="40"/>
                                    <span>Servidor SMTP que será utilizado para envio dos e-mails</span>
                                </label>

                                <label class="input-align">
                                    Porta:
                                    <input type="text" ng-model="config.BRACP_MAIL_PORT" size="4"/>
                                    <span>Porta que será utilizada para conexão com o servidor SMTP</span>
                                </label>

                                <label class="input-align">
                                    Usuário:
                                    <input type="text" ng-model="config.BRACP_MAIL_USER" size="40"/>
                                    <span>Nome de usuário autorizado para uso do servidor SMTP</span>
                                </label>

                                <label class="input-align">
                                    Senha:
                                    <input type="password" ng-model="config.BRACP_MAIL_PASS" size="30"/>
                                    <span>Senha do nome de usuário autorizado no servidor SMTP (Valor padrão: <strong>ragnarok</strong>)</span>
                                </label>

                                <label class="input-align">
                                    Nome do Remetente:
                                    <input type="text" ng-model="config.BRACP_MAIL_FROM_NAME" size="30"/>
                                    <span>Nome do usuário que enviará o e-mail.</span>
                                </label>

                                <label class="input-align">
                                    Endereço do Remetente:
                                    <input type="text" ng-model="config.BRACP_MAIL_FROM" size="50"/>
                                    <span>Endereço de e-mail do remetente.</span>
                                </label>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- Configurações do RECAPTCHA. -->
                <div ng-switch-when="recaptcha" class="install-content">
                    
                    <h1>Configurações do reCAPTCHA</h1>

                    <p>O reCAPTCHA ajuda a deixar os formulários mais seguros, validando a requisição contra os bots de internet.</p>

                    <div class="bracp-message info">
                        Para usar o reCAPTCHA você precisa obter uma chave para o dominio que fará uso desta chave.<br>
                        Você pode obte-la <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">clicando aqui</a>.
                    </div>
                    <br>
                    <div class="install-data">

                        <label class="input-align">
                            <input type="checkbox" ng-model="config.BRACP_RECAPTCHA_ENABLED"/>
                            Habilita reCAPTCHA
                            <span>Permite que o brACP faça validações dos formulários com o uso do reCAPTCHA.</span>
                        </label>

                        <div ng-switch on="config.BRACP_RECAPTCHA_ENABLED">
                            <div ng-switch-when="true">
                                <br>
                                <label class="input-align">
                                    Chave Pública:
                                    <input type="text" ng-model="config.BRACP_RECAPTCHA_PUBLIC_KEY" size="70"/>
                                    <span>Chave pública informada pelo serviço da google.</span>
                                </label>

                                <label class="input-align">
                                    Chave Privada:
                                    <input type="text" ng-model="config.BRACP_RECAPTCHA_PRIVATE_KEY" size="70"/>
                                    <span>Chave privada informada pelo serviço da google.</span>
                                </label>

                                <label class="input-align">
                                    Endereço de Validação:
                                    <input type="text" ng-model="config.BRACP_RECAPTCHA_PRIVATE_URL" size="70"/>
                                    <span>Endereço para validação dos dados, não altere se não tiver certeza do que está fazendo.</span>
                                </label>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- Configurações do DONATION. -->
                <div ng-switch-when="donation" class="install-content">
                    
                    <h1>Configurações de Doação</h1>

                    <p>Algumas configurações padrões para qualquer serviço de doação implementado.</p>

                    <div class="install-data">
                        <label class="input-align">
                            Multiplicador proporcional:
                            <input type="text" ng-model="config.DONATION_AMOUNT_MULTIPLY" size="10"/>
                            <span>Ao doar <strong>{{config.PAYPAL_CURRENCY}} 1.00</strong> recebe <strong>{{(config.DONATION_AMOUNT_MULTIPLY * 1)}} Bônus</strong></span>
                        </label>

                        <label class="input-align">
                            <input type="checkbox" ng-model="config.DONATION_SHOW_NEXT_PROMO"/>
                            Habilitar exibição de próximas promoções
                            <span>Permite que o brACP informe ao jogador quando serão as próximas promoções de bônus</span>
                        </label>

                        <div ng-switch on="config.DONATION_SHOW_NEXT_PROMO">
                            <div ng-switch-when="true">
                                <br>
                                <label class="input-align">
                                    Dias para próximas promoções:
                                    <input type="text" ng-model="config.DONATION_INTERVAL_DAYS" size="10"/>
                                    <span>Exibe todas as promoções para daqui <strong>{{config.DONATION_INTERVAL_DAYS}}</strong> dias.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <p class="bracp-message info">Por enquanto, somente o <strong>PayPal</strong> está habilitado para formas de doações.</p>

                    <div class="install-data">
                        <label class="input-align">
                            <input type="checkbox" ng-model="config.PAYPAL_INSTALL"/>
                            Habilitar doações por PayPal
                            <span>Permite que o servidor receba doações pelo PayPal.</span>
                        </label>

                        <div ng-switch on="config.PAYPAL_INSTALL">
                            <div ng-switch-when="true">
                                <br>
                                <label class="input-align">
                                    E-mail:
                                    <input type="text" ng-model="config.PAYPAL_ACCOUNT" size="40"/>
                                    <span>Endereço de e-mail que receberá as doações por PayPal</span>
                                </label>
                                <label class="input-align">
                                    Moeda de doação:
                                    <select ng-model="config.PAYPAL_CURRENCY">
                                        <option ng-repeat="currency in BRACP_PAYPAL_CURRENCY">{{currency}}</option>
                                    </select>
                                    <span>Endereço de e-mail que receberá as doações por PayPal</span>
                                </label>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Configurações do cache. -->
                <div ng-switch-when="cache" class="install-content">
                    
                    <h1>Configurações de Cache</h1>

                    <div ng-switch on="BRACP_ALLOW_MEMCACHE">

                        <div ng-switch-when="1">
                            
                            <p>O Uso do servidor de cache permite que algumas páginas que tenham intenso acesso ao banco de dados
                                sejam "protegidas" por fazer o uso do cache.</p>

                            <div class="install-data">
                                <label class="input-align">
                                    <input type="checkbox" ng-model="config.BRACP_MEMCACHE"/>
                                    Habilitar uso de servidor de Cache
                                    <span>Permite que o brACP faça uso de servidor de cache para algumas informações.</span>
                                </label>

                                <div ng-switch on="config.BRACP_MEMCACHE">
                                    <div ng-switch-when="true">
                                        <br>
                                        <label class="input-align">
                                            Servidor:
                                            <input type="text" ng-model="config.BRACP_MEMCACHE_SERVER" size="30"/>
                                            <span>Endereço do servidor de cache.</span>
                                        </label>

                                        <label class="input-align">
                                            Porta:
                                            <input type="text" ng-model="config.BRACP_MEMCACHE_PORT" size="5"/>
                                            <span>Número da porta para conexão com o servidor de cache.</span>
                                        </label>

                                        <label class="input-align">
                                            Tempo de Validade:
                                            <input type="text" ng-model="config.BRACP_MEMCACHE_EXPIRE" size="8"/>
                                            <span>Tempo (em segundos) que o valor do cache será salvo no servidor.<br><strong>{{Math.floor(config.BRACP_MEMCACHE_EXPIRE/60)}} minuto(s) e {{(config.BRACP_MEMCACHE_EXPIRE%60)}} segundo(s)</strong></span>
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div ng-switch-when="0">
                            <div class="bracp-message error">
                                A Configuração de cache não pode ser realizada, o seu PHP não possui a biblioteca <strong>memcache</strong>
                                carregada.<br>
                                <br>
                                <a href="http://php.net/manual/pt_BR/book.memcache.php" target="_blank">Clicando aqui</a>, talvez te ajude um pouco.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Configurações do other. -->
                <div ng-switch-when="other" class="install-content">
                    Configurar OTHER.
                </div>

                <!-- Bem vindo a instalação do painel de controle. -->
                <div ng-switch-default class="install-content">
                    <div class="bracp-message error">
                        <div class="header">
                            Falha de verificação <span class="sub-title">(Código: {{BRACP_ERROR_CODE}})</span>
                        </div>
                        
                        <div ng-switch on="BRACP_ERROR_CODE">
                            <div ng-switch-when="1">
                                Sua versão de instalação do PHP é inferior a requerida para execução do painel de controle.<br>
                                A Versão minima para execução é a 5.4.0 ou superior.<br>
                                <br>
                                <strong><i>Hey, psiu! Talvez isso te ajude:
                                <a href="http://php.net/" target="_blank">PHP.net</a></i></strong> 
                            </div>

                            <div ng-switch-when="2">
                                Os arquivos do composer não foram baixados!<br>
                                Verifique sua instalação e tente novamente.<br>
                                <br>
                                <strong><i>Hey, psiu! Talvez isso te ajude:
                                <a href="http://getcomposer.org/" target="_blank">Composer</a>.</i></strong>
                             </div>

                            <div ng-switch-when="3">
                                O Programa de instalação não será capaz criar o arquivo de instalação se você enviar os dados.<br>
                                Verifique as permissões do diretório e tente novamente.<br>
                                <br>
                                Talvez isso ajude!<br>
                                <strong>chmod -R 0777</strong> 
                             </div>

                             <div ng-switch-default>
                                -- UNKNOW ERROR --
                             </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>


    </body>
</html>