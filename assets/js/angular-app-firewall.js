
(function(ng) {

var fwd = ng.module('app-firewall', ['app', 'angular-storage', 'angular-quill', 'datetime',
                                        'vcRecaptcha', 'angular-mask', 'angular-uri-parser',
                                        'angular-app-ajax', 'angular-app-modal', 'angular-window-location',
                                        'angular-chartjs']);

fwd.controller('config', ['$scope', 'modal-window', 'ajax-request', 'window-location', 'uri-parser', function($scope, modal, ajax, location, uri) {

    /**
     * Método para enviar uma requisição e solicitar a limpeza de uma tabela do banco de dados.
     */
    $scope.cleanTable = function(tableName)
    {
        var win = modal.create();
        win.setTitle('Firewall - Limpar Tabela');
        win.setClass('warning');
        win.setContent('<div>'+
            '<div class="message error">Após efetuar a limpeza, nenhum dado poderá ser recuperado.</div>'+
            'Você tem certeza que deseja limpar o conteúdo da tabela <strong><em>' + tableName + '</em></strong>?'+
        '</div>');
        win.addButton('Sim', 'error', {
            click: function() {

                win.close();

                ajax.run({
                    url     : location.current() + '/clean',
                    method  : 'POST',
                    data    : uri.parse({
                        'table' : tableName
                    }),
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {

                    if('error' in response.data)
                    {
                        var win = modal.create();
                        win.setClass('error');
                        win.setTitle('Firewall - Limpar Tabela');
                        win.setContent('<div>Ocorreu um erro durante a tentativa de limpar a tabela.<br>É possível que esta tabela não possa ser limpa.</div>');
                        win.addButton('Fechar', 'error', {
                            click : function() {
                                win.close();
                            }
                        });
                        win.display($scope);
                        return;
                    }

                    window.location.reload();
                })

            }
        });
        win.addButton('Não', 'success', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);
    }

}]);

fwd.controller('users', ['$scope', 'modal-window', 'ajax-request', 'window-location', 'uri-parser', function($scope, modal, ajax, location, uri) {

    $scope.list = [];
    $scope.loggedUserID = -1;
    $scope.form = {
        id      : -1,
        user    : '',
        pass    : ''
    };

    $scope.init = function(users, loggedUserID)
    {
        this.list = JSON.parse(atob(users));
        this.loggedUserID = loggedUserID;
    };

    /**
     * Abre os dados de usuário para edição.
     * 
     * @param object user Usuário que será editado.
     */
    $scope.edit = function(user)
    {
        this.form.id = user.UserID;
        this.form.user = user.User;
        this.form.pass = '';
    };

    /**
     * Habilita/desabilita um usuário 
     * 
     * @param object user
     * @param boolean status Se verdadeiro, habilita... se não desabilita.
     */
    $scope.enableDisable = function(user, status)
    {
        var win = modal.create();
        win.setClass('warning');
        win.setTitle('Firewall - Habilitar/Desabilitar usuário');
        win.setContent('<div>Você tem certeza que deseja '+(status ? 'habilitar':'desabilitar')+' este usuário?</div>');
        win.addButton('Sim', 'success', {
            click : function() {
                win.close();

                // Executa o ajax para mudança de status.
                ajax.run({
                    url     : location.current() + '/change',
                    method  : 'POST',
                    data    : uri.parse({
                        'UserID' : user.UserID,
                        'LoginEnabled' : status
                    }),
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {
                    if('error' in response.data)
                    {
                        var win = modal.create();
                        win.setClass('error');
                        win.setTitle('Firewall - Habilitar/Desabilitar usuário');
                        win.setContent('<div>Ocorreu um erro durante a tentativa de salvar os dados.</div>');
                        win.addButton('Fechar', 'error', {
                            click : function() {
                                win.close();
                            }
                        });
                        win.display($scope);
                        return;
                    }
                    window.location.reload();
                });
            }
        });
        win.addButton('Não', 'error', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);
    }

    /**
     * Adiciona/edita um usuário.
     */
    $scope.add = function()
    {
        var win = modal.create();
        win.setClass('warning');
        win.setTitle('Firewall - Salvar usuário');
        win.setContent('<div>Você tem certeza que deseja <strong>' + (this.form.id != -1 ? 'editar':'incluir') +'</strong> este usuário?</div>');
        win.addButton('Sim', 'success', {
            click: function() {
                win.close();

                // Executa o ajax para edição.
                ajax.run({
                    url     : location.current() + '/add',
                    method  : 'POST',
                    data    : uri.parse($scope.form),
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {

                    // Se ocorreu erro durante a escrita dos dados no banco.
                    if('error' in response.data)
                    {
                        var win = modal.create();
                        win.setClass('error');
                        win.setTitle('Firewall - Salvar usuário');
                        win.setContent('<div>Ocorreu um erro durante a tentativa de salvar os dados. Verifique se o nome de usuário não foi cadastrado antes.</div>');
                        win.addButton('Fechar', 'error', {
                            click : function() {
                                win.close();
                            }
                        });
                        win.display($scope);
                        return;
                    }

                    window.location.reload();
                });
            }
        });
        win.addButton('Não', 'error', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);

        return false;
    }

}]);

fwd.controller('requests', ['$scope', 'modal-window', 'ajax-request', 'window-location', 'uri-parser', function($scope, modal, ajax, location, uri) {
}]);

fwd.controller('rules', ['$scope', 'modal-window', 'ajax-request', 'window-location', 'uri-parser', function($scope, modal, ajax, location, uri) {

    /**
     * Dados do formulário
     */
    $scope.form = {
        code : 'function($ipData, $rule)\n{\n'+
            '\treturn false;\n' +
        '}',
        reason : '',
        expire : '',
        enabled : '1',
        id : -1
    }

    // Dados das listas carregadas.
    $scope.list = [];

    $scope.init = function(rules)
    {
        this.list = JSON.parse(atob(rules));
    }

    $scope.edit = function(rule)
    {
        this.form.code = atob(rule.Rule);
        this.form.reason = rule.RuleReason;
        this.form.expire = rule.RuleExpire;
        this.form.enabled = rule.RuleEnabled;
        this.form.id = rule.RuleID;
    }

    /**
     * Desabilita uma regra que estava habilitada.
     * 
     * @param object rule Objeto de regra que está em edição.
     */
    $scope.disable = function(rule)
    {
        this.edit(rule);
        this.form.enabled = '0';
        this.add();
    }

    /**
     * Habilita uma regra firewall que estava desabilitada.
     * 
     * @param object rule Objeto de regra que está em edição.
     */
    $scope.enable = function(rule)
    {
        this.edit(rule);
        this.form.enabled = '1';
        this.add();
    }

    /**
     * Função para adicionar uma nova regra ao firewll
     */
    $scope.add = function()
    {
        var win = modal.create();
        win.setTitle('Firewall - Regras de Bloqueio');
        win.setContent('<div>Você tem certeza que deseja <u>'+(this.form.id == -1 ? 'adicionar' : 'editar')+'</u> esta regra? <br><strong><em>Lembre-se: Você também pode ser afetado por ela.</em></strong></div>');
        win.setClass('warning');
        win.addButton('Sim', 'success', {
            click : function() {
                win.close();


                ajax.run({
                    'url' : location.current() + '/add',
                    'data' : uri.parse($scope.form),
                    'method' : 'POST',
                    'headers' : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {
                    if('error' in response.data)
                    {
                        var win = modal.create();
                        win.setTitle('Firewall - Regras de Bloqueio');
                        win.setClass('error');
                        win.setContent('<div>Ocorreu uma falha ao adicionar sua regra. Verifique a sintaxe de sua função e tente novamente.</div>');
                        win.addButton('Fechar', 'error', {
                            click : function() {
                                win.close();
                            }
                        });
                        win.display($scope);
                    }
                    else
                    {
                        window.location.reload();
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
    }

}])

fwd.controller('blacklist', ['$scope', 'modal-window', 'ajax-request', 'window-location', 'uri-parser', function($scope, modal, ajax, location, uri) {

    $scope.list = [];
    $scope.form = {
        ipAddress : '',
        reason : '',
        time : ''
    };

    /**
     * Inicializa a listagem na tela.
     */
    $scope.init = function(list)
    {
        this.list = JSON.parse(atob(list));
    };

    $scope.add = function()
    {
        var win = modal.create();
        win.setTitle('Firewall - Lista Negra - Adicionar Endereço IP');
        win.setClass('warning');
        win.setContent('<div>Você tem certeza que deseja adicionar o endereço <strong>{{form.ipAddress}}</strong> a lista negra?</div>');
        win.addButton('Sim', 'success', {
            click : function() {
                win.close();

                ajax.run({
                    'url' : location.current() + '/add',
                    'data' : uri.parse($scope.form),
                    'method' : 'POST',
                    'headers' : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {

                    window.location.reload();

                });

            }
        });
        win.addButton('Não', 'error', {
            click : function() {
                win.close();
            }
        });
        win.display($scope);
    }

    /**
     * Função para realizar a liberação do endereço ip do black list solicitado.
     * 
     * @param int blackListId Código da entrada do blacklist.
     */
    $scope.free = function(blackListId)
    {
        var win = modal.create();
        win.setTitle('Firewall - Lista Negra - Liberar Endereço IP');
        win.setClass('warning');
        win.setContent('<div>Você tem certeza que deseja liberar o endereço bloqueado?</div>');
        win.addButton('Sim', 'error', {
            click : function()
            {
                win.close();

                ajax.run({
                    url : location.current() + '/free',
                    data : uri.parse({
                        'BlackListID' : blackListId
                    }),
                    method : 'POST',
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function(response) {
                    var data = response.data;

                    if('success' in data)
                    {
                        window.location.reload();
                        return;
                    }

                    var win = modal.create();
                    win.setTitle('Firewall - Liberar Endereço IP');
                    win.setClass('error');
                    win.setContent('<div>Ocorreu uma falha ao liberar o endereço de ip solicitado.</div>');
                    win.addButton('OK', 'error', {
                        click : function() {
                            win.close();
                        }
                    });
                    win.display($scope);
                });
            }
        });
        win.addButton('Não', 'success', {
            click : function() {
                win.close();
            }
        })
        win.display($scope);
    };

}]);

fwd.controller('top', ['$scope', 'ajax-request', 'modal-window', 'window-location', function($scope, ajax, modal, location) {

    /**
     * Executa a função de logout para o painel de controle de firewall.
     */
    $scope.logout = function()
    {
        var win = modal.create();
        win.setTitle('Firewall - Encerrar');
        win.setClass('info');
        win.setContent('<div>Você tem certeza que deseja encerrar a sua sessão?</div>');
        win.addButton('Sim', 'info', {
            click : function() {
                win.close();

                ajax.run({
                    url : location.main() + '/firewall/admin/dashboard/logout',
                    method : 'POST',
                    headers : {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                }, function() {
                    window.location.href = location.main() + '/firewall/admin/'; 
                }, false, $scope);
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

fwd.controller('login', ['$scope', 'modal-window', 'ajax-request', 'uri-parser', 'window-location', function($scope, modal, ajax, uriParser, location) {

    /**
     * Credênciais de acesso digitadas pelo usuário.
     */
    $scope.credentials = {
        username : '',
        password : ''
    };

    /**
     * Envia os dados para realizar a tentativa de login no
     * gerenciador de firewall.
     * 
     * -> Faz a chamada de $scope.login()
     */
    $scope.submit = function()
    {
        ajax.run({
            url     : location.current() + '/login',
            method  : 'POST',
            data    : uriParser.parse(this.credentials),
            headers : {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }, function(response) {
            $scope.login(response.data);
        }, function(error) {
            $scope.login(false);
        }, $scope);

        return false;
    };

    /**
     * Executa informações de login e devolve as mensagens solicitadas em tela.
     * 
     * @param mixed data
     * -> Se data = false, houve erro durante a requisição e não foi possível
     *    completar a chamada.
     */
    $scope.login = function(data)
    {
        // Se ocorreu erros durante a requisição a página de login.
        if(data === false)
        {
            var win = modal.create();
            win.setTitle('Firewall - Login');
            win.setClass('error');
            win.setContent('<div>Ocorreu um erro durante a tentativa de login.<br>Tente mais tarde.</div>');
            win.addButton('Fechar', 'error', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
            return;
        }

        // Caso login seja realizado com sucesos, recarrega a tela.
        // Caso seja bloqueado via firewall. <- Todas as próximas requisições também serão bloqueadas.
        if(('success' in data && data.success) || ('blackListed' in data && data.blackListed))
        {
            window.location.reload();
            return;
        }

        // Zera a senha em tela.
        this.credentials.password = '';

        // Falha ao entrar no login. Nome de usuário e senha incorreto.
        if('error' in data && data.error)
        {
            var win = modal.create();
            win.setTitle('Firewall - Login');
            win.setClass('error');
            win.setContent('<div>Seu nome de usuário e/ou senha digitados estão incorretos.</div>');
            win.addButton('Fechar', 'error', {
                click : function() {
                    win.close();
                }
            });
            win.display($scope);
        }

        return;
    };

}]);


}) (angular);

