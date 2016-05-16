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
    'BRACP_MD5_PASSWORD_HASH'               => 1,
    'BRACP_MAIL_REGISTER_ONCE'              => 1,
    'BRACP_CHANGE_MAIL_DELAY'               => 60,
    'BRACP_ALLOW_CHANGE_MAIL'               => 1,
    'BRACP_ALLOW_CREATE_ACCOUNT'            => 1,
    'BRACP_CONFIRM_ACCOUNT'                 => 0,
    'BRACP_ALLOW_ADMIN'                     => 1,
    'BRACP_ALLOW_ADMIN_GMLEVEL'             => 99,
    'BRACP_ALLOW_LOGIN_GMLEVEL'             => 0,
    'BRACP_ALLOW_ADMIN_CHANGE_PASSWORD'     => 0,
    'BRACP_ALLOW_RANKING'                   => 1,
    'BRACP_ALLOW_SHOW_CHAR_STATUS'          => 1,
    'BRACP_ALLOW_RANKING_ZENY'              => 1,
    'BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY'    => 1,
    'BRACP_DEVELOP_MODE'                    => 0,
    'BRACP_MAINTENCE'                       => 0,
    'BRACP_VERSION'                         =>'0.2.1-beta',

    // MySQL
    'BRACP_SQL_DRIVER'                      => 'pdo_mysql',
    'BRACP_SQL_HOST'                        => '127.0.0.1:3306',
    'BRACP_SQL_USER'                        => 'ragnarok',
    'BRACP_SQL_PASS'                        => 'ragnarok',
    'BRACP_SQL_DBNAME'                      => 'ragnarok',

    // Servidor de E-mail
    'BRACP_ALLOW_MAIL_SEND'                 => 1,
    'BRACP_MAIL_HOST'                       => '127.0.0.1',
    'BRACP_MAIL_PORT'                       => 25,
    'BRACP_MAIL_USER'                       => 'ragnarok',
    'BRACP_MAIL_PASS'                       => 'ragnarok',
    'BRACP_MAIL_FROM'                       => 'noreply@127.0.0.1',
    'BRACP_MAIL_FROM_NAME'                  => 'noreply',
    'BRACP_NOTIFY_CHANGE_PASSWORD'          => 1,
    'BRACP_NOTIFY_CHANGE_MAIL'              => 1,
    'BRACP_ALLOW_RECOVER'                   => 1,
    'BRACP_RECOVER_BY_CODE'                 => 1,
    'BRACP_RECOVER_CODE_EXPIRE'             => 120,
    'BRACP_RECOVER_STRING_LENGTH'           => 8,
    'BRACP_RECOVER_RANDOM_STRING'           => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',

    // reCAPTCHA
    'BRACP_RECAPTCHA_ENABLED'               => 1,
    'BRACP_RECAPTCHA_PUBLIC_KEY'            => '',
    'BRACP_RECAPTCHA_PRIVATE_KEY'           => '',
    'BRACP_RECAPTCHA_PRIVATE_URL'           => 'https://www.google.com/recaptcha/api/siteverify',

    // Doações - PayPal
    'PAYPAL_INSTALL'                        => 1,
    'PAYPAL_ACCOUNT'                        => '',
    'PAYPAL_CURRENCY'                       => 'BRL',
    'DONATION_AMOUNT_MULTIPLY'              => 100,
    'DONATION_SHOW_NEXT_PROMO'              => 1,
    'DONATION_INTERVAL_DAYS'                => 3,

    // Outros
    'BRACP_ALLOW_RESET_APPEAR'              => 1,
    'BRACP_ALLOW_RESET_POSIT'               => 1,
    'BRACP_ALLOW_RESET_EQUIP'               => 1,
    'BRACP_REGEXP_USERNAME'                 => '[a-zA-Z0-9]{4,24}',
    'BRACP_REGEXP_PASSWORD'                 => '[a-zA-Z0-9]{4,20}',
    'BRACP_REGEXP_EMAIL'                    => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}',
    'BRACP_ALLOW_CHOOSE_THEME'              => 1,
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
                $scope.BRACP_SWITCH = 'home';
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
                <div ng-switch-when="home">
                    Bem vindo.<br>
                </div>

               <!-- Configurações para o banco de dados -->
                <div ng-switch-when="mysql">
                    Configurar MySQL.
                </div>

                <!-- Configurações do servidor de e-mail. -->
                <div ng-switch-when="mail">
                    Configurar EMAIL.
                </div>

                <!-- Configurações do RECAPTCHA. -->
                <div ng-switch-when="recaptcha">
                    Configurar RECAPTCHA.
                </div>

                <!-- Configurações do DONATION. -->
                <div ng-switch-when="donation">
                    Configurar DONATION.
                </div>

                <!-- Configurações do cache. -->
                <div ng-switch-when="cache">
                    Configurar CACHE.
                </div>

                <!-- Configurações do other. -->
                <div ng-switch-when="other">
                    Configurar OTHER.
                </div>

                <!-- Bem vindo a instalação do painel de controle. -->
                <div ng-switch-default>
                    ERROR
                </div>

            </div>
        </div>


    </body>
</html>