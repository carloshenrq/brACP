(function(ng) {

'use strict';

var ajax = ng.module('angular-app-ajax', []);

// Inicializa as configurações para adição do loading ao fundo.
ajax.config(['$httpProvider', function($httpProvider) {

    $httpProvider.interceptors.push(function($q) {
        return {
            'request' : function(config)
            {
                // Remove o foco do elemento que o usuário está.
                if(document.activeElement)
                    ng.element(document.activeElement).blur();

                var bg = ng.element('<div class="app-http-background"/>').appendTo('body');
                var loading = ng.element('<div class="app-http-loading"/>');
                for(var i = 0; i < 3; i++)
                    ng.element('<div class="app-spinner app-spinner-' + (i+1) + '"/>').appendTo(loading);
                loading.appendTo(bg);

                bg.hide().stop(true, true).fadeIn('fast');

                return config;
            },

            'requestError' : function(rejection)
            {
                var bg = ng.element(document.querySelector('.app-http-loading')).closest('.app-http-background');
                bg.stop(true, true).fadeOut('fast', function() {
                    bg.remove();
                });

                return $q.reject(rejection);
            },

            'response'    : function(response)
            {
                var bg = ng.element(document.querySelector('.app-http-loading')).closest('.app-http-background');
                bg.stop(true, true).fadeOut('fast', function() {
                    bg.remove();
                });
                return response;
            },

            'responseError' : function(response)
            {
                var bg = ng.element(document.querySelector('.app-http-loading')).closest('.app-http-background');
                bg.stop(true, true).fadeOut('fast', function() {
                    bg.remove();
                });
                return $q.reject(response);
            }
        };
    });

}]);

ajax.service('ajax-request', ['$http', '$compile', function($http, $compile) {

    var _obj = this;

    _obj.runRequest = function(config, success, error, $scope)
    {
        if(!error) error = function(request) {
            console.error(request);
        }

        if(!success) success = function(request) {
            console.log(request);
        }

        // Realiza a requisição e retorna os dados.
        $http(config).then(function(response) {

            if(angular.isString(response.data))
            {
                var tpl = angular.element(response.data),
                    comp = $compile(tpl)($scope);
                
                response.data = comp;
            }

            success(response);

        }, error);
    };

    return {
        run : _obj.runRequest
    };
}]);

}) (angular);
