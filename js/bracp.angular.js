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

var brACPApp = angular.module('brACP', []);

/**
 * Controlador para logins de acesso do painel de controle.
 * - Acessos de login padrão deverão ser passados por aqui.
 */
brACPApp.controller('account.login', ['$scope', '$http', function($scope, $http) {
    $scope.userid = '';
    $scope.user_pass = '';

    $scope.stage = 0;
    $scope.loginSuccess = false;
    $scope.loginError = false;

    $scope.submitLogin = function() {

        var urlLogin = document.querySelector('#_BRACP_URL').value + 'account/login';
        var params = $.param({
            'userid' : this.userid,
            'user_pass' : this.user_pass
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlLogin,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {

            $scope.stage = response.data.stage;
            $scope.loginSuccess = response.data.loginSuccess;
            $scope.loginError = response.data.loginError;

            if(response.data.loginSuccess)
                window.location.reload();
        });
    };
}]);

/**
 * Controlador para registro de novas contas.
 * - Deverão passar por aqui os novos registros.
 */
brACPApp.controller('account.register', ['$scope', '$http', function($scope, $http) {

    $scope.userid = '';
    $scope.user_pass = '';
    $scope.user_pass_conf = '';
    $scope.sex = 'M';
    $scope.email = '';
    $scope.email_conf = '';

    $scope.stage = 0;
    $scope.error_state = 0;
    $scope.accept_terms = false;

    $scope.submitRegister = function() {
        var urlRegister = document.querySelector('#_BRACP_URL').value + 'account/register';
    };

}]);
