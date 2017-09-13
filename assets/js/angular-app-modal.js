/**
 * 
 */
(function(ng) {

'use strict';

var modal = ng.module('angular-app-modal', ['angular-app-ajax', 'angular-uri-parser']);

modal.service('modal-window', ['$compile', 'uri-parser', 'ajax-request', function($compile, uriParser, ajax) {
    /**
     * Função para criar um novo objeto para uso
     * do modal.
     * 
     * @return object
     */
    var createObject = function()
    {
        // Retorna uma nova instância de objeto para
        // O Modal informado.
        return new (function() {
            var obj = this;

            /**
             * Todas as informações necessárias para gerar
             * o modalbox em tela.
             */
            var modalData = {
                'id'    : 'MODAL_'+(new Date().getTime()),
                'title' : 'Test',
                'class' : '',
                'content' : {
                    'body' : null,
                    'url'  : null,
                    'data' : []
                },
                'buttons' : []
            };

            /**
             * Define o titulo do box.
             * 
             * @param string title
             */
            this.setTitle   = function(title)
            {
                modalData.title = title;
            };

            /**
             * Define a classe para a modalbox.
             * 
             * @param string className
             */
            this.setClass   = function(className)
            {
                modalData.class = className;
            }

            /**
             * Define o texto do conteúdo html a ser exibido.
             * 
             * @param string htmlText
             */
            this.setContent = function(htmlText)
            {
                modalData.content.body = htmlText;
            };

            /**
             * Define o conteúdo como sendo uma URL e os dados que serão
             * utilizados para a requisição.
             * 
             * @param string url
             * @param array data
             */
            this.setContentUrl  = function(url, data)
            {
                modalData.content.url = url;
                modalData.content.data = data;
            }

            /**
             * Define a função para ser executada após o carregamento completo
             * do corpo.
             */
            this.onBodyLoad     = function(func)
            {
                modalData.content.afterLoad = func;
            };

            /**
             * Define a função para ser executada após fechar a janela.
             */
            this.onBodyClose    = function(func)
            {
                modalData.content.afterClose = func;
            }

            /**
             * Adiciona um botão ao modal.
             */
            this.addButton  = function(text, className, events)
            {
                // Adiciona um botão na tela
                modalData.buttons.push({
                    'text' : text,
                    'className' : className,
                    'events' : events
                });
            };

            /**
             * Esconde a caixa modal atual e a remove da tela.
             * Se necessário chamar o objeto novamente com o método do display.
             */
            this.close   = function()
            {
                var mBody = ng.element(document.querySelector('#' + modalData.id));
                mBody.closest('.app-http-background').stop(true, true).fadeOut('fast', function() {
                    mBody.remove();
                });
            };

            /**
             * Exibe o modal informado na tela e carrega todas as informações necessárias.
             */
            this.display = function($scope)
            {
                var mBg = ng.element('<div class="app-http-background"/>').appendTo('body');
                var mContainer = ng.element('<div id="'+ modalData.id + '" class="modal"/>');
                var mTitle = ng.element('<div class="title"/>').appendTo(mContainer);
                var mBody = ng.element('<div class="body"/>').appendTo(mContainer);
                var mFooter = ng.element('<div class="footer"/>').appendTo(mContainer);

                mBg.hide();
                mTitle.html(modalData.title);

                // Adiciona os botões a tela.
                ng.forEach(modalData.buttons, function(value, index) {
                    var mButton = ng.element('<button class="button"/>').appendTo(mFooter);
                    mButton.html(value.text);
                    if(value.className.length > 0)
                        mButton.addClass(value.className);
                    ng.forEach(value.events, function(callback, evt) {
                        mButton.bind(evt, callback);
                    });
                })

                // Adiciona o class ao modal principal.
                if(modalData.class.length > 0)
                    mContainer.addClass(modalData.class);

                if(modalData.content.body != null)
                    mBody.html('').append($compile(ng.element(modalData.content.body))($scope)[0]);

                if(modalData.content.body == null && modalData.content.url != null)
                {
                    ajax.run({
                        'method' : 'POST',
                        'url'    : modalData.content.url,
                        'data'   : uriParser.parse(modalData.content.data),
                        'headers'  : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }, function(response) {
                        mBody.html('').append(response.data);
                        if(ng.isDefined(modalFooter))
                            modalFooter.show();
                    }, function() {
                        if(ng.isDefined(modalFooter))
                            modalFooter.show();
                    }, $scope);
                }

                mContainer.appendTo(mBg);
                mBg.stop(true, true).fadeIn('fast');
            };
        })();
    };

    return {
        'create' : createObject
    };
}])

}) (angular);
