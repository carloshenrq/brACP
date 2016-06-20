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
    $scope.success_state = false;
    $scope.accept_terms = false;

    $scope.submitRegister = function() {
        var urlRegister = document.querySelector('#_BRACP_URL').value + 'account/register';
        var params = $.param({
            'userid'            : this.userid,
            'user_pass'         : this.user_pass,
            'user_pass_conf'    : this.user_pass_conf,
            'sex'               : this.sex,
            'email'             : this.email,
            'email_conf'        : this.email_conf
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlRegister,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.stage            = 0;
            $scope.error_state      = response.data.error_state;
            $scope.success_state    = response.data.success_state;
            $scope.accept_terms     = true;
        });
    };
}]);

/**
 * Controlador para código de ativações de novas contas.
 * - Deverão passar por aqui as ativações de conta.
 */
brACPApp.controller('account.register.resend', ['$scope', '$http', function($scope, $http) {

    $scope.userid = '';
    $scope.email = '';

    $scope.stage = 0;
    $scope.has_code = false;
    $scope.error_state = 0;
    $scope.success_state = false;

    $scope.submitResend = function() {
        var urlConfirm = document.querySelector('#_BRACP_URL').value + 'account/confirmation';
        var params = $.param({
            'userid'    : this.userid,
            'email'     : this.email
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlConfirm,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.stage            = 0;
            $scope.error_state      = response.data.error_state;
            $scope.success_state    = response.data.success_state;
        });
    };

    $scope.submitConfirm = function() {
        var urlConfirm = document.querySelector('#_BRACP_URL').value + 'account/confirmation';
        var params = $.param({
            'code'      : this.code
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlConfirm,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.stage            = 0;
            $scope.error_state      = response.data.error_state;
            $scope.success_state    = response.data.success_state;
        });
    }

}]);

/**
 * Controlador para recuperação contas.
 */
brACPApp.controller('account.recover', ['$scope', '$http', function($scope, $http) {
    $scope.userid = '';
    $scope.email = '';

    $scope.stage = 0;
    $scope.has_code = false;
    $scope.error_state = 0;
    $scope.success_state = false;

    $scope.submitRecover = function() {
        var urlConfirm = document.querySelector('#_BRACP_URL').value + 'account/recover';
        var params = $.param({
            'userid'    : this.userid,
            'email'     : this.email
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlConfirm,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.stage            = 0;
            $scope.error_state      = response.data.error_state;
            $scope.success_state    = response.data.success_state;
        });
    };

    $scope.submitRecoverConfirm = function() {
        var urlConfirm = document.querySelector('#_BRACP_URL').value + 'account/recover';
        var params = $.param({
            'code'    : this.code
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlConfirm,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.stage            = 0;
            $scope.error_state      = response.data.error_state;
            $scope.success_state    = response.data.success_state;
        });
    };

}]);

/**
 * Controlador para alteração de senha.
 */
brACPApp.controller('account.password', ['$scope', '$http', function($scope, $http) {

    $scope.user_pass        = '';
    $scope.user_pass_new    = '';
    $scope.user_pass_conf   = '';

    $scope.stage = 0;
    $scope.error_state = 0;
    $scope.success_state = false;

    $scope.passwordInit = function(allowAdminChange, accountLv, adminLevel) {

        if(!allowAdminChange && accountLv >= adminLevel)
            $scope.stage = 2;

    };

    $scope.submitPassword = function() {
        var urlConfirm = document.querySelector('#_BRACP_URL').value + 'account/password';
        var params = $.param({
            'user_pass'         : this.user_pass,
            'user_pass_new'     : this.user_pass_new,
            'user_pass_conf'    : this.user_pass_conf
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlConfirm,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.stage            = 0;
            $scope.error_state      = response.data.error_state;
            $scope.success_state    = response.data.success_state;
        });
    };
}]);

/**
 * Controlador para alteração de email.
 */
brACPApp.controller('account.email', ['$scope', '$http', function($scope, $http) {

    $scope.email        = '';
    $scope.email_new    = '';
    $scope.email_conf   = '';

    $scope.stage = 0;
    $scope.error_state = 0;
    $scope.success_state = false;

    $scope.submitMail = function() {
        var urlConfirm = document.querySelector('#_BRACP_URL').value + 'account/email';
        var params = $.param({
            'email'         : this.email,
            'email_new'     : this.email_new,
            'email_conf'    : this.email_conf
        });

        $scope.stage = 1;

        $http({
            'method'    : 'post',
            'url'       : urlConfirm,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            $scope.stage            = 0;
            $scope.error_state      = response.data.error_state;
            $scope.success_state    = response.data.success_state;
        });
    };
}]);

/**
 * Controlador para alteração de email.
 */
brACPApp.controller('serverStatus', ['$scope', '$http', function($scope, $http) {

    $scope.statusInit   = function(srvSelected, loginServer, charServer, mapServer)
    {
        $scope.BRACP_SRV_SELECTED = srvSelected;

        $scope.BRACP_SRV_LOGIN  = loginServer;
        $scope.BRACP_SRV_CHAR   = charServer;
        $scope.BRACP_SRV_MAP    = mapServer;
    };

    $scope.serverChange = function()
    {
        var urlServer = document.querySelector('#_BRACP_URL').value + 'server';
        var params = $.param({
            'BRACP_SRV_SELECTED'         : $scope.BRACP_SRV_SELECTED.match(/^SRV_([0-9]+)$/)[1]
        });

        $http({
            'method'    : 'post',
            'url'       : urlServer,
            'data'      : params,
            'headers'   : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(function(response) {
            window.location.reload();
        });
    };

}]);
