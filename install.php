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

// Verify if the dependencies from composer are installed.
if(!is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'vendor'))
{
    echo 'Dependencies not found. (Run \'composer install\')';
    exit;
}

$root = $_SERVER['DOCUMENT_ROOT'];
$dirc = str_replace('\\', '/', __DIR__) . '/';
$BRACP_INSTALL_URL = substr($dirc, strlen($root));

if(hash('md5', $_SERVER['REQUEST_URI']) != hash('md5', $BRACP_INSTALL_URL))
{
    header('Location: '.$BRACP_INSTALL_URL);
    exit;
}

// Carrega as informações do APP
require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if(!defined('PHP_VERSION'))
    define('PHP_VERSION', phpversion(), false);

// Adicionado leitura da classe principal para teste dos temas.
require_once __DIR__ . '/app/TMod.php';
require_once __DIR__ . '/app/Themes.php';
require_once __DIR__ . '/app/Language.php';
require_once __DIR__ . '/app/Cache.php';

// Teste de bibliotecas para ver se está ok em salvar os dados.
$PRE_REQUISITES = array(
    'phpversion'        => version_compare(PHP_VERSION, '5.5.0') > 0,
    'is_writable'       => is_writable(__DIR__),
    'composer'          => is_dir('vendor'),
    'curl_ext'          => extension_loaded('curl'),
    'hash_ext'          => extension_loaded('hash'),
    'json_ext'          => extension_loaded('json'),
    'xml_ext'           => extension_loaded('xml'),
    'libxml_ext'        => extension_loaded('libxml'),
    'openssl_ext'       => extension_loaded('openssl'),
    'pcre_ext'          => extension_loaded('pcre'),
    'PDO_ext'           => extension_loaded('PDO'),
    'pdo_mysql_ext'     => extension_loaded('pdo_mysql'),
    'sockets_ext'       => extension_loaded('sockets'),
    'zip_ext'           => extension_loaded('zip'),
    'sqlite_ext'        => extension_loaded('pdo_sqlite'),
);

$PRE_MemCached = extension_loaded('memcache');

// Teste para as variaveis de configuração do painel de controle.
$_CONFIG_DATA = array(

    // Informações de versão
    'BRACP_VERSION'                         => '0.2.2-beta',

    // Endereços (STEP=3)
    'BRACP_URL'                             =>  'http' . ((isset($_SERVER['HTTPS'])) ? 's':'') . '://' . $_SERVER['HTTP_HOST'] . $BRACP_INSTALL_URL,
    'BRACP_DIR_INSTALL_URL'                 =>  $BRACP_INSTALL_URL,
    'BRACP_TEMPLATE_DIR'                    =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates',
    'BRACP_ENTITY_DIR'                      =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Model',
    'BRACP_DEFAULT_TIMEZONE'                =>  @date_default_timezone_get(),

    // Expressões regulares (STEP=4)
    'BRACP_REGEXP_EMAIL'                    => '[a-z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}',

    // Recaptcha (STEP=5)
    'BRACP_RECAPTCHA_ENABLED'               => false,
    'BRACP_RECAPTCHA_PRIVATE_KEY'           => '',
    'BRACP_RECAPTCHA_PUBLIC_KEY'            => '',
    'BRACP_RECAPTCHA_PRIVATE_URL'           => 'https://www.google.com/recaptcha/api/siteverify',

    // Servidor de e-mail (STEP=6)
    'BRACP_ALLOW_MAIL_SEND'                 => false,
    'BRACP_MAIL_HOST'                       => '',
    'BRACP_MAIL_PORT'                       => 25,
    'BRACP_MAIL_USER'                       => '',
    'BRACP_MAIL_PASS'                       => '',
    'BRACP_MAIL_FROM'                       => '',
    'BRACP_MAIL_FROM_NAME'                  => '',

    // Configurações de conta. (STEP=7)
    'BRACP_ALLOW_CREATE_ACCOUNT'            => true,
    'BRACP_MD5_PASSWORD_HASH'               => false,
    'BRACP_MAIL_REGISTER_ONCE'              => false,
    'BRACP_ALLOW_CHANGE_MAIL'               => false,
    'BRACP_CHANGE_MAIL_DELAY'               => 60,
    'BRACP_CONFIRM_ACCOUNT'                 => false,
    'BRACP_ALLOW_RECOVER'                   => false,
    'BRACP_RECOVER_BY_CODE'                 => false,
    'BRACP_RECOVER_CODE_EXPIRE'             => 120,
    'BRACP_RECOVER_STRING_LENGTH'           => 8,
    'BRACP_RECOVER_RANDOM_STRING'           => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
    'BRACP_NOTIFY_CHANGE_PASSWORD'          => false,
    'BRACP_NOTIFY_CHANGE_MAIL'              => false,
    'BRACP_ALLOW_LOGIN_GMLEVEL'             => 0,
    'BRACP_ALLOW_ADMIN_GMLEVEL'             => 99,
    'BRACP_ALLOW_ADMIN_CHANGE_PASSWORD'     => false,

    // Configurações de banco de dados do brACP (STEP=8)
    'BRACP_SQL_CP_DRIVER'                   => 'pdo_mysql',
    'BRACP_SQL_CP_HOST'                     => '127.0.0.1',
    'BRACP_SQL_CP_USER'                     => 'bracp',
    'BRACP_SQL_CP_PASS'                     => 'bracp',
    'BRACP_SQL_CP_DBNAME'                   => 'bracp',

    // Configurações de banco de dados de monstros e itens (STEP=9)
    'BRACP_SQL_DB_DRIVER'                   => 'pdo_mysql',
    'BRACP_SQL_DB_HOST'                     => '127.0.0.1',
    'BRACP_SQL_DB_USER'                     => 'ragnarok',
    'BRACP_SQL_DB_PASS'                     => 'ragnarok',
    'BRACP_SQL_DB_DBNAME'                   => 'ragnarok',

    // Configurações de geração de cache. (STEP=11)
    'BRACP_MEMCACHE'                        => extension_loaded('memcache'),
    'BRACP_MEMCACHE_HOST'                   => '127.0.0.1',
    'BRACP_MEMCACHE_PORT'                   => 11211,
    'BRACP_MEMCACHE_EXPIRE'                 => 600,

    // Configurações de sessão e segurança de sessão  (STEP=12)
    'BRACP_SESSION_SECURE'                  => true,
    'BRACP_SESSION_ALGO'                    => 'AES-256-ECB',
    'BRACP_SESSION_KEY'                     => '',
    'BRACP_SESSION_IV'                      => '',

    // Outras configurações (STEP=13)
    'BRACP_DEVELOP_MODE'                    => false,
    'BRACP_MAINTENCE'                       => false,
    'BRACP_ALLOW_ADMIN'                     => false,
    'BRACP_ALLOW_CHOOSE_THEME'              => true,
    'BRACP_ALLOW_RANKING'                   => true,
    'BRACP_ALLOW_RANKING_ZENY'              => true,
    'BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY'    => true,
    'BRACP_ALLOW_SHOW_CHAR_STATUS'          => false,
    'BRACP_DEFAULT_THEME'                   => 'classic',
    'BRACP_DEFAULT_LANGUAGE'                => 'pt_BR',
    'BRACP_SRV_PING_DELAY'                  => 300,
    'BRACP_ALLOW_VENDING'                   => false,
    'BRACP_ALLOW_MODS'                      => false,
    'BRACP_MODS_DIR'                        => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'mods',
    'BRACP_ALLOW_EXTERNAL_REQUEST'          => false,
    'BRACP_PASS_CHANGE_ALERT'               => 30,
    'BRACP_LOG_IP_DETAILS'                  => false,
);

// Informações sobre criptografia de sessão.
$_CONFIG_CIPHER = array();

if(extension_loaded('openssl'))
{
    foreach(openssl_get_cipher_methods() as $cipher)
    {
        if(in_array($cipher, $_CONFIG_CIPHER))
            continue;

        $password = base64_encode(openssl_random_pseudo_bytes(32));
        $iv = null;
        $iv_len = openssl_cipher_iv_length($cipher);

        if($iv_len > 0)
            $iv = base64_encode(openssl_random_pseudo_bytes($iv_len));

        $_CONFIG_CIPHER[$cipher] = (object)array(
            'password'  => $password,
            'iv'        => $iv
        );

        unset($password, $iv, $iv_len);
    }
}

// Obtém todos os temas e linguagens que estão na pasta.
$themes = Themes::readAll();
$langs = Language::readAll();

// Carrega os dados de SCSS
$scss = new \Leafo\ScssPhp\Compiler;
$scss->setVariables([
    'data_path'     => $_SERVER['REQUEST_URI'] . 'data',
    'theme_path'    => $_SERVER['REQUEST_URI'] . 'themes/classic'
]);
$scss->addImportPath('themes/classic');

$scss_message = file_get_contents('themes/classic/message.scss');
$scss_install = file_get_contents('themes/classic/install.scss');
$scss_button = file_get_contents('themes/classic/button.scss');
$scss_modal = file_get_contents('themes/classic/modal.scss');

$css_message = $scss->compile($scss_message);
$css_install = $scss->compile($scss_install);
$css_button = $scss->compile($scss_button);
$css_modal = $scss->compile($scss_modal);

// Carrega o minifier do css.
$css_minify = new MatthiasMullie\Minify\CSS;
$css_minify->add($css_message);
$css_minify->add($css_install);
$css_minify->add($css_button);
$css_minify->add($css_modal);

$css_style = $css_minify->minify();

$js_angular = file_get_contents('js/angular.js');
$js_jquery = file_get_contents('js/jquery-2.1.4.js');

$js_minify = new MatthiasMullie\Minify\JS;
$js_minify->add($js_angular);
$js_minify->add($js_jquery);

$js_content = $js_minify->minify();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>brACP - Instalação do Painel de Controle</title>

        <!--
            2016-04-14, CHLFZ: Problemas de CHARSET identificado por pelo Sir Will e postado no fórum.
                                -> @issue 7
        -->
        <meta charset="UTF-8"/>

        <style><?php echo $css_style; ?></style>

        <script><?php echo $js_content; ?></script>

        <script>
            var install = angular.module('brACP', []);

            install.controller('install', ['$scope', '$http', '$sce', function($scope, $http, $sce) {

                // Status da instalação.
                $scope.INSTALL_STATE = 0;
                $scope.INSTALL_MESSAGE = '';

                $scope.STEP = 1;
                $scope.STEP_ERROR   = [];
                $scope.STEP_WARNING = [];
                $scope.STEP_SUCCESS = [];

                $scope.PRE_REQUISITES = <?php echo json_encode($PRE_REQUISITES); ?>;
                $scope.ACCEPT_TERMS = false;
                $scope.MEMCACHE_LOADED = <?php echo (($PRE_MemCached == true) ? 'true' : 'false'); ?>;

                // Variaveis de configuração inicial.
                $scope.INSTALL_VARS = <?php echo json_encode($_CONFIG_DATA); ?>;
                $scope.SESSION_ALGO_KEY_IV = <?php echo json_encode($_CONFIG_CIPHER); ?>;

                // Variavel de configuração para os servidores.
                $scope.BRACP_SERVERS = [];

                // Configuração para expressão regular de usuários.
                $scope.REGEXP_USERNAME = '10';
                $scope.REGEXP_PASSWORD = '02';

                // Testa os requisitos do step 1
                window.jQuery.each($scope.PRE_REQUISITES, function(index, value) {
                    
                    if(value == false && $scope.STEP_ERROR.indexOf(1) == -1)
                        $scope.STEP_ERROR.push(1);

                });

                /**
                 * Realiza a instalação do brACP.
                 */
                $scope.install              = function()
                {
                    var _tmp = parseInt('0x' + parseInt($scope.REGEXP_USERNAME) + parseInt($scope.REGEXP_PASSWORD));
                    var _username = _tmp&0x10 ? '[a-zA-Z0-9]{4,32}' :
                                    _tmp&0x20 ? '[a-zA-Z0-9\\s@\\$#%&\\*!]{4,32}' : '.{4,32}';
                    var _password = _tmp&0x01 ? '[a-zA-Z0-9]{4,32}' :
                                    _tmp&0x02 ? '[a-zA-Z0-9\\s@\\$#%&\\*!]{4,32}' : '.{4,32}';

                    var installData = angular.merge(
                        $scope.INSTALL_VARS,
                        {
                            'BRACP_SERVERS'         : $scope.BRACP_SERVERS,
                            'BRACP_REGEXP_FORMAT'   : _tmp,
                            'BRACP_REGEXP_USERNAME' : _username,
                            'BRACP_REGEXP_PASSWORD' : _password,
                        }
                    );

                    // $scope.config.BRACP_REGEXP_FORMAT = parseInt('0x' + (parseInt($scope.BRACP_REGEXP_FORMAT_USER) + parseInt($scope.BRACP_REGEXP_FORMAT_PASS)));
                    // Inicializar proteção da tela para status e tratamento de instalação
                    $scope.INSTALL_STATE = 1;

                   $http.post('install.parse.php', installData, {
                        'Content-Type' : 'application/x-www-form-urlencoded'
                    }).then(function(response) {

                        var data = response.data;

                        if(data.status == 0)
                        {
                            $scope.INSTALL_STATE = 2;
                        }
                        else
                        {
                            $scope.INSTALL_STATE = 0;
                            $scope.INSTALL_MESSAGE = data.message;
                        }

                        // console.info(response);

                    }, function(response) {

                        console.error(response);

                    });
                    
                    /*$http({
                        'method'    : 'post',
                        'url'       : 'install.parse.php',
                        'data'      : installData,
                        'headers'   : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }).then(function(response) {
                        
                        var data = response.data;

                        if(data.status > 0)
                        {
                            alert('Ocorreu um erro durante a instalação:\n\n' + data.local+ '\n\n' + data.message);
                            return;
                        }

                        window.location.reload();

                    }, function(response) {
                        console.error(response);
                    });*/
                }

                $scope.sessionAlgoChange    = function()
                {
                    var SESSION_ALGO = $scope.INSTALL_VARS.BRACP_SESSION_ALGO;
                    var SESSION_KEY_IV = $scope.SESSION_ALGO_KEY_IV[SESSION_ALGO];

                    $scope.INSTALL_VARS.BRACP_SESSION_KEY   =   SESSION_KEY_IV.password;
                    $scope.INSTALL_VARS.BRACP_SESSION_IV    =   SESSION_KEY_IV.iv;
                };

                // Configuração para adicionar um novo sub-servidor.
                $scope.addServer = function()
                {
                    var length = $scope.BRACP_SERVERS.length;

                    $scope.BRACP_SERVERS.push({
                        'name'  : 'brAthena',
                        'sql'   : {
                            'driver'    : 'pdo_mysql',
                            'host'      : '127.0.0.1',
                            'user'      : 'ragnarok',
                            'pass'      : 'ragnaork',
                            'dbname'    : 'ragnarok'
                        },
                        'servers'    : {
                            'login' : { 'address' : '127.0.0.1', 'port' : 6900 },
                            'char'  : { 'address' : '127.0.0.1', 'port' : 6121 },
                            'map'   : { 'address' : '127.0.0.1', 'port' : 5121 }
                        },
                        'default' : (length == 0)
                    });
                }

                // Função para remover um sub-servidor.
                $scope.remServer = function(index)
                {
                    if($scope.BRACP_SERVERS.length <= 1)
                    {
                        alert("Você deve possuir ao menos 1 servidor adicionado.");
                        return;
                    }


                    $scope.BRACP_SERVERS.splice(index, 1);
                }

                $scope.validateStep     = function(step, foward)
                {

                    switch(step)
                    {
                        case 2:
                            if($scope.ACCEPT_TERMS == false && $scope.STEP_ERROR.indexOf(step) == -1 )
                                $scope.STEP_ERROR.push(step)
                            else if($scope.ACCEPT_TERMS && $scope.STEP_ERROR.indexOf(step) >= 0)
                            {
                                var ind = $scope.STEP_ERROR.indexOf(step);
                                $scope.STEP_ERROR.splice(ind, 1); 
                            }

                            break;
                    }

                    if(foward && $scope.STEP_ERROR.indexOf(step) >= 0)
                    {
                        alert('Você não pode continuar. Existem itens que não podem ser validados.')
                        return;
                    }

                    var tmp_step = step + (foward == true ? 1:-1);
                    var success_step = $scope.STEP_SUCCESS.indexOf(tmp_step);

                    if(success_step >= 0)
                        $scope.STEP_SUCCESS.splice(success_step, 1);
                    else if(foward && $scope.STEP_SUCCESS.indexOf(step) == -1)
                        $scope.STEP_SUCCESS.push(step);

                    $scope.STEP = tmp_step;
                    return true;
                };

                // Adiciona o primeiro sub-server.
                $scope.addServer();
                $scope.sessionAlgoChange();
            }]);

        </script>

    </head>
    <body ng-app="brACP" ng-controller="install">

        <div class="message success icon" ng-show="INSTALL_STATE == 2">
            <h1>Instalação realizada com sucesso!</h1>

            <p>A Instalação do brACP foi realizada com sucesso! Lembre-se, salve o conteúdo abaixo para futuras informações.<br>
            Para acessar o brACP, pressione F5 para atualizar a página.</p>
        </div>

        <div class="loading-ajax" ng-show="INSTALL_STATE == 1">
            <div class="loading-bar loading-bar-1"></div>
            <div class="loading-bar loading-bar-2"></div>
            <div class="loading-bar loading-bar-3"></div>
            <div class="loading-bar loading-bar-4"></div>
        </div>

        <div class="install" ng-show="!INSTALL_STATE">

            <div class="title">
                Guia de instalação do brACP - Passo {{STEP}} de 13
            </div>

            <div class="content">

                <div class="steps">
                    <ul>
                        <li><label ng-class="{ 'step-selected' : STEP == 1, 'step-warning' : STEP_WARNING.indexOf(1) >= 0, 'step-error' : STEP_ERROR.indexOf(1) >= 0, 'step-success' : STEP_SUCCESS.indexOf(1) >= 0 }">Pré-requisitos</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 2, 'step-warning' : STEP_WARNING.indexOf(2) >= 0, 'step-error' : STEP_ERROR.indexOf(2) >= 0, 'step-success' : STEP_SUCCESS.indexOf(2) >= 0 }">Licença</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 3, 'step-warning' : STEP_WARNING.indexOf(3) >= 0, 'step-error' : STEP_ERROR.indexOf(3) >= 0, 'step-success' : STEP_SUCCESS.indexOf(3) >= 0 }">Endereços</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 4, 'step-warning' : STEP_WARNING.indexOf(4) >= 0, 'step-error' : STEP_ERROR.indexOf(4) >= 0, 'step-success' : STEP_SUCCESS.indexOf(4) >= 0 }">Expressões Regulares</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 5, 'step-warning' : STEP_WARNING.indexOf(5) >= 0, 'step-error' : STEP_ERROR.indexOf(5) >= 0, 'step-success' : STEP_SUCCESS.indexOf(5) >= 0 }">reCAPTCHA</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 6, 'step-warning' : STEP_WARNING.indexOf(6) >= 0, 'step-error' : STEP_ERROR.indexOf(6) >= 0, 'step-success' : STEP_SUCCESS.indexOf(6) >= 0 }">E-Mail</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 7, 'step-warning' : STEP_WARNING.indexOf(7) >= 0, 'step-error' : STEP_ERROR.indexOf(7) >= 0, 'step-success' : STEP_SUCCESS.indexOf(7) >= 0 }">Contas e Usuários</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 8, 'step-warning' : STEP_WARNING.indexOf(8) >= 0, 'step-error' : STEP_ERROR.indexOf(8) >= 0, 'step-success' : STEP_SUCCESS.indexOf(8) >= 0 }">SQL- brACP</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 9, 'step-warning' : STEP_WARNING.indexOf(9) >= 0, 'step-error' : STEP_ERROR.indexOf(9) >= 0, 'step-success' : STEP_SUCCESS.indexOf(9) >= 0 }">SQL- Database</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 10, 'step-warning' : STEP_WARNING.indexOf(10) >= 0, 'step-error' : STEP_ERROR.indexOf(10) >= 0, 'step-success' : STEP_SUCCESS.indexOf(10) >= 0 }">SQL- Servidores</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 11, 'step-warning' : STEP_WARNING.indexOf(11) >= 0, 'step-error' : STEP_ERROR.indexOf(11) >= 0, 'step-success' : STEP_SUCCESS.indexOf(11) >= 0 }">Cache</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 12, 'step-warning' : STEP_WARNING.indexOf(12) >= 0, 'step-error' : STEP_ERROR.indexOf(12) >= 0, 'step-success' : STEP_SUCCESS.indexOf(12) >= 0 }">Sessões</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 13, 'step-warning' : STEP_WARNING.indexOf(13) >= 0, 'step-error' : STEP_ERROR.indexOf(13) >= 0, 'step-success' : STEP_SUCCESS.indexOf(13) >= 0 }">Outros</label></li>
                    </ul>
                </div>

                    <div ng-if="STEP == 1" class="body">
                        
                        <h1>Pré-Requisitos</h1>

                        <p>
                            Abaixo, segue a lista dos pré-requisitos para execução do brACP,
                            caso algum deles não esteja conforme, será necessário arrumar antes de continuar.
                        </p>

                        <p ng-if="STEP_ERROR.indexOf(1) >= 0" class="message error">
                            Alguns dos prés requisitos não foram cumpridos! Verifique os itens que não estão conformes e atualize a tela.
                        </p>

                        <div class="requisites">
                            <div class="row">
                                <div class="cell">Versão do PHP:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.phpversion ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.phpversion ? 'OK':'Sua versão <?php echo PHP_VERSION; ?>')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Permissão de escrita:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.is_writable ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.is_writable ? 'OK':'Ops! Verifique as permissões de escrita.')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Composer:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.composer ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.composer ? 'OK':'Ops! Verifique a pasta \'vendor/\'')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão cURL:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.curl_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.curl_ext ? 'OK':'Por favor, habilite a extensão "cURL".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão Hash:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.hash_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.hash_ext ? 'OK':'Por favor, habilite a extensão "hash".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão Json:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.json_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.json_ext ? 'OK':'Por favor, habilite a extensão "json".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão Xml:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.xml_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.xml_ext ? 'OK':'Por favor, habilite a extensão "xml".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão LibXml:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.libxml_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.libxml_ext ? 'OK':'Por favor, habilite a extensão "libxml".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão OpenSSL:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.openssl_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.openssl_ext ? 'OK':'Por favor, habilite a extensão "openssl".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão PCRE:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.pcre_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.pcre_ext ? 'OK':'Por favor, habilite a extensão "pcre".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão PDO:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.PDO_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.PDO_ext ? 'OK':'Por favor, habilite a extensão "pdo".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão PDO MySQL:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.pdo_mysql_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.pdo_mysql_ext ? 'OK':'Por favor, habilite a extensão "pdo_mysql".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão Sockets:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.sockets_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.sockets_ext ? 'OK':'Por favor, habilite a extensão "sockets".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão Zip:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.zip_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.zip_ext ? 'OK':'Por favor, habilite a extensão "zip".')}}
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">Extensão SQLite:</div>
                                <div class="cell" ng-class="(PRE_REQUISITES.sqlite_ext ? 'cell-success' : 'cell-error')">
                                    {{(PRE_REQUISITES.sqlite_ext ? 'OK':'Por favor, habilite a extensão "sqlite_ext".')}}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div ng-if="STEP == 2" class="body">
                        
                        <h1>Licença</h1>

                        <div class="message info icon">
                            <h1>Lembre-se</h1>
                            Todas as alterações aplicadas no brACP, são de inteira responsabilidade sua.
                        </div>

                        <pre class="install-license"><?php include "license"; ?></pre>

                        <div ng-if="!$parent.ACCEPT_TERMS" class="message error icon">
                            É necessário aceitar os termos de licença antes de realizar a instalação.
                        </div>

                        <label>
                            <input type="checkbox" ng-model="$parent.ACCEPT_TERMS">
                            Eu declaro que aceito e compreendo os termos acima.
                        </label>

                    </div>

                    <div ng-if="STEP == 3" class="body">
                        
                        <h1>Endereços</h1>

                        <p>Segue abaixo as variáveis padrões de caminho para instalação do brACP.</p>

                        <label data-info="URL de Instalação" data-warning="URL de acesso ao brACP. Caminho digitado no navegador para acessar o brACP.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_URL"/>
                        </label>

                        <label data-info="Pasta de Instalação" data-warning="Pasta onde foi instalado o brACP. Necessário para configurar o .htaccess e também para requisições internas.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_DIR_INSTALL_URL"/>
                        </label>

                        <label data-info="Caminho dos arquivos templates" data-warning="Caminho completo para onde os arquivos templates estão. Necessário para montar as telas do brACP.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_TEMPLATE_DIR"/>
                        </label>

                        <label data-info="Caminho dos arquivos de entidade" data-warning="Caminho completo para onde os arquivos de banco de dados. Necessário para realizar os acessos a banco.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_ENTITY_DIR"/>
                        </label>

                        <input id="BRACP_ALLOW_EXTERNAL_REQUEST_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_EXTERNAL_REQUEST" class="input-checkbox" type="checkbox">
                        <label for="BRACP_ALLOW_EXTERNAL_REQUEST_CHK" class="input-checkbox" data-warning="Habilita requisições externas ao brACP? Por segurança, fica desabilitado!">Habilitar acesso de requisições externas?</label>

                        <label data-info="Fuso Horário" data-warning="Fuso horário para gravar informações corretas de data e hora.">
                            <select ng-model="INSTALL_VARS.BRACP_DEFAULT_TIMEZONE">
                                <?php foreach(timezone_identifiers_list() as $timezone) { ?>
                                    <option value="<?php echo $timezone; ?>"><?php echo $timezone; ?></option>
                                <?php } ?>
                            </select>
                        </label>
 

                   </div>

                    <div ng-if="STEP == 4" class="body">
                        
                        <h1>Expressões Regulares</h1>

                        <label data-info="Nome de usuário" data-warning="Expressão regular para campo de usuário, que tipo de caracteres serão aceitos.">
                            <select ng-model="REGEXP_USERNAME">
                                <option value="10">Somente letras e números.</option>
                                <option value="20">Letras, números, espaços e caracteres especiais ( @ $ # % & * ! )</option>
                                <option value="30">Campo livre</option>
                            </select>
                        </label>

                        <label data-info="Senha de usuário" data-warning="Expressão regular para campo de senha, que tipo de caracteres serão aceitos.">
                            <select ng-model="REGEXP_PASSWORD">
                                <option value="01">Somente letras e números.</option>
                                <option value="02">Letras, números, espaços e caracteres especiais ( @ $ # % & * ! )</option>
                                <option value="03">Campo livre</option>
                            </select>
                        </label>

                        <label data-info="E-mail de usuário" data-warning="Expressão regular para definir os e-mails de usuário.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_REGEXP_EMAIL"/>
                        </label>
                    </div>

                    <div ng-if="STEP == 5" class="body">
                        
                        <h1>reCAPTCHA</h1>

                        <input id="RECAPTCHA_ALLOW" ng-model="INSTALL_VARS.BRACP_RECAPTCHA_ENABLED" class="input-checkbox" type="checkbox">
                        <label for="RECAPTCHA_ALLOW" class="input-checkbox" data-warning="Habilita proteção contra spams e flood de requisições.">Habilita o uso do reCAPTCHA para validar os usuários</label>

                        <p ng-if="INSTALL_VARS.BRACP_RECAPTCHA_ENABLED" class="message info icon">
                            A Validação reCAPTCHA somente irá aparecer após o usuário errar algumas vezes os formulários do sistema.<br>
                            <br>
                            Mais informações: <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Google reCAPTCHA</a>
                        </p>

                        <label ng-if="INSTALL_VARS.BRACP_RECAPTCHA_ENABLED" data-info="Chave Privada" data-warning="Chave privada para o reCAPTCHA, usada para válidação com o servidor.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_RECAPTCHA_PRIVATE_KEY"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_RECAPTCHA_ENABLED" data-info="Chave Pública" data-warning="Chave pública para o reCAPTCHA, usada para gerar o código de validação.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_RECAPTCHA_PUBLIC_KEY"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_RECAPTCHA_ENABLED" data-info="URL para Verificação" data-warning="Endereço para validação dos códigos de retorno.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_RECAPTCHA_PRIVATE_URL"/>
                        </label>
                        
                    </div>

                    <div ng-if="STEP == 6" class="body">
                        
                        <h1>E-Mail</h1>

                        <input id="EMAIL_ALLOW" ng-model="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" class="input-checkbox" type="checkbox">
                        <label for="EMAIL_ALLOW" class="input-checkbox" data-warning="Permite que alguns e-mails sejam enviados apartir do painel de controle.">Habilitar envio de e-mails</label>

                        <p ng-if="!INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" class="message warning icon">
                            Algumas opções de configuração somente serão possíveis caso a configuração de e-mails esteja habilitada.<br>
                            Essas configuração são por exemplo:<br>
                            <strong>Confirmação de Contas, Recuperação de senhas, Notificações de Mudanças (Senha e E-mail)</strong>.
                        </p>

                        <p ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" class="message info icon">
                            Procure com o seu provedor as informações de SMTP para preenchimento dos dados abaixo.
                        </p>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" data-info="Servidor" data-warning="Endereço do servidor SMTP que será utilizado para envio dos e-mails.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_MAIL_HOST"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" data-info="Porta" data-warning="Porta que o serviço SMTP está sendo executado no servidor.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_MAIL_PORT"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" data-info="Usuário" data-warning="Nome de usuário para uso do serviço SMTP.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_MAIL_USER"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" data-info="Senha" data-warning="Senha do usuário para uso do serviço SMTP.">
                            <input type="password" ng-model="INSTALL_VARS.BRACP_MAIL_PASS"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" data-info="Endereço do Remetente" data-warning="Endereço de e-mail do remetente.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_MAIL_FROM"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" data-info="Nome do Remetente" data-warning="Nome de quem está enviando o e-mail.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_MAIL_FROM"/>
                        </label>

                    </div>

                    <div ng-if="STEP == 7" class="body">
                        
                        <h1>Contas e Usuários</h1>

                        <input id="ACC_CREATE_0" ng-model="INSTALL_VARS.BRACP_ALLOW_CREATE_ACCOUNT" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_0" class="input-checkbox" data-warning="Permite que novas contas sejam criadas através do brACP.">Habilitar a criação de novas contas</label>

                        <input id="ACC_CREATE_1" ng-model="INSTALL_VARS.BRACP_MD5_PASSWORD_HASH" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_1" class="input-checkbox" data-warning="Permite o uso de MD5 nas senhas de usuários">Habilitar senhas com MD5</label>

                        <input id="ACC_CREATE_2" ng-model="INSTALL_VARS.BRACP_MAIL_REGISTER_ONCE" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_2" class="input-checkbox" data-warning="Permite que seja utilizado somente uma vez o endereço de e-mail informado.">Bloquear uso duplicado de e-mails.</label>

                        <input id="ACC_CREATE_3" ng-model="INSTALL_VARS.BRACP_ALLOW_CHANGE_MAIL" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_3" class="input-checkbox" data-warning="Permite que o jogador possa alterar seu endereço de e-mail pelo brACP.">Habilitar alteração de e-mail.</label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_CHANGE_MAIL" data-info="Delay para mudança de e-mail" data-warning="Tempo de espera para a próxima mudança de e-mail.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_CHANGE_MAIL_DELAY"/>
                        </label>

                        <p ng-if="!INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" class="message warning icon">
                            Todas as configurações abaixo dependem que o painel de controle esteja configurado para envio de e-mails.
                        </p>

                        <input id="ACC_CREATE_4" ng-model="INSTALL_VARS.BRACP_CONFIRM_ACCOUNT" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_4" class="input-checkbox" data-warning="Permite que o brACP valide novas contas criadas apartir de um código de cadastrado enviado por e-mail.">Habilitar confirmação de contas</label>

                        <input id="ACC_CREATE_5" ng-model="INSTALL_VARS.BRACP_ALLOW_RECOVER" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_5" class="input-checkbox" data-warning="Permite que o brACP habilite a recuperação de contas por e-mail.">Habilitar recuperação de contas</label>

                        <p ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND && INSTALL_VARS.BRACP_MD5_PASSWORD_HASH && INSTALL_VARS.BRACP_ALLOW_RECOVER" class="message info">
                            A <strong>Recuperação por código</strong> será utilizada automaticamente no caso de uso de senhas com MD5.
                        </p>

                        <input id="ACC_CREATE_6" ng-model="INSTALL_VARS.BRACP_RECOVER_BY_CODE" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_6" ng-if="INSTALL_VARS.BRACP_ALLOW_RECOVER" class="input-checkbox" data-warning="Permite uma validação de código de recuperação por e-mail antes de alterar a senha da conta.">Recuperação por código.</label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_RECOVER && INSTALL_VARS.BRACP_RECOVER_BY_CODE" data-info="Tempo (em minutos) para expirar o código de recuperação" data-warning="Validade para o código de ativação.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_RECOVER_CODE_EXPIRE"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND && INSTALL_VARS.BRACP_ALLOW_RECOVER" data-info="Tamanho para a senha gerada" data-warning="Tamanho que a nova senha gerada pelo painel de controle irá ter após recuperada">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_RECOVER_STRING_LENGTH"/>
                        </label>

                        <label ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND && INSTALL_VARS.BRACP_ALLOW_RECOVER" data-info="Caracteres habilitados para nova senha" data-warning="Se você desejar, algum novo caractere para a nova senha gerada, digite abaixo. A Nova senha é gerada aleatoriamente.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_RECOVER_RANDOM_STRING"/>
                        </label>

                        <input id="ACC_CREATE_7" ng-model="INSTALL_VARS.BRACP_NOTIFY_CHANGE_PASSWORD" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_7" ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" class="input-checkbox" data-warning="A Cada alteração de senha do jogador, será enviado um e-mail para a conta dele.">Habilitar notificações de mudança de senha</label>

                        <input id="ACC_CREATE_8" ng-model="INSTALL_VARS.BRACP_NOTIFY_CHANGE_MAIL" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_8" ng-if="INSTALL_VARS.BRACP_ALLOW_MAIL_SEND" class="input-checkbox" data-warning="A cada alteração de e-mail do jogador, será enviado um e-mail tanto para o antigo quanto para o novo.">Habilitar notificações de mudança de e-mail</label>

                        <label data-info="Nível mínimo da conta para logar" data-warning="Valor mínimo no campo 'group_id' para permitir o login do usuário.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_ALLOW_LOGIN_GMLEVEL"/>
                        </label>

                        <label data-info="Nível mínimo da conta para considerar administrador" data-warning="Valor mínimo no campo 'group_id' para o brACP cpnsiderar a conta nível administrador.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_ALLOW_ADMIN_GMLEVEL"/>
                        </label>

                        <input id="ACC_CREATE_9" ng-model="INSTALL_VARS.BRACP_ALLOW_ADMIN_CHANGE_PASSWORD" class="input-checkbox" type="checkbox">
                        <label for="ACC_CREATE_9" class="input-checkbox" data-warning="Permite que usuários nível adminsitrador realizem alteração de senha pelo brACP. (não recomendado)">Habilitar alteração de senha de adminsitradores</label>

                    </div>

                    <div ng-if="STEP == 8" class="body">
                        
                        <h1>SQL - brACP</h1>

                        <p>Segue abaixo as configurações de acesso ao banco de dados do brACP.</p>

                        <p class="message info icon">Essas configurações são apenas para o banco de dados do brACP e não do ragnarok.</p>
                        
                        <label data-info="Drive para conexão" data-warning="Não alterar se não souber o que está fazendo.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_CP_DRIVER"/>
                        </label>
                        
                        <label data-info="Servidor de Banco de Dados" data-warning="Endereço IP ou Dominio do servidor de banco de dados.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_CP_HOST"/>
                        </label>
                        
                        <label data-info="Usuário" data-warning="Nome de usuário para acesso ao banco de dados.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_CP_USER"/>
                        </label>
                        
                        <label data-info="Senha" data-warning="Senha de usuário para acesso ao banco de dados (Padrão: bracp)">
                            <input type="password" ng-model="INSTALL_VARS.BRACP_SQL_CP_PASS"/>
                        </label>
                        
                        <label data-info="Database (Schema)" data-warning="Nome do banco de dados para a conexão.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_CP_DBNAME"/>
                        </label>

                    </div>

                    <div ng-if="STEP == 9" class="body">
                        
                        <h1>SQL - Database</h1>

                        <p>Segue abaixo as configurações de acesso ao banco de dados de monstros e itens.</p>

                        <p class="message info icon">Essas configurações são apenas para o banco de dados de monstros e itens, também utilizadas pelo emulador.</p>

                        <label data-info="Drive para conexão" data-warning="Não alterar se não souber o que está fazendo.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_DB_DRIVER"/>
                        </label>
                        
                        <label data-info="Servidor de Banco de Dados" data-warning="Endereço IP ou Dominio do servidor de banco de dados.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_DB_HOST"/>
                        </label>
                        
                        <label data-info="Usuário" data-warning="Nome de usuário para acesso ao banco de dados.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_DB_USER"/>
                        </label>
                        
                        <label data-info="Senha" data-warning="Senha de usuário para acesso ao banco de dados (Padrão: ragnarok)">
                            <input type="password" ng-model="INSTALL_VARS.BRACP_SQL_DB_PASS"/>
                        </label>
                        
                        <label data-info="Database (Schema)" data-warning="Nome do banco de dados para a conexão.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SQL_DB_DBNAME"/>
                        </label>

                    </div>

                    <div ng-if="STEP == 10" class="body">
                        
                        <h1>SQL - Servidores</h1>
                        
                        <div class="message warning icon">
                            <h1>Suporte a Sub-Servidores</h1>
                            Você poderá ter vários sub-servidores configurados. Clique no botão <strong>Adicionar</strong> para criar um novo registro ou <strong>Remover</strong> para deletar um dos registros.
                        </div>

                        <p>
                            <button class="button info" ng-click="addServer()">Adicionar Servidor</button>
                        </p>

                        <div class="install-server" ng-repeat="server in BRACP_SERVERS track by $index">

                            <button ng-show="BRACP_SERVERS.length > 1" class="button error" ng-click="remServer($index)" >Remover {{($index+1)}} de {{BRACP_SERVERS.length}}</button>

                            <div class="install-server-info">
                                <label data-info="Nome" data-warning="Nome do sub-servidor.">
                                    <input type="text" ng-model="server.name"/>
                                </label>
                                <input id="server_{{$index}}" ng-model="server.default" class="input-checkbox" type="checkbox">
                                <label for="server_{{$index}}" class="input-checkbox" data-warning="Servidor a ser usado como servidor de contas.">Servidor de contas?</label>
                            </div>
                            <div class="install-server-info">
                                <label data-info="Endereço/Domino do servidor de login" data-warning="Endereço IP ou Dominio do servidor de login.">
                                    <input type="text" ng-model="server.servers.login.address"/>
                                </label>
                                <label data-info="Porta do servidor de login" data-warning="Porta do servidor de login.">
                                    <input type="text" ng-model="server.servers.login.port"/>
                                </label>
                            </div>

                            <div class="install-server-info">
                                <label data-info="Endereço/Domino do servidor de personagem" data-warning="Endereço IP ou Dominio do servidor de personagem.">
                                    <input type="text" ng-model="server.servers.char.address"/>
                                </label>
                                <label data-info="Porta do servidor de personagem" data-warning="Porta do servidor de personagem.">
                                    <input type="text" ng-model="server.servers.char.port"/>
                                </label>
                            </div>

                            <div class="install-server-info">
                                <label data-info="Endereço/Domino do servidor de mapas" data-warning="Endereço IP ou Dominio do servidor de mapas.">
                                    <input type="text" ng-model="server.servers.map.address"/>
                                </label>
                                <label data-info="Porta do servidor de mapas" data-warning="Porta do servidor de mapas.">
                                    <input type="text" ng-model="server.servers.map.port"/>
                                </label>
                            </div>

                            <div class="install-server-sql">
                                <label data-info="Drive para conexão" data-warning="Não alterar se não souber o que está fazendo.">
                                    <input type="text" ng-model="server.sql.driver"/>
                                </label>
                                
                                <label data-info="Servidor de Banco de Dados" data-warning="Endereço IP ou Dominio do servidor de banco de dados.">
                                    <input type="text" ng-model="server.sql.host"/>
                                </label>
                                
                                <label data-info="Usuário" data-warning="Nome de usuário para acesso ao banco de dados.">
                                    <input type="text" ng-model="server.sql.user"/>
                                </label>
                                
                                <label data-info="Senha" data-warning="Senha de usuário para acesso ao banco de dados (Padrão: ragnarok)">
                                    <input type="password" ng-model="server.sql.pass"/>
                                </label>
                                
                                <label data-info="Database (Schema)" data-warning="Nome do banco de dados para a conexão.">
                                    <input type="text" ng-model="server.sql.dbname"/>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div ng-if="STEP == 11" class="body">
                        
                        <h1>Cache</h1>

                        <p class="message error icon" ng-show="!MEMCACHE_LOADED">
                            A Biblioteca MEMCACHE não está habilitada.<br>
                            Para utilizar os serviços de cache, verifique as extensões carregadas.
                        </p>

                        <div ng-show="MEMCACHE_LOADED">
                            <p class="message info icon">
                                O Serviço de cache permite que algumas respostas ficam mais rápidas como o caso de classificações e tradução do painel de controle.
                            </p>

                            <input id="BRACP_MEMCACHE_CHK" ng-model="INSTALL_VARS.BRACP_MEMCACHE" class="input-checkbox" type="checkbox">
                            <label for="BRACP_MEMCACHE_CHK" class="input-checkbox" data-warning="Define se o serviço de cache será habilitado para o brACP.">Habilitar cache</label>

                            <label ng-show="INSTALL_VARS.BRACP_MEMCACHE" data-info="Endereço do Servidor" data-warning="Endereço de conexão com o servidor de cache.">
                                <input type="text" ng-model="INSTALL_VARS.BRACP_MEMCACHE_HOST"/>
                            </label>

                            <label ng-show="INSTALL_VARS.BRACP_MEMCACHE" data-info="Porta do Servidor" data-warning="Porta para conexão com o servidor de cache.">
                                <input type="text" ng-model="INSTALL_VARS.BRACP_MEMCACHE_PORT"/>
                            </label>

                            <label ng-show="INSTALL_VARS.BRACP_MEMCACHE" data-info="Tempo (em segundos) de duração" data-warning="Tempo que o cache será mantido antes de ser excluído">
                                <input type="text" ng-model="INSTALL_VARS.BRACP_MEMCACHE_EXPIRE"/>
                            </label>
                        </div>
                    </div>

                    <div ng-if="STEP == 12" class="body">
                        
                        <h1>Sessões</h1>

                        <input id="BRACP_SESSION_SECURE_CHK" ng-model="INSTALL_VARS.BRACP_SESSION_SECURE" class="input-checkbox" type="checkbox">
                        <label for="BRACP_SESSION_SECURE_CHK" class="input-checkbox" data-warning="Define se o brACP irá tratar as sessões de forma segura, com criptografia.">Habilitar sessões seguras</label>
                        
                        <label ng-show="INSTALL_VARS.BRACP_SESSION_SECURE" data-info="Algoritmo de Criptografia" data-warning="Algoritmo de criptografia para as sessões o KEY e IV serão exibidos logo abaixo.">
                            <select ng-model="INSTALL_VARS.BRACP_SESSION_ALGO" ng-change="sessionAlgoChange();">
                                <?php foreach($_CONFIG_CIPHER as $cipher => $data) { ?>
                                    <option data-password="<?php echo $data->password; ?>" data-iv="<?php echo $data->password; ?>" value="<?php echo $cipher; ?>"><?php echo $cipher; ?></option>
                                <?php } ?>
                            </select>
                        </label>

                        <label ng-show="INSTALL_VARS.BRACP_SESSION_SECURE" data-info="Chave de Criptografia" data-warning="Chave de criptografia para o algoritmo selecionado.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SESSION_KEY" readonly/>
                        </label>

                        <label ng-show="INSTALL_VARS.BRACP_SESSION_SECURE" data-info="IV" data-warning="IV para a chave de criptografia selecionada.">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SESSION_IV" readonly/>
                        </label>

                    </div>

                    <div ng-if="STEP == 13" class="body">
                        
                        <h1>Outros</h1>

                        <input id="BRACP_DEVELOP_MODE_CHK" ng-model="INSTALL_VARS.BRACP_DEVELOP_MODE" class="input-checkbox" type="checkbox">
                        <label for="BRACP_DEVELOP_MODE_CHK" class="input-checkbox" data-warning="Define se o brACP irá executar em modo desenvolvimento.">Habilitar modo desenvolvedor</label>

                        <input id="BRACP_MAINTENCE_CHK" ng-model="INSTALL_VARS.BRACP_MAINTENCE" class="input-checkbox" type="checkbox">
                        <label for="BRACP_MAINTENCE_CHK" class="input-checkbox" data-warning="Define se o brACP irá executar em modo manutenção.">Habilitar manutenção</label>

                        <input id="BRACP_ALLOW_ADMIN_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_ADMIN" class="input-checkbox" type="checkbox">
                        <label for="BRACP_ALLOW_ADMIN_CHK" class="input-checkbox" data-warning="Define se irá permitir o brACP operar com o modo administrador (AdminCP).">Habilitar modo administrador</label>

                        <input id="BRACP_ALLOW_CHOOSE_THEME_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_CHOOSE_THEME" class="input-checkbox" type="checkbox">
                        <label for="BRACP_ALLOW_CHOOSE_THEME_CHK" class="input-checkbox" data-warning="Define se usuários poderão trocar de tema.">Habilitar alteração de temas</label>

                        <input id="BRACP_ALLOW_RANKING_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_RANKING" class="input-checkbox" type="checkbox">
                        <label for="BRACP_ALLOW_RANKING_CHK" class="input-checkbox" data-warning="Define se as classificações (rankings) estarão habilitados.">Habilitar classificações</label>

                        <input id="BRACP_ALLOW_RANKING_ZENY_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_RANKING_ZENY" class="input-checkbox" type="checkbox">
                        <label ng-show="INSTALL_VARS.BRACP_ALLOW_RANKING" for="BRACP_ALLOW_RANKING_ZENY_CHK" class="input-checkbox" data-warning="Define se irá habilitar a classificação de zenys.">Habilitar classificação de zeny</label>

                        <input id="BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY" class="input-checkbox" type="checkbox">
                        <label ng-show="INSTALL_VARS.BRACP_ALLOW_RANKING_ZENY" for="BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY_CHK" class="input-checkbox" data-warning="Define se irá habilitar a exibição da quantidade de zenys dos personagens.">Habilitar exibição da quantidade de zenys na classificação de zenys</label>

                        <input id="BRACP_ALLOW_SHOW_CHAR_STATUS_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_SHOW_CHAR_STATUS" class="input-checkbox" type="checkbox">
                        <label for="BRACP_ALLOW_SHOW_CHAR_STATUS_CHK" class="input-checkbox" data-warning="Define se em páginas de classificações será exibido se o personagem está online.">Habilitar exibição de estado do personagem</label>
                        
                        <label data-info="Tema Padrão" data-warning="Tema de visualização principal do brACP.">
                            <select ng-model="INSTALL_VARS.BRACP_DEFAULT_THEME">
                                <?php foreach($themes as $i => $theme) { ?>
                                    <option value="<?php echo $theme->folder; ?>"><?php echo $theme->name; ?> (<?php echo $theme->version; ?>, <?php echo $theme->folder; ?>)</option>
                                <?php } ?>
                            </select>
                        </label>
                        
                        <label data-info="Idioma Padrão" data-warning="Idioma de visualização principal do brACP.">
                            <select ng-model="INSTALL_VARS.BRACP_DEFAULT_LANGUAGE">
                                <?php foreach($langs as $i => $lang) { ?>
                                    <option value="<?php echo $lang; ?>"><?php echo $lang; ?></option>
                                <?php } ?>
                            </select>
                        </label>

                        <label data-info="Intervalo máximo (em segundos) para ping nos servidores" data-warning="Tempo em segundos de espera para poder realizar um próximo nas portas dos servidores (login, char e map)">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_SRV_PING_DELAY" readonly/>
                        </label>

                        <input id="BRACP_ALLOW_VENDING_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_VENDING" class="input-checkbox" type="checkbox">
                        <label for="BRACP_ALLOW_VENDING_CHK" class="input-checkbox" data-warning="Define se irá habilitar a exibição de mercadores online no brACP">Habilitar vendas do servidor online</label>

                        <input id="BRACP_ALLOW_MODS_CHK" ng-model="INSTALL_VARS.BRACP_ALLOW_MODS" class="input-checkbox" type="checkbox">
                        <label for="BRACP_ALLOW_MODS_CHK" class="input-checkbox" data-warning="Define se irá habilitar a aplicação de mods (AdminCP).">Habilitar aplicador de mods</label>
                        
                        <label ng-show="INSTALL_VARS.BRACP_ALLOW_MODS" data-info="Caminho dos arquivos de entidade" data-warning="Caminho completo para os arquivos de modificações">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_MODS_DIR"/>
                        </label>

                        <label data-info="Tempo em dias para alerta de senha não alterada" data-warning="Tempo em dias, do alerta que irá surgir ao usuário se ficar muito tempo sem alterar sua senha. (0: Desabilita)">
                            <input type="text" ng-model="INSTALL_VARS.BRACP_PASS_CHANGE_ALERT"/>
                        </label>

                        <input id="BRACP_LOG_IP_DETAILS_CHK" ng-model="INSTALL_VARS.BRACP_LOG_IP_DETAILS" class="input-checkbox" type="checkbox">
                        <label for="BRACP_LOG_IP_DETAILS_CHK" class="input-checkbox" data-warning="Define se irá habilitar os logs de localização para endereços de ips.">Habilitar logs para ips</label>
                    </div>


            </div>

            <div class="message error icon" ng-show="INSTALL_STATE == 0 && INSTALL_MESSAGE.length > 0">
                <h1>Ocorreu um erro durante a instalação!</h1>

                <p>A Mensagem do erro pode ser verificada abaixo</p>

                {{INSTALL_MESSAGE}}
            </div>

            <div class="footer">
                
                <div class="back">
                    <button ng-if="STEP > 1" ng-click="validateStep(STEP, false)" class="button error">Voltar</button>
                </div>

                <div class="next">
                    <button ng-if="STEP <= 12" ng-click="validateStep(STEP, true)" class="button info">Próximo</button>
                    <button ng-if="STEP == 13" ng-click="install()" class="button success">Instalar</button>
                </div>

            </div>

        </div>

    </body>
</html>