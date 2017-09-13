(function(ng) {

'use strict';

ng.module('angular-app-fb', [])
    // Cria o serviço para login do facebook.
    .service('fb-login', function() {

        /**
         * Obtém o status de login do facebook.
         *
         * @param function callback Função para receber o retorno.
         */
        var fbLoginStatus = function(callback) 
        {
            FB.getLoginStatus(function(response) {
                callback(response);
            });
        };

        /**
         * Verifica o status do login e depois, se não estiver logado
         * Realiza o login.
         *
         * @param function callback para retorno dos dados.
         */
        var fbLogin = function(callback)
        {
            FB.getLoginStatus(function(response) {
                if(response.status != "connected")
                {
                    FB.login(callback);
                    return;
                }

                callback(response);
            });
        };

        /**
         * Verifica o login de usuário.
         */
        var fbReLogin = function(callback)
        {
            FB.login(callback);
        };

        /**
         * Função para obter os dados de callback para logout de usuário.
         *
         * @param function callback para retorno dos dados.
         */
        var fbLogout = function(callback)
        {
            FB.getLoginStatus(function(response) {
                if(response.status == "connected")
                {
                    FB.logout(callback);
                    return;
                }

                callback(response);
            });
        };

        /**
         * Executa operações pelo facebook API.
         *
         * @param string path
         * @param function callback
         */
        var fbApiExec = function(path, callback)
        {
            FB.api(path, function(response) {
                callback(response);
            });
        };


        /**
         * Retorna informações sobre o usuário logado.
         *
         * @param function callback para retorno dos dados.
         */
        var fbInfo = function(callback)
        {
            fbApiExec('/me?fields=name,email,id,gender,locale,verified,picture', callback);
        };

        /**
         * Obtém dados do api solicitado.
         * 
         * @return window.FB
         */
        var fbApi = function()
        {
            return FB;
        }

        return {
            login       : fbLogin,
            runLogin    : fbReLogin,
            logout      : fbLogout,
            info        : fbInfo,
            exec        : fbApiExec,
            getStatus   : fbLoginStatus,
            getApi      : fbApi
        };
    })
    // Cria uma diretiva para o botão do facebook aparecer na tela.
    .directive('fbApi', ['$timeout', '$parse', function($timeout, $parse) {
        return {
            restrict : 'E',
            template : '<div id="fb-root"></div>',
            scope : { 'login' : '=' },
            replace : true,
            link : function(scope, element, attrs)
            {
                // Verifica se existe o apiId
                if(!('appId' in attrs))
                    return;

                // Código do api da aplicação.
                var appId = attrs.appId;

                // Verifica se já foi encontrado um elemento
                // com as informações do facebook.
                if(ng.element(document.querySelector('#facebook-jssdk')).length > 0)
                    return;

                // Obtém o elemento head do corpo.
                var head = ng.element(document.querySelector('head'));

                // Cria o elemento script para obter os dados de api do facebook.
                ng.element('<script id="facebook-jssdk" src="//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.8&appId='+appId+'"></script>')
                    .prependTo(head)
                    .ready(function() {

                        // Executa quando a biblioteca do facebook for carregada.
                        window.fbAsyncInit = function()
                        {
                            // Inicializa a api para a aplicação atual.
                            FB.init({
                                'appId' : appId,
                                cookie  : true,
                                xfbml   : true,
                                version : 'v2.8'
                            });

                            if(scope.login)
                            {
                                // Obtém o status de resposta atual do facebook.
                                FB.getLoginStatus(function(fbResponse){
                                    scope.login(fbResponse);
                                });
                            }
                        };

                    });

            }
        }
    }]);

}) (angular);
