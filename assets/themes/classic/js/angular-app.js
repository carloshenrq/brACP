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

(function(ng){

'use strict';

// Cria o módulo da aplicação para o bracp
var app = ng.module('bracp', ['angular-storage', 'angular-quill', 'angularjs-datetime-picker',
                                    'vcRecaptcha', 'angular-mask',
                                    'angular-input-file',
                                    'angular-app-ajax', 'angular-app-modal', 'angular-chartjs',
                                    'angular-app-fb', 'angular-window-location']);

app.controller('game-access', ['$scope', '$interval', 'ajax-request', 'window-location', 'uri-parser', 'modal-window',
    function($scope, $interval, ajax, location, uri, modal) {

        $scope.accounts = 0;
        $scope.serverSelected = [];

        $scope.linkData = {
            'userid'    : '',
            'user_pass' : ''
        };
        $scope.createData = {
            'userid'        : '',
            'user_pass'     : '',
            'user_pass_cnf' : '',
            'sex'           : 'M'
        };
        $scope.changePassData = {
            'user_pass'     : '',
            'new_user_pass' : '',
            'cnf_user_pass' : '',
        };

        $scope.init = function(accounts, servers)
        {
            this.accounts = parseInt(accounts);

            // Atribui a lista de servidores por conta
            for(var i = 0; i < servers.length; i++)
                this.serverSelected[i] = servers[i].toString();

        };

        $scope.reset = function()
        {
            $scope.linkData = {
                'userid'    : '',
                'user_pass' : ''
            };
            $scope.createData = {
                'userid'        : '',
                'user_pass'     : '',
                'user_pass_cnf' : '',
                'sex'           : 'M'
            };
            $scope.changePassData = {
                'user_pass'     : '',
                'new_user_pass' : '',
                'cnf_user_pass' : '',
            };
        };

        $scope.changePass = function(account_id)
        {
            $scope.reset();

            // Abre a janela para criar o acesso.
            var win = modal.create();
            win.setTitle('brACP - Alterar Senha');
            win.setContent(
            '<div>' +
                '<p>Para alterar a senha de acesso ao jogo, você deve informar os dados logo abaixo.</p>'+
                '<p style="margin: 2em 4em;">'+
                    '<input type="password" class="input" ng-model="changePassData.user_pass" placeholder="Digite a senha atual deste acesso." required/>'+
                    '<input type="password" class="input" ng-model="changePassData.new_user_pass" placeholder="Digite a nova senha de acesso." required/>'+
                    '<input type="password" class="input" ng-model="changePassData.cnf_user_pass" placeholder="Confirme a nova senha" required/>'+
                '</p>'+
            '</div>');
            win.addButton('Alterar', '', {
                click : function() {

                    var changeData = {
                        'account_id'    : account_id,
                        'user_pass'     : $scope.changePassData.user_pass,
                        'new_user_pass' : $scope.changePassData.new_user_pass,
                        'cnf_user_pass' : $scope.changePassData.cnf_user_pass
                    };

                    $scope.reset();
                    
                    ajax.run({
                        url     : location.main() + '/game/change/password',
                        method  : 'POST',
                        data    : uri.parse(changeData),
                        headers : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }, function(response) {
                        var data = response.data;

                        var winInfo = modal.create();
                        winInfo.setTitle('brACP - Alterar Senha');
                        if('error' in data)
                        {
                            winInfo.setContent('<div class="message error">Não foi possível realizar a alteração da senha. Verifique os dados informados</div>');
                            winInfo.addButton('OK', '', {
                                click : function() {
                                    winInfo.close();
                                }
                            });
                        }
                        else
                        {
                            winInfo.setContent('<div class="message success">Senha alterada com sucesso.</div>');
                            winInfo.addButton('OK', '', {
                                click : function() {
                                    winInfo.close();
                                    win.close();
                                }
                            });
                        }
                        winInfo.display($scope);
                        return;

                    }, function(response) {

                    }, $scope);

                }
            });
            win.addButton('Fechar', 'error', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
            return;
        }

        /**
         * Função para gerenciar dados de personagem da conta informada.
         * 
         * @param account_id
         */
        $scope.charManage = function(account_id, server_id)
        {
            /**
             * @TODO: Fazer a requisição dos personagens
             *        para o servidor selecionado
             */
        }

        /**
         * Cria um novo acesso ao jogo para o perfil do usuário.
         */
        $scope.createAccess = function()
        {
            $scope.reset();
            // Abre a janela para criar o acesso.
            var win = modal.create();
            win.setTitle('brACP - Criar Acesso');
            win.setContent(
            '<div>' +
                '<p>Para criar o acesso ao jogo, você deve inserir as informações abaixo.</p>'+
                '<p style="margin: 2em 4em;">'+
                    '<input type="text" class="input" ng-model="createData.userid" placeholder="Digite um nome de usuário." required/>'+
                    '<input type="password" class="input" ng-model="createData.user_pass" placeholder="Digite a senha do acesso." required/>'+
                    '<input type="password" class="input" ng-model="createData.user_pass_cnf" placeholder="Confirme a senha de acesso" required/>'+
                    '<select class="input" ng-model="createData.sex">'+
                        '<option value="M">M - Masculino</option>'+
                        '<option value="F">F - Feminino</option>'+
                    '</select>'+
                '</p>'+
                '<div class="message warning small"><em>*Esta ação não poderá ser desfeita! Ao vincular o usuário, ele será permanentemente vinculado a este perfil.</em></div>'+
            '</div>');
            win.addButton('Criar', '', {
                click : function() {

                    ajax.run({
                        url     : location.main() + '/game/create',
                        method  : 'POST',
                        data    : uri.parse($scope.createData),
                        headers : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }, function(response) {
                        var data = response.data;
                        
                        var winInfo = modal.create();
                        winInfo.setTitle('brACP - Criar Acesso');
                        if('error' in data)
                        {
                            winInfo.setContent('<div class="message error">' + data.message + '</div>');
                            winInfo.addButton('OK', '', {
                                click : function() {
                                    winInfo.close();
                                }
                            });
                        }
                        else
                        {
                            winInfo.setContent('<div class="message success">Acesso criado com sucesso!</div>');
                            winInfo.addButton('OK', '', {
                                click : function() {
                                    winInfo.close();
                                    win.close();
                                    window.location.href = location.current();
                                }
                            });
                        }
                        winInfo.display($scope);
                        return;

                    });

                    // win.close();
                }
            });
            win.addButton('Fechar', 'error', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
            return;
        };

        /**
         * Vincula o perfil do usuário com uma conta de acesso ao jogo.
         */
        $scope.linkAccess = function()
        {
            var patternMail = document.querySelector('#BRACP_PATTERN_MAIL').value;

            $scope.reset();
            
            // Abre a janela para vincular as informações.
            var win = modal.create();
            win.setTitle('brACP - Vincular Acesso');
            win.setContent(
            '<div>' +
                '<p>Para vincular um acesso ao jogo, é necessário que você informe alguns dados do acesso existente.</p>'+
                '<p style="margin: 2em;">'+
                    '<input type="text" class="input" ng-model="linkData.userid" placeholder="Digite o nome de usuário existente." required/>'+
                    '<input type="password" class="input" ng-model="linkData.user_pass" placeholder="Digite aqui a senha deste acesso." required/>'+
                '</p>'+
                '<div class="message warning small"><em>*Esta ação não poderá ser desfeita! Ao vincular o usuário, ele será permanentemente vinculado a este perfil.</em></div>'+
            '</div>');
            win.addButton('Vincular', '', {
                click : function() {
                    
                    ajax.run({
                        url     : location.main() + '/game/link',
                        method  : 'POST',
                        data    : uri.parse($scope.linkData),
                        headers : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }, function(response) {

                        var data = response.data;

                        var winInfo = modal.create();
                        winInfo.setTitle('brACP - Vincular Acesso');
                        if('error' in data)
                        {
                            winInfo.setContent('<div class="message error">Ocorreu um erro durante o processo de vincular a conta. Verifique se estas informações realmente existem ou estão vinculadas a outro perfil.</div>');
                            winInfo.addButton('OK', '', {
                                click : function() {
                                    winInfo.close();
                                }
                            });
                        }
                        else
                        {
                            winInfo.setContent('<div class="message success">Vinculo realizado com sucesso!</div>');
                            winInfo.addButton('OK', '', {
                                click : function() {
                                    winInfo.close();
                                    win.close();
                                    window.location.href = location.current();
                                }
                            });
                        }
                        winInfo.display($scope);
                        return;
                    });

                }
            });
            win.addButton('Fechar', 'error', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
            return;
        };

    }]);

app.controller('profile-edit', ['$scope', '$interval', 'ajax-request', 'window-location', 'uri-parser', 'modal-window',
    function($scope, $interval, ajax, location, uri, modal) {

        $scope.loggedUser = null;
        $scope.savedUser = null;
        $scope.googleAutenticator = {};

        $scope.init = function(loggedUser)
        {
            this.loggedUser = JSON.parse(atob(loggedUser));
            this.savedUser = JSON.parse(atob(loggedUser));
        }

        $scope.addGACode = function()
        {
            ajax.run({
                url     : location.main() + '/profile/config/google/activate',
                method  : 'POST',
                data    : uri.parse($scope.googleAuthenticator),
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }, function(response) {
                
                var data = response.data;

                if('success' in data)
                {
                    var win = modal.create();
                    win.setTitle('brACP - Google Authenticator');
                    win.setContent('<div class="message success">O <strong>Google Authenticator</strong> foi vinculado a sua conta com sucesso!</div>');
                    win.addButton('OK', '', {
                        click : function() {
                            window.location.href = location.current();
                        }
                    });
                    win.display($scope);
                    return;
                }

                var win = modal.create();
                win.setTitle('brACP - Google Authenticator');
                win.setContent('<div class="message error">Os dados digitados são incorretos! Verifique o código do <strong>Google Authenticator</strong> e tente novamente.</div>');
                win.addButton('OK', '', {
                    click : function() {
                        win.close();
                    }
                });
                win.display($scope);
                return;
            });
        }

        /**
         * Remove o autenticador do google.
         */
        $scope.profileRemoveGoogle = function()
        {
            var win = modal.create();
            win.setTitle('brACP - Google Authenticator');
            win.setContent('<div class="message error">'+
                                'Você tem certeza que deseja remover o <strong>Google Authenticator</strong>?<br>'+
                                'Você poderá adicionar a verificação novamente quando quiser.'+
                            '</div>');
            win.addButton('Sim', 'error', {
                click : function() {
                    win.close();

                    ajax.run({
                        'url'       : location.main() + '/profile/config/google/remove',
                        'method'    : 'POST',
                        'data'      : uri.parse({}),
                        headers : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }, function(response) {

                        var data = response.data;

                        if('error' in data)
                        {
                            var win = modal.create();
                            win.setTitle('brACP - Google Authenticator');
                            win.setContent('<div class="message error">' + data.message + '</div>');
                            win.addButton('OK', '', {
                                click : function() {
                                    win.close();
                                }
                            });
                            win.display($scope);
                            return;
                        }

                        // Recarrega a tela
                        window.location.href = location.current();
                    });

                }
            });
            win.addButton('Não', '', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
            return;
        };

        $scope.profileActivateGoogle = function()
        {
            ajax.run({
                url     : location.main() + '/profile/config/google/activate',
                method  : 'POST',
                data    : uri.parse({}),
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }, function(response) {
                var data = response.data;

                if('error' in data)
                {
                    var win = modal.create();
                    win.setTitle('brACP - Google Authenticator');
                    win.setContent('<div class="message error">' + data.message + '</div>');
                    win.addButton('OK', '', {
                        click : function() {
                            win.close();
                        }
                    });
                    win.display($scope);
                    return;
                }

                $scope.googleAuthenticator = data;

                var win = modal.create();
                win.setTitle('brACP - Google Authenticator');
                win.setContent('<div>'+
                    '<form class="form-google-authenticator" ng-submit="addGACode()">'+
                        '<div class="message info">Por favor, leia o QRCode logo abaixo no aplicativo do <strong>Google Authenticator</strong> ou digite o código de vinculo manual e logo após o código de vinculação que aparecer.</div>'+
                        '<div class="form-input form-qrcode" style="width: 42%;">'+
                            '<img src="{{googleAuthenticator.qrCodeUrl}}" width="200px" height="200px"/>'+
                        '</div>'+
                        '<div class="form-input form-data" style="width: 58%;">'+
                            '<label class="input" data-before="Código para Vínculo Manual">'+
                                '<input type="text" class="input" ng-model="googleAuthenticator.secret" readonly/>'+
                            '</label>'+
                            '<label class="input" data-before="Código do Autenticador">'+
                                '<input type="text" class="input" ng-model="googleAuthenticator.code" pattern="^[A-Za-z0-9]{6}$" maxlength="6" required/>'+
                            '</label>'+
                            '<button class="button fill">'+
                                'Verificar Código'+
                            '</button>'+
                        '</div>'+
                        '<input type="submit" id="_addGACode"/>'+
                    '</form>'+
                '</div>');
                win.addButton('Cancelar', 'error', {
                    click : function() {
                        win.close();
                    }
                });
                win.display($scope);
                return;

            });

        }

        $scope.save = function()
        {
            // O obtém o tamanho do avatar.
            var avatarLength = 0;
            
            try
            {
                avatarLength = this.loggedUser.avatarUrl.match(/^data\:(?:[^\,]+),(.*)$/i)[1].length;
            }
            catch(ex)
            {
                avatarLength = 0;
            }

            // Avatar acima de 200kb não permite que seja salvo.
            if(avatarLength > 204800)
            {
                var win = modal.create();
                win.setTitle('brACP - Editar Perfil');
                win.setContent('<div class="message error">Seu avatar é muito grande para ser salvo. <strong>O Tamanho máximo é de <em>200kb (204800 bytes)</em>.</strong></div>');
                win.addButton('OK', '', {
                    click : function() {
                        win.close();
                    }
                });
                win.display($scope);
                return;
            }

            ajax.run({
                url     : location.main() + '/profile/config',
                method  : 'POST',
                data    : uri.parse(this.loggedUser),
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }, function(response) {
                var data = response.data;

                var win = modal.create();
                win.setTitle('brACP - Editar Perfil');

                if('success' in data)
                {
                    $scope.savedUser = ng.copy($scope.loggedUser);
                    win.setContent('<div class="message success">Dados do perfil alterados com sucesso.</div>');
                }
                else
                {
                    win.setContent('<div class="message error">' + data.message + '</div>');
                }

                win.addButton('OK', '', {
                    click : function() {
                        win.close();
                    }
                });
                win.display($scope);
            });
        }

        $scope.reset = function()
        {
            this.loggedUser = ng.copy(this.savedUser);
        }


    }]);

app.controller('profile-view', ['$scope', '$interval', 'ajax-request', 'window-location', 'uri-parser', 'modal-window',
    function($scope, $interval, ajax, location, uri, modal) {

        $scope.profileId = -1;
        $scope.report = {
            reason : '',
            text : ''
        };

        /**
         * Função para realizar a edição dos dados para o perfil
         * do jogador.
         */
        $scope.profileEdit = function()
        {
            window.location.href = location.main() + '/profile/config';
        }

        /**
         * Função para realizar a denuncia do jogador.
         */
        $scope.profileReport = function()
        {
            var win = modal.create();
            win.setTitle('brACP - Denunciar Perfil');
            win.setContent('<div>'+
                '<div class="message error">'+
                    '<strong>Você tem certeza que deseja denunciar este perfil?</strong><br>'+
                    '<em style="font-size: .7em;">*Devemos lembrar que, caso você seja considerado um usuário que realiza denúncias atoa, você pode perder o direito denunciar.</em>'+
                '</div>'+
                '<p>Após sua denúncia ser realizada, nossa equipe irá avaliar o caso e dar uma resposta sobre a denúncia.</p>'+
            '</div>');
            win.addButton('Sim', 'error', {
                click : function() {
                    win.close();

                    var win2 = modal.create();
                    win2.setTitle('brACP - Denunciar Perfil');
                    win2.setContent('<div>'+
                        '<p>Antes de realizar sua denuncia, é necessário que você selecione para estar fazendo isto.</p>'+
                        '<br>'+
                        '<div class="form">'+
                            '<label class="input" data-before="Motivo da Denuncia">'+
                                '<select class="input" ng-model="report.reason" required>'+
                                    '<option value="">- Selecione</option>'+
                                    '<option value="F">Ofensivo/Agressivo</option>'+
                                    '<option value="S">Conteúdo sexual/Pornografia</option>'+
                                    '<option value="B">Bullyng/Difamação</option>'+
                                    '<option value="O">Outros</option>'+
                                '</select>'+
                            '</label>'+
                            '<label class="input" data-before="Descrição/Relato">'+
                                '<quill-editor extra="{modules : { toolbar : [[\'bold\', \'italic\', \'underline\', \'strike\'], [{ \'color\': [] }], [\'link\', \'image\', \'code-block\', \'blockquote\']] }}" ng-model="report.text"></quill-editor>'+
                            '</label>'+
                        '</div>'+
                    '</div>');
                    win2.addButton('Denunciar', 'error', {
                        click : function() {

                            if(($scope.report.reason || '') == '')
                                return;

                            win2.close();

                            // Envia a requisição ajax para reportar o perfil aberto.
                            ajax.run({
                                url     : location.main() + '/profile/report',
                                method  : 'POST',
                                data    : uri.parse({
                                    'profileId'     : $scope.profileId,
                                    'reasonType'    : $scope.report.reason,
                                    'reasonText'    : $scope.report.text
                                }),
                                headers : {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            }, function(response) {

                                // Obtém os dados de resultado para a denuncia.
                                var data = response.data;

                                // Reseta os dados de denuncia.
                                $scope.report = {
                                    reason : '',
                                    text : ''
                                };

                                var win = modal.create();
                                win.setTitle('brACP - Denunciar Perfil');

                                // Se foi reportado com sucesso, então exibe a mensagem
                                // Junto com informação de delay
                                if(data.success)
                                {
                                    win.setContent('<div class="message success">'+
                                            '<strong>Este perfil foi denunciado com sucesso!</strong><br>'+
                                            '<em font-size=".7em">* Você deverá aguardar pelo menos 2 horas antes de fazer uma nova denuncia a este perfil.</em>'+
                                        '</div>');
                                }
                                else
                                {
                                    win.setContent('<div class="message error">'+
                                            '<strong>Não foi possível denunciar este perfil...</strong><br>'+
                                            '<em font-size=".7em">* Se você já denunciou ele nas últimas 2 horas, não vai conseguir denunciar novamente.</em>'+
                                        '</div>');
                                }

                                win.addButton('Fechar', '', {
                                    click : function() {
                                        win.close();
                                    }
                                });
                                win.display($scope);
                            });

                        }
                    });
                    win2.addButton('Cancelar', '', {
                        click : function() {
                            win2.close();
                        }
                    });
                    win2.display($scope);
                }
            });
            win.addButton('Não', '', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
        };

        $scope.profileReportSend = function()
        {
            alert(true);
            return false;
        };

        /**
         * Função para realizar o bloqueio do perfil para o jogador
         */
        $scope.profileUnblock = function()
        {
            var win = modal.create();
            win.setTitle('brACP - Desbloquear Perfil');
            win.setContent('<p>Você tem certeza que deseja desbloquear este perfil?<br>'+
                            'Vocês poderam enviar mensagens e visualizar o perfil um do outro.</p>');
            win.addButton('Sim', '', {
                click : function() {
                    
                    win.close();

                    // Envia a requisição ajax para bloquear o perfil aberto.
                    ajax.run({
                        url     : location.main() + '/profile/unblock',
                        method  : 'POST',
                        data    : uri.parse({
                            'profileId' : $scope.profileId
                        }),
                        headers : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }, function(response) {
                        var data = response.data;

                        if(data.success == false)
                        {
                            var win = modal.create();
                            win.setTitle('brACP - Bloquear Perfil [Erro]');
                            win.setContent('<p>Ocorreu um probleminha ao desbloquear este perfil...</p>');
                            win.addButton('Fechar', '', {
                                click : function() {
                                    win.close();
                                }
                            });
                            win.display($scope);
                            return;
                        }

                        // Recarrega a tela para mostrar que o perfil já foi
                        // desbloqueado.
                        window.location.reload(true);
                    });

                }
            });
            win.addButton('Não', '', {
                click : function() {
                    win.close();
                }
            });

            win.display($scope);
        };

        /**
         * Função para realizar o bloqueio do perfil para o jogador
         */
        $scope.profileBlock = function()
        {
            var win = modal.create();
            win.setTitle('brACP - Bloquear Perfil');
            win.setContent('<p>Você tem certeza que deseja bloquear este perfil?<br>'+
                            'Vocês não poderam mais enviar mensagens e visualizar o perfil um do outro.</p>');
            win.addButton('Não', '', {
                click : function() {
                    win.close();
                }
            });
            win.addButton('Sim', 'error', {
                click : function() {
                    
                    win.close();

                    // Envia a requisição ajax para bloquear o perfil aberto.
                    ajax.run({
                        url     : location.main() + '/profile/block',
                        method  : 'POST',
                        data    : uri.parse({
                            'profileId' : $scope.profileId
                        }),
                        headers : {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    }, function(response) {
                        var data = response.data;

                        if(data.success == false)
                        {
                            var win = modal.create();
                            win.setTitle('brACP - Bloquear Perfil [Erro]');
                            win.setContent('<p>Ocorreu um probleminha ao bloquear este perfil...</p>');
                            win.addButton('Fechar', '', {
                                click : function() {
                                    win.close();
                                }
                            });
                            win.display($scope);
                            return;
                        }
                        
                        // Recarrega a tela para mostrar que o perfil já foi
                        // Bloqueado.
                        window.location.reload(true);
                    });

                }
            });
            win.display($scope);
        };

    }]);


app.controller('top-user', ['$compile', '$scope', '$interval', 'fb-login', 'ajax-request', 'window-location', 'uri-parser', 'modal-window',
    'vcRecaptchaService',
    function($compile, $scope, $interval, fbApi, ajax, location, uri, modal, recaptcha) {

    $scope.box = 0;

    $scope.userInfo = {
        id: -1,
        blocked: -1,
        verified: -1,
        code : ''
    };

    /**
     * Função de inicialização do menu de topo.
     * 
     * @param id Id de usuário.
     * @param blocked Informa se o usuário está bloqueado.
     * @param verified Informa se o usuário está verificado.
     */
    $scope.init = function(id, blocked, verified)
    {
        this.userInfo.id = id;
        this.userInfo.blocked = blocked;
        this.userInfo.verified = verified;
    };

    $scope.checkBox = function(box)
    {
        this.box = (box == this.box ? 0 : box);
    }

    /**
     * Informações para mudança de senha.
     */
    $scope._changePass = {
        'old' : '',
        'new' : '',
        'cnf' : ''
    };

    /**
     * Método chamado quando o usuário deseja fazer a alteração de senha
     * de sua conta.
     */
    $scope.profileChangePass = function()
    {
        var patternPassword = document.querySelector('#BRACP_PATTERN_PASS').value;

        this._changePass = {
            'old' : '',
            'new' : '',
            'cnf' : ''
        };

        var win = modal.create();
        win.setTitle('brACP - Alterar Senha');
        win.setContent('<div>' +
            '<p>Para realizar a alteração de sua senha, é necessário que você nos informe os dados abaixo.</p>'+
            '<p style="margin-top: 1em; margin-bottom: 1em;">'+
                '<input type="password" class="input" ng-model="_changePass.old" placeholder="Digite aqui, sua senha atual." required pattern="' + patternPassword + '"/>'+
                '<input type="password" class="input" ng-model="_changePass.new" placeholder="Digite aqui, sua nova senha." required pattern="' + patternPassword + '"/>'+
                '<input type="password" class="input" ng-model="_changePass.cnf" placeholder="Confirme a nova senha" required pattern="' + patternPassword + '"/>'+
            '</p>'+
            '<p>Esta senha, é de seu perfil, e não dos acessos ao jogo.</p>'+
        '</div>');
        win.addButton('Alterar', '', {
            click : function() {

                win.close();

                // Envia a requisição ajax para verificação da conta.
                ajax.run({
                    url     : location.main() + '/profile/password',
                    method  : 'POST',
                    data    : uri.parse($scope._changePass),
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {
                    // Obtém os dados de retorno para verificação.
                    var data = response.data;

                    // Verifica dados de retorno.
                    if('error' in data)
                    {
                        var win = modal.create();
                        win.setTitle('brACP - Alterar Senha');
                        win.setContent('<div class="message error">' + data.errorMessage + '</div>');
                        win.addButton('Fechar', 'error', {
                            click : function() {
                                win.close();
                            }
                        });
                        win.display($scope);
                        return;
                    }

                    // Recarrega a tela.
                    var win = modal.create();
                    win.setTitle('brACP - Alterar Senha');
                    win.setContent('<div class="message success">Sua senha foi alterada com sucesso.</div>');
                    win.addButton('Fechar', 'error', {
                        click : function() {
                            win.close();
                        }
                    });
                    win.display($scope);
                    return;
                });
            }
        });
        win.addButton('Fechar', 'error', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);
        return;
    };

    /**
     * Entra nos registros de atividade para a conta logada.
     */
    $scope.profileLogs  = function()
    {
        window.location.href = location.main() + '/profile/logs';
    };

    /**
     * Executa ao clicar no botão do perfil.
     */
    $scope.profile = function()
    {
        // Informações para caso o perfil do usuário esteja bloqueado.
        if(this.userInfo.blocked)
        {
            var win = modal.create();
            win.setTitle('brACP - Perfil Bloqueado');
            win.setContent('<div class="message error">Seu perfil se encontra bloqueado para qualquer ação.</div>');
            win.addButton('Fechar', '', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
            return;
        }
        else
        {
            this.checkBox(1);
        }
    };

    /**
     * Abre o menu administrativo para o usuário
     */
    $scope.admin = function()
    {
        this.checkBox(2);
    };

    /**
     * Transfere o jogador para as interações de acesso ao jogo.
     */
    $scope.profileShowGameAccess = function()
    {
        window.location.href = location.main() + '/game';
    };

    /**
     * Comando para exibir os dados de perfil do usuário.
     */
    $scope.profileShowMe   = function() 
    {
        window.location.href = location.main() + '/profile/me';
    };

    /**
     * Comando para exibir informações de configuração do perfil.
     */
    $scope.profileShowConfig   = function() 
    {
        window.location.href = location.main() + '/profile/config';
    };

    /**
     * Comando para exibir os logs de perfil para o usuário.
     */
    $scope.profileShowLog   = function() 
    {
        window.location.href = location.main() + '/profile/logs';
    };

    /**
     * Envia ao usuário o último de validação.
     */ 
    $scope.profileResendCode = function()
    {
        ajax.run({
            url     : location.main() + '/profile/verify/resend',
            method  : 'POST',
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }, function(response) {

            var data = response.data;

            var win = modal.create();
            win.setTitle('brACP - Código de Confirmação [Reenviar]');
            
            if(data.success)
            {
                win.setContent('<div class="message success">'+
                                    'O Código de confirmação do seu perfil foi reenviado com sucesso.'+
                                '</div>');
            }
            else
            {
                win.setContent('<div class="message error">'+
                    'Nenhum código de confirmação encontrado para validação de perfil.'+
                '</div>');
            }

            win.addButton('Fechar', '', {
                click : function() {
                    win.close();
                }
            });

            win.display($scope);

        });
    }

    /**
     * Código de confirmação do perfil.
     */
    $scope.profileConfirmCode = function()
    {
        var win = modal.create();
        win.setTitle('brACP - Código de Confirmação');
        win.setContent('<div>'+
                            '<p>Para finalizar a confirmação de seus dados do perfil, por favor, insira o código de confirmação no campo abaixo.<br>'+
                            'Após confirmado com sucesso, seu perfil será liberado.</p>'+
                            '<div class="message error" style="margin-top: 1em; margin-bottom: 1em;" ng-show="userInfo.code.length != 32">O Código de verificação deve possuir 32 caracteres</div>'+
                            '<p style="margin-top: 1em; margin-bottom: 1em;"><input type="text" class="input" ng-model="userInfo.code" placeholder="Código de verificação" maxlength="32"/></p>'+
                            '<p>Caso ainda não tenha recebido seu código de confirmação**, ou o mesmo tenha expirado, clique no botão <label class="label info">Reenviar</label> logo abaixo.</p>'+
                        '</div>');
        win.addButton('Confirmar', '', {
            click : function() {

                // Impede de enviar os dados com código inferior a 32 caracteres.
                if($scope.userInfo.code.length != 32)
                    return;

                win.close();

                // Envia a requisição ajax para verificação da conta.
                ajax.run({
                    url     : location.main() + '/profile/verify',
                    method  : 'POST',
                    data    : uri.parse({
                        'code' : $scope.userInfo.code
                    }),
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {
                    // Obtém os dados de retorno para verificação.
                    var data = response.data;

                    // Verifica dados de retorno.
                    if('error' in data)
                    {
                        var win = modal.create();
                        win.setTitle('brACP - Código de Confirmação');
                        win.setContent('<div class="message error">' + data.errorMessage + '</div>');
                        win.addButton('Fechar', 'error', {
                            click : function() {
                                win.close();
                                $scope.$apply(function() {
                                    $scope.userInfo.pw = '';
                                });
                            }
                        });
                        win.display($scope);
                        return;
                    }

                    // Recarrega a tela.
                    window.location.href = location.main();
                });
            }
        });
        win.addButton('Reenviar', 'info', {
            click : function() {
                win.close();

                $scope.profileResendCode();
            }
        });
        win.addButton('Fechar', 'error', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);
        return;
    }

    /**
     * Método para realizar logout no brACP.
     * -> Confirma o logout do usuário. Se sim, finaliza a sessão mas não remove os
     *    dados de sessão do facebook. (Ele poderá logar novamente se desejar.)
     */
    $scope.logout = function()
    {
        var win = modal.create();
        win.setTitle('brACP - Encerrar sessão');
        win.setContent('<p>Você tem certeza que deseja encerrar?</p>');
        win.addButton('Sim', '', {
            click : function() {
                win.close();

                // Envia Request para logout de usuário.
                ajax.run({
                    url     : location.main() + '/profile/logout',
                    method  : 'POST',
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {
                    window.location.href = location.main();
                });

            }
        });
        win.addButton('Não', 'error', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);
    };


}]);

app.controller('top-no-user', ['$compile', '$scope', '$interval', 'fb-login', 'ajax-request', 'window-location', 'uri-parser', 'modal-window', function($compile, $scope, $interval, fbApi, ajax, location, uri, modal) {

    // Dados de informações de login de usuário.
    $scope.user = {
        id : '',
        pw : '',
        recaptcha_response : ''
    };

    $scope.gaAuth = {
        gaCode : ''
    };

    // Dados para informações de registro do usuário.
    $scope.register = {
        name : '',
        email : '',
        password : '',
        gender : '',
        birthDate : '',
        recaptcha_response : ''
    };

    $scope.fbLogin = {
        loggedIn : false,
        name : ''
    };

    $scope.box = 0;

    /**
     * Função de inicialização para testar o acesso e login do facebook.
     */
    $scope.init = function()
    {
        var fbLoaded = $interval(function() {
            if(window.FB)
            {
                // Cancela o timer.
                $interval.cancel(fbLoaded);

                // Verifica se o usuário em uso está logado, se estiver obtem os status dele para
                // Alterar informações do botão de login.
                fbApi.getStatus(function(obj) {
                    // Caso esteja logado com o facebook, dai informa dados de login
                    // Para o usuário atual.
                    if(obj.status == "connected")
                    {
                        // Obtém os dados de login e altera a descrições dos botões para 
                        // Adicionar o nome o do usuário.
                        fbApi.info(function(infoObj) {
                            $scope.$apply(function() {
                                $scope.fbLogin = {
                                    loggedIn : true,
                                    name : infoObj.name
                                };
                            });
                        });
                    }

                });
            }
        }, 500);
    };

    /**
     * Ao clicar, muda o valor de box.
     */
    $scope.changeBox = function(box)
    {
        if(this.box == box) this.box = 0;
        else this.box = box;
    }

    /**
     * Faz a verificação do código de autenticação do google.
     */
    $scope.verifyGACode = function()
    {
        // Envia a requisição ajax ao servidor
        // Para verificar os credênciais de acesso no banco de dados.
        ajax.run({
            url     : location.main() + '/profile/login',
            method  : 'POST',
            data    : uri.parse({
                'gaCode'    : $scope.gaAuth.gaCode
            }),
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }, function(response) {

            var data = response.data;

            if('error' in data)
            {
                var win = modal.create();
                win.setTitle('brACP - Google Autenticator');
                win.setContent('<div class="message error">Código digitado é incorreto! Verifique e tente novamente.</div>');
                win.addButton('Fechar', 'error', {
                    click : function() {
                        win.close();
                        $scope.$apply(function() {
                            $scope.gaAuth.gaCode = '';
                        });
                    }
                });
                win.display($scope);
                return;
            }

            window.location.href = location.main();
            return;
        });
    }

    /**
     * Realiza um login com os dados de usuário e senha digitados acima.
     * Caso consiga logar normalmente, a página será recarregada e os dados atualizados.
     */
    $scope.login = function()
    {
        // Envia a requisição ajax ao servidor
        // Para verificar os credênciais de acesso no banco de dados.
        ajax.run({
            url     : location.main() + '/profile/login',
            method  : 'POST',
            data    : uri.parse(this.user),
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }, function(response) {

            var data = response.data;

            // Verifica se ocorreu erro no retorno do login de usuário
            // Se houve erro durante o login, informa a mensagem de erro.
            if('error' in data)
            {
                var win = modal.create();
                win.setTitle('brACP - Entrar');
                win.setContent('<div class="message error">Nome de usuário e/ou senha incorretos! Verifique e tente novamente!</div>');
                win.addButton('Fechar', 'error', {
                    click : function() {
                        win.close();
                        $scope.$apply(function() {
                            $scope.user.pw = '';
                        });
                    }
                });
                win.display($scope);
                return;
            }

            // Verifica se o login foi dado para autenticação em 2 fatores pelo google
            // Caso tenha sido autenticação em 2 fatores então, mostra na tela as informações
            // Necessárias para realizar o login.
            if(data.success == false && data.gaInUse == true)
            {
                // Define os dados de autenticação do GoogleAutenticator.
                $scope.gaAuth.gaCode = '';

                var win = modal.create();
                win.setTitle('brACP - Google Autenticator');
                win.setContent('<div>'+
                            '<form class="form-google-authenticator" ng-submit="verifyGACode()">'+
                                '<div class="message info">Por favor, digite o código que está sendo exibido em seu Google Autenticator.</div>'+
                                '<div class="form-input form-qrcode">'+
                                    '<img src="' + location.main() + '/asset/img/blocked.png" width="128px" height="128px"/>'+
                                '</div>'+
                                '<div class="form-input form-data">'+
                                    '<label class="input" data-before="Código do Autenticador">'+
                                        '<input type="text" class="input" ng-model="gaAuth.gaCode" pattern="^[A-Za-z0-9]{6}$" maxlength="6" required/>'+
                                    '</label>'+
                                    '<button class="button fill">'+
                                        'Verificar Código'+
                                    '</button>'+
                                '</div>'+
                                '<input type="submit" id="_verifyGACode"/>'+
                            '</form>'+
                        '</div>');

                win.addButton('Cancelar', 'error', {
                    click : function() {
                        win.close();
                    }
                });
                win.display($scope);
                return;
            }



            // Recarrega a página para aceitar os dados de login.
            window.location.href = location.main();
        });

        return false;
    };

    /**
     * Método para criar uma conta utilizando as vias normais.
     */
    $scope.create = function()
    {

        ajax.run({
            url     : location.main() + '/profile/create',
            method  : 'POST',
            data    : uri.parse(this.register),
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }, function(response) {
            // Verifica os dados de facebook.
            var data = response.data;

            if('error' in data)
            {
                var win = modal.create();
                win.setTitle('brACP - Cadastrar');
                win.setContent('<div class="message error">'+
                    data.errorMessage +
                '</div>');
                win.addButton('Fechar', 'error', {
                    click : function() {
                        win.close();
                    }
                });
                win.display($scope);
                return;
            }

            window.location.href = location.main();
        });

        return false;
    };

    /**
     * Realiza a criação de contas com o uso do facebook.
     */
    $scope.createWithFb = function()
    {
        var win = modal.create();
        win.setTitle('brACP - Cadastrar com o Facebook');
        win.setContent('<div>'+
                '<div class="message warning">'+
                    'Ao criar sua conta com o facebook, você não poderá remover o vinculo a menos que informe um e-mail e senha válidos.'+
                '</div>'+
                '<p>Você tem certeza que deseja criar sua conta usando o facebook?</p>'+
            '</div>');
        win.addButton('Sim', '', {
            click : function() {

                win.close();

                fbApi.login(function(obj) {
                    // Verifica se está logado corretamente.
                    if(obj.status == "connected")
                    {
                        // Obtém os dados de login e altera a descrições dos botões para 
                        // Adicionar o nome o do usuário.
                        fbApi.info(function(infoObj) {
                            $scope.$apply(function() {
                                $scope.fbLogin = {
                                    loggedIn : true,
                                    name : infoObj.name
                                };
                            });
                        });

                        // Envia uma requisição ajax solicitando o cadastro com o facebook.
                        ajax.run({
                            url     : location.main() + '/profile/create',
                            method  : 'POST',
                            data    : uri.parse({
                                accessToken : obj.authResponse.accessToken
                            }),
                            headers : {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        }, function(response){

                            // Verifica os dados de facebook.
                            var data = response.data;

                            if('error' in data)
                            {
                                var win = modal.create();
                                win.setTitle('brACP - Cadastrar com o Facebook');
                                win.setContent('<div class="message error">'+
                                    data.errorMessage +
                                '</div>');
                                win.addButton('Entrar!', '', {
                                    click : function() {
                                        win.close();

                                        $scope.loginWithFb();
                                    }
                                });
                                win.addButton('Fechar', 'error', {
                                    click : function() {
                                        win.close();
                                    }
                                });
                                win.display($scope);
                                return;
                            }

                            window.location.href = location.main();
                        });
                    }
                });

            }
        });
        win.addButton('Não', 'error', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);
    };

    /**
     * Realiza uma tentativa de login com o facebook para o usuário informado.
     * Caso consiga logar normalmente, a página será recarregada e os dados atualizados.
     */
    $scope.loginWithFb = function()
    {
        fbApi.login(function(obj) {

            // Obtém os dados de login e altera a descrições dos botões para 
            // Adicionar o nome o do usuário.
            fbApi.info(function(infoObj) {
                $scope.$apply(function() {
                    $scope.fbLogin = {
                        loggedIn : true,
                        name : infoObj.name
                    };
                });
            });

            ajax.run({
                url     : location.main() + '/profile/login',
                method  : 'POST',
                data    : uri.parse({
                    'accessToken' : obj.authResponse.accessToken
                }),
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }, function(response) {
                // Obtém os dados de resposta para a verificação de accessToken
                // Obtidos do facebook.
                var data = response.data;

                // Parâmetro apenas retorna quando não existe
                // Dados para o facebook solicitado.
                if('error' in data)
                {
                    var win = modal.create();
                    win.setTitle('brACP - Entrar com o Facebook');
                    win.setContent('<div class="message error">O Facebook que você forneceu, não pertence a nenhuma conta cadastrada.</div>');
                    win.addButton('Cadastre-se!', '', {
                        click : function() {
                            win.close();
                        }
                    });
                    win.addButton('Fechar', 'error', {
                        click : function() {
                            win.close();
                        }
                    });
                    win.display($scope);
                    return;
                }

                // Recarrega a página para aceitar os dados de login.
                window.location.href = location.main();
            });
        });
    };

    /**
     * Realiza o logout do facebook.
     */
    $scope.logoutFromFb = function()
    {
        fbApi.logout(function(obj) {
            $scope.$apply(function() {
                $scope.fbLogin = {
                    loggedIn : false,
                    name : ''
                };
            });
        });
    };

}]);

// Dados de avisos e anuncios
app.controller('announces', ['$scope', '$interval', 'ajax-request', 'window-location', 'uri-parser', 'modal-window',
function($scope, $interval, ajax, location, uri, modal) {

    $scope.init = function(userAnnounces)
    {
        console.info('@TODO: Anuncios de usuários.');
    }

}]);

// Método para executar quando carregar for iniciar o módulo do 
// bracp
app.run(function() {


});


}) (angular);
