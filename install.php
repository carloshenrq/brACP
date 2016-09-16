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
);

// Teste para as variaveis de configuração do painel de controle.
$_CONFIG_DATA = array(
    // Endereços (STEP=3)
    'BRACP_URL'                         =>  'http' . ((isset($_SERVER['HTTPS'])) ? 's':'') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
    'BRACP_DIR_INSTALL_URL'             =>  $_SERVER['REQUEST_URI'],
    'BRACP_TEMPLATE_DIR'                =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates',
    'BRACP_ENTITY_DIR'                  =>  dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Model',
    'BRACP_DEFAULT_TIMEZONE'            =>  @date_default_timezone_get(),

    // Expressões regulares (STEP=4)
    'BRACP_REGEXP_EMAIL'                => '[a-z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}',

    // Recaptcha
    'BRACP_RECAPTCHA_ENABLED'           => false,
    'BRACP_RECAPTCHA_PRIVATE_KEY'       => '',
    'BRACP_RECAPTCHA_PUBLIC_KEY'        => '',
    'BRACP_RECAPTCHA_PRIVATE_URL'       => 'https://www.google.com/recaptcha/api/siteverify',
);

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

        <link rel="stylesheet" type="text/css" href="themes/classic/css/message.css"/>
        <link rel="stylesheet" type="text/css" href="themes/classic/css/install.css"/>
        <link rel="stylesheet" type="text/css" href="themes/classic/css/button.css"/>

        <script src="js/angular.min.js"></script>
        <script src="js/jquery-2.1.4.min.js"></script>
        <script>
            var install = angular.module('brACP', []);

            install.controller('install', ['$scope', '$http', function($scope, $http) {

                $scope.STEP = 5;
                $scope.STEP_ERROR   = [];
                $scope.STEP_WARNING = [];
                $scope.STEP_SUCCESS = [];

                $scope.PRE_REQUISITES = <?php echo json_encode($PRE_REQUISITES); ?>;
                $scope.ACCEPT_TERMS = false;

                // Variaveis de configuração inicial.
                $scope.INSTALL_VARS = <?php echo json_encode($_CONFIG_DATA); ?>;

                // Configuração para expressão regular de usuários.
                $scope.REGEXP_USERNAME = '10';
                $scope.REGEXP_PASSWORD = '02';

                // Testa os requisitos do step 1
                window.jQuery.each($scope.PRE_REQUISITES, function(index, value) {
                    
                    if(value == false && $scope.STEP_ERROR.indexOf(1) == -1)
                        $scope.STEP_ERROR.push(1);

                });

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

            }]);

        </script>

    </head>
    <body ng-app="brACP" ng-controller="install">

        <div class="install">

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

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 7" class="body">
                        
                        <h1>Contas e Usuários</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 8" class="body">
                        
                        <h1>SQL - brACP</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 9" class="body">
                        
                        <h1>SQL - Database</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 10" class="body">
                        
                        <h1>SQL - Servidores</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 11" class="body">
                        
                        <h1>Cache</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 12" class="body">
                        
                        <h1>Sessões</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 13" class="body">
                        
                        <h1>Outros</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

            </div>

            <div class="footer">
                
                <div class="back">
                    <button ng-if="STEP > 1" ng-click="validateStep(STEP, false)" class="button error">Voltar</button>
                </div>

                <div class="next">
                    <button ng-if="STEP <= 12" ng-click="validateStep(STEP, true)" class="button info">Próximo</button>
                    <button ng-if="STEP == 13" ng-click="validateStep(STEP, true)" class="button success">Instalar</button>
                </div>

            </div>

        </div>

    </body>
</html>