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

                $scope.STEP = 1;
                $scope.STEP_ERROR   = [];
                $scope.STEP_WARNING = [];


                $scope.validateStep     = function(step, foward)
                {

                    $scope.STEP = step + (foward == true ? 1:-1);
                    return true;
                };

            }]);

        </script>

    </head>
    <body ng-app="brACP" ng-controller="install">

        <div class="install">

            <div class="title">
                Guia de instalação do brACP - Passo {{STEP}} de 12
            </div>

            <div class="content">

                <div class="steps">
                    <ul>
                        <li><label ng-class="{ 'step-selected' : STEP == 1, 'step-warning' : STEP_WARNING.indexOf(1) >= 0, 'step-error' : STEP_ERROR.indexOf(1) >= 0 }">Pré-requisitos</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 2, 'step-warning' : STEP_WARNING.indexOf(2) >= 0, 'step-error' : STEP_ERROR.indexOf(2) >= 0 }">Licença</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 3, 'step-warning' : STEP_WARNING.indexOf(3) >= 0, 'step-error' : STEP_ERROR.indexOf(3) >= 0 }">Endereços</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 4, 'step-warning' : STEP_WARNING.indexOf(4) >= 0, 'step-error' : STEP_ERROR.indexOf(4) >= 0 }">Expressões Regulares</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 5, 'step-warning' : STEP_WARNING.indexOf(5) >= 0, 'step-error' : STEP_ERROR.indexOf(5) >= 0 }">reCAPTCHA</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 6, 'step-warning' : STEP_WARNING.indexOf(6) >= 0, 'step-error' : STEP_ERROR.indexOf(6) >= 0 }">E-Mail</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 7, 'step-warning' : STEP_WARNING.indexOf(7) >= 0, 'step-error' : STEP_ERROR.indexOf(7) >= 0 }">Contas e Usuários</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 8, 'step-warning' : STEP_WARNING.indexOf(8) >= 0, 'step-error' : STEP_ERROR.indexOf(8) >= 0 }">SQL- brACP</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 9, 'step-warning' : STEP_WARNING.indexOf(9) >= 0, 'step-error' : STEP_ERROR.indexOf(9) >= 0 }">SQL- Database</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 10, 'step-warning' : STEP_WARNING.indexOf(10) >= 0, 'step-error' : STEP_ERROR.indexOf(10) >= 0 }">SQL- Servidores</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 11, 'step-warning' : STEP_WARNING.indexOf(11) >= 0, 'step-error' : STEP_ERROR.indexOf(11) >= 0 }">Cache</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 12, 'step-warning' : STEP_WARNING.indexOf(12) >= 0, 'step-error' : STEP_ERROR.indexOf(12) >= 0 }">Sessões</label></li>
                        <li><label ng-class="{ 'step-selected' : STEP == 13, 'step-warning' : STEP_WARNING.indexOf(13) >= 0, 'step-error' : STEP_ERROR.indexOf(13) >= 0 }">Outros</label></li>
                    </ul>
                </div>

                    <div ng-if="STEP == 1" class="body">
                        
                        <h1>Pré-Requisitos</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 2" class="body">
                        
                        <h1>Licença</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 3" class="body">
                        
                        <h1>Endereços</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 4" class="body">
                        
                        <h1>Expressões Regulares</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>

                    <div ng-if="STEP == 5" class="body">
                        
                        <h1>reCAPTCHA</h1>

                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
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
                
                <button ng-if="STEP <= 12" ng-click="validateStep(STEP, true)" class="button info">Próximo {{STEP}} de 12</button>
                <button ng-if="STEP > 1" ng-click="validateStep(STEP, false)" class="button error">Voltar</button>

            </div>

        </div>

    </body>
</html>