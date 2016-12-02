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

return
[
    /**
     * @refer templates/menu.tpl
     * @author carloshenrq
     */
    'MENU'  => [
        'TITLE'     => 'Menu',
        'HOME'      => 'Principal',
        'ADMIN'     => [
            'TITLE'     => 'Administração',
            'BACKUP'    => 'Criar Backup',
            'THEMES'    => 'Atualizar Temas',
            'CACHE'     => 'Limpar Cache',
            'MODS'      => 'Customizações',
            'PLAYERS'   => 'Jogadores',
            'UPDATE'    => 'Atualização de Sistema',
        ],
        'MYACC'     => [
            'TITLE'         => 'Minha Conta',
            'UNAUTHENTICATED'   => [
                'LOGIN'         => 'Entrar',
                'CREATE'        => 'Registrar',
                'CREATE.SEND'   => 'Código de Ativação',
                'RECOVER'       => 'Recuperar',
            ],
            'AUTHENTICATED'     => [
                'CHANGE'    => [
                    'PASS'  => 'Alterar Senha',
                    'MAIL'  => 'Mudar E-mail',
                ],
                'STORAGE'   => 'Armazém',
                'CHARS'     => 'Personagens',
                'LOGOUT'    => 'Sair',
            ],
        ],
        'MERCHANTS' =>  'Mercadores',
        'RANKINGS'  =>  [
            'TITLE'     => 'Classificações',
            'PLAYERS'   => 'Jogadores',
            'CHARS'     => 'Personagens',
            'ECONOMY'   => 'Economia',
            'WOE'       => 'Guerra do Emperium',
            'CASTLES'   => 'Castelos',
            'GUILDS'    => 'Clãs',
        ],
    ],

    /**
     * @refer templates/default.tpl
     * @author carloshenrq
     */
    'DEFAULT'   => [
        'DEVELOP'   => [
            'TITLE'     => 'Lembrete, o modo de desenvolvimento está ativado!',
            'MESSAGE'   => 'O Sistema está sendo executado em modo desenvolvimento! Algumas configurações podem não responder ao esperado.',
        ],
        'BETA'      => [
            'TITLE'     => 'Você está executando uma versão beta! <i>(%s)</i>',
            'MESSAGE'   => 'A Versão do sistema que está em execução não é estavel e ainda está em fase de testes! Por favor, fique atento as atualizações pois muitos erros podem ser corrigidos.',
        ],
        'ADMIN'     => [
            'TITLE'     => 'Lembrete aos adminsitradores!',
            'MESSAGE'   => 'Algumas opções podem não estar habilitadas para administradores devido a questões de segurança.',
        ],
    ],

    /**
     * @refer templates/home.tpl
     * @author carloshenrq
     */
    'HOME'      => [
        'TITLE'     => 'Principal',
        'MESSAGE'   =>  'Seja muito bem vindo ao painel de controle.'
    ],

    /**
     * @refer templates/account.login.ajax.tpl
     * @author carloshenrq
     */
    'LOGIN'     => [
        'TITLE'     => 'Minha Conta &raquo; Entrar',

        'ERROR'     => [
            'MISMATCH'  => 'Combinação de usuário e senha incorretos.',
            'DENIED'    => 'Acesso negado. Você não pode realizar login.',
        ],
        'SUCCESS'   => 'Login realizado com sucesso. Aguarde...',
        'MESSAGE'   => [
            'HEADER'        => 'Para ter acesso ao gerênciamento de sua conta, é necessário<br>'.
                                'que você realize login antes de continuar.',
            'CREATE'        =>  'Não possui uma conta? clique aqui para criar.',
            'RECOVER'       =>  'Perdeu sua conta? clique aqui para recuperar.',
        ],
        'HOLDER'    => [
            'USERID'    => 'Nome de usuário',
            'PASSWD'    => 'Senha de usuário',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Entrar',
            'RESET'     => 'Limpar',
            'CLOSE'     => 'Fechar',
        ],
    ],

    'LOGIN_BUTTONS'             => [
        'SUBMIT'                => 'Entrar',
        'RESET'                 => 'Limpar',
    ],

    /**
     * @refer templates/account.logout.ajax.tpl
     * @author carloshenrq
     */
    'LOGOUT'    => [
        'TITLE'     => 'Minha Conta &raquo; Sair',
        'SUCCESS'   => 'Logout efetuado com sucesso! Aguarde...',
    ],

    /**
     * @refer templates/account.register.ajax.tpl
     * @author carloshenrq
     */
    'CREATE'    => [
        'TITLE'     => 'Minha Conta &raquo; Registrar',
        'ERROR'     => [
            'DISABLED'  => 'Criação de contas está desativada.',
            'ADMIN.MODE'    => 'A Criação deste tipo de conta somente é possivel em modo administrador.',
            'MISMATCH'  => [
                'PASSWORD'  => 'As senhas digitadas não conferem!',
                'EMAIL'     => 'Os endereços de e-mail digitados não conferem!',
            ],
            'USED'      => 'Nome de usuário ou endereço de e-mail já está em uso.',
        ],
        'SUCCESS'   => 'Sua conta foi criada com sucesso! Você já pode realizar login.',
        'MESSAGE'   => [
            'HEADER'    => 'Para criar sua conta, é necessário que você informe os dados abaixo corretamente para que seja possivel seu acesso ao jogo e as funções do painel de controle.',
        ],
        'MAIL'      => [
            'TITLE' => 'Conta Registrada',
        ],
        'HOLDER'    => [
            'USERID'                => 'Nome de usuário',
            'PASSWORD'              => 'Senha de usuário',
            'PASSWORD.CONFIRM'      => 'Confirme a senha',
            'MALE'                  => 'Masculino',
            'FEMALE'                => 'Feminino',
            'EMAIL'                 => 'Endereço de e-mail',
            'EMAIL.CONFIRM'         => 'Confirme o e-mail',
            'BIRTHDATE'             => 'Data de Nascimento',
            'ACCEPT.TERMS'          => 'Eu concordo com os termos do servidor.',
        ],
        'FORMAT'    =>  [
            'BIRTHDATE'             => 'dd/MM/yyyy',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Registrar',
            'CLOSE'     => 'Fechar'
        ],
    ],

    /**
     * @refer templates/account.register.resend.ajax.tpl
     * @author carloshenrq
     */
    'RESEND'    => [
        'TITLE'     => 'Minha Conta &raquo; Código de Ativação',
        'ERROR'     => [
            'DISABLED'  => 'O código de ativação de contas está desativado.',
            'NOACC'     => 'Dados para reenvio do código de confirmação é inválido.',
            'USED'      => 'O Código de confirmação já foi utilizado ou é inválido.',
        ],
        'MAIL'      => [
            'TITLE.CONFIRM'     => 'Confirme seu Registro',
            'TITLE.CONFIRMED'   => 'Conta Confirmada',
        ],
        'SUCCESS'   => 'Código de confirmação enviado com sucesso para o e-mail cadastrado.',
        'CONFIRMED' => 'Sua conta foi ativada. Você já pode realizar login.',
        'MESSAGE'   => [
            'HEADER_NO_CODE'    => 'Para reenviar o código de ativação de sua conta, você deve digitar seu nome de usuário e endereço de e-mail cadastrados para que sejam reenviados com sucesso.',
            'HEADER_HAS_CODE'   => 'Já que você possui o código de ativação, você deve digita-lo abaixo para confirmar sua conta.',
        ],
        'HOLDER'    => [
            'USERID'    => 'Nome de usuário',
            'EMAIL'     => 'E-mail cadastradado',
            'CODE'      => 'Código de ativação',
            'HAS_CODE'  => 'Eu já possuo o código de ativação!',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Reenviar',
            'CONFIRM'   => 'Confirmar',
            'RESET'     => 'Limpar',
            'CLOSE'     => 'Fechar',
        ],
    ],

    /**
     * @refer templates/rankings.chars.ajax.tpl
     * @author carloshenrq
     */
    'RANKINGS'      => [
        'NO_CHARS'  => 'Nenhum personagem classificado.',

        'CHARS' => [
            'TITLE'     => 'Classificações &raquo; Personagens &raquo; Geral',
            'CAPTION'   => 'Top %s jogadores do servidor',
        ],

        'ECONOMY'   => [
            'TITLE'     => 'Classificações &raquo; Personagens &raquo; Economia',
            'CAPTION'   => 'Top %s jogadores mais ricos',
        ],

        'TABLE' => [
            'POSIT'     => 'Pos.',
            'NAME'      => 'Nome',
            'LEVEL'     => 'Nível',
            'CLASS'     => 'Classe',
            'STATUS'    => 'Status',
            'ZENY'      => 'Zeny',
        ],
    ],

    /**
     * @refer templates/account.storage.ajax.tpl
     * @author carloshenrq
     */
    'STORAGE'       => [
        'TITLE'     => 'Minha Conta &raquo; Armazém',
        'SUBTITLE'  => 'Armazém',

        'ERROR'     => [
            'NO_ITEMS'  => 'Você não possui itens no armazém para serem exibidos.',
        ],

        'MESSAGE'   => [
            'HEADER'    => 'Segue abaixo a lista de todos os itens que estão no seu armazém.',
        ],
    ],

    /**
     * @refer templates/account.recover.ajax.tpl
     * @author carloshenrq
     */
    'RECOVER'       => [
        'TITLE'     => 'Minha Conta &raquo; Recuperar',
        'ERROR'     => [
            'DISABLED'  => 'Recuperação de contas está desativada.',
            'MISMATCH'  => 'Combinação de usuário e e-mail não encontrados.',
            'USED'      => 'Código de recuperação inválido ou já utilizado.',
            'OTHER'     => 'Não foi possível recuperar a senha de usuário.',
        ],
        'CONFIRMED'     => 'Sua nova senha foi enviada para seu endereço de e-mail.',
        'MAIL'      => [
            'TITLE_CODE'     => 'Código de Recuperação',
            'TITLE_SEND'     => 'Recuperação de Usuário',
        ],
        'SUCCESS'   => [
            'CODE'  => 'Um código de recuperação foi enviado ao seu endereço de e-mail.',
            'SEND'  => 'Os dados de sua conta foram enviados ao seu endereço de e-mail.',
        ],
        'MESSAGE'   => [
            'HEADER_NO_CODE'    => 'Para recuperar seu nome de usuário, você deve preencher abaixo as informações corretas para que seja possível realizar esta recuperação.',
            'HEADER_HAS_CODE'   => 'Se você possuir o código de recuperação digite abaixo para enviarmos sua nova senha.',
        ],
        'HOLDER'    => [
            'USERID'    => 'Nome de usuário',
            'EMAIL'     => 'Endereço de e-mail',
            'CODE'      => 'Código de recuperação',
            'HAS_CODE'  => 'Já possuo o código de recuperação.',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Recuperar',
            'CONFIRM'   => 'Confirmar',
            'CLOSE'     => 'Fechar',
        ],
    ],

    /**
     * @refer templates/account.chars.tpl
     * @author carloshenrq
     */
    'CHARS'     => [
        'TITLE' => 'Minha Conta &raquo; Personagens',

        'ERROR' => [
            'NO_CHAR'   => 'Você não tem personagens para visualizar. Tente realizar login no jogo e criar um novo personagem!'
        ],

        'MESSAGE'   => 'Abaixo, segue a lista dos personagens que você possui no jogo para você realizar algumas ações como resetar posição, equipamentos e apararência...',

        'SUCCESS'   => [
            'POSIT'     => 'Local resetado com sucesso!',
            'APPEAR'    => 'Visual resetado com sucesso!',
            'EQUIP'     => 'Equipamentos resetados com sucesso!'
        ],

        'TABLE'     => [
            'NAME'          => 'Nome',
            'CLASS'         => 'Classe',
            'PARTY'         => 'Grupo',
            'GUILD'         => 'Clã',
            'LEVEL'         => 'Nível',
            'POSIT_NOW'     => 'Local',
            'POSIT_SAVE'    => 'Retorno',
            'ZENY'          => 'Zeny',
            'STATUS'        => 'Status',
            'ACTION'        => 'Resetar Informações',
            'NO_PARTY'      => 'Sem Grupo',
            'NO_GUILD'      => 'Sem clã'
        ],

        'BUTTONS'   => [
            'RESET_POSIT'   => 'Local',
            'RESET_APPEAR'  => 'Visual',
            'RESET_EQUIP'   => 'Equips',
        ]
    ],

    /**
     * @refer templates/account.change.password.ajax.tpl
     * @author carloshenrq
     */
    'CHANGEPASS'    =>  [
        'TITLE' => 'Minha Conta &raquo; Alterar Senha',

        'ERROR'    =>  [
            'NOADMIN'       => 'Nenhum administrador está permitido para alterar senha.',

            'MISMATCH1'     => 'Senha atual digitada não confere.',
            'MISMATCH2'     => 'Novas senhas digitadas não conferem.',
            'EQUALS'        => 'Sua nova senha não pode ser igual a senha anterior.',
            'OTHER'         => 'Ocorreu um erro durante a alteração de sua senha.',
        ],

        'MESSAGE'   =>  [
            'ADMIN' => '<strong>Nota.:</strong> Por motivos de segurança é recomendado que a alteração de senha para adminsitradores seja desabilitada!',

            'HEADER'    => 'Para realizar a alteração de sua senha é necessário que você digite sua senha atual, sua nova senha e confirme.',
        ],

        'SUCCESS'   =>  'Sua senha foi alterada com sucesso!',

        'HOLDER'    =>  [
            'ACTUAL.PASSWORD'   => 'Senha atual',
            'NEW.PASSWORD'      => 'Digite sua nova senha',
            'CONFIRM.PASSWORD'  => 'Confirme sua nova senha',
        ],

        'BUTTONS'   => [
            'SUBMIT'    => 'Alterar',
            'CLOSE'     => 'Fechar',
        ],

        'MAIL'      => [
            'TITLE' => 'Notificação: Alteração de Senha',
        ],
    ],

    /**
     * @refer templates/account.change.mail.ajax.tpl
     * @author carloshenrq
     */
    'CHANGEMAIL'    => [
        'TITLE' => 'Minha Conta &raquo; Mudar Email',

        'ERROR' => [
            'DISABLED'  => 'Alteração de e-mail está desativada.',
            'NOADMIN'   => 'Nenhum administrador está permitido a alterar seu endereço de email.',

            'MISMATCH1' => 'E-mail atual não confere com o digitado.',
            'MISMATCH2' => 'Os e-mails digitados não conferem.',
            'EQUALS'    => 'O Novo endereço de e-mail não pode ser igual ao atual.',
            'DELAY'     => 'Você não pode alterar seu endereço de e-mail agora. Tente mais tarde.',
            'TAKEN'     => 'Este endereço de e-mail já está em uso.',
            'OTHER'     => 'Ocorreu um erro durante a alteração do seu endereço.',
        ],

        'SUCCESS'   => 'Seu endereço de e-mail foi alterado com sucesso.',

        'MESSAGE'   => [
            'HEADER'    => 'Para realizar a alteração de seu endereço de e-mail é necessário que você digite seu e-mail atual, seu novo endereço de email e confirme!',
        ],

        'HOLDER'    => [
            'EMAIL'     => 'Email atual',
            'NEWEMAIL' => 'Novo email',
            'CONFIRM'   => 'Confirme seu novo email',
        ],

        'BUTTONS'   => [
            'SUBMIT'    => 'Alterar',
            'CLOSE'     => 'Fechar',
        ],

        'MAIL'  => [
            'TITLE' => 'Notificação: Alteração de E-mail',
        ],
    ],

    // Tradução dos arquivos administrativos.
    'ADMIN'     => [
        /**
         * @refer templates/admin.players.ajax.tpl
         * @author carloshenrq
         */
        'PLAYER'   => [
            'TITLE' => 'Administração &raquo; Jogadores',
        ],

        /**
         * @refer templates/admin.update.ajax.tpl
         * @author carloshenrq
         */
        'UPDATE'   => [
            'TITLE' => 'Administração &raquo; Atualização de Sistema',

            'WARNING'   => [
                'TITLE'     => 'Atenção!',
                'MESSAGE'   => 'Após feita a atualização, será necessário refazer a instalação do painel de controle.<br>'.
                                'São muitas alterações para serem aplicadas, é mais interessante fazer uma instalação limpa em alguns casos.<br>'.
                                '<br>'.
                                'Somente inicie o processo de atualização se você tem certeza que deseja continuar.',
            ],
            'VERSION'   => '<strong>Versão instalada:</strong> %s',
            'TABLE'     => [
                'CAPTION'       => 'Versões estaveis',
                'COLUMNS'       => [
                    'DESCRIPTION'   => 'Descrição',
                    'VERSION'       => 'Versão',
                    'DATE'          => 'Data',
                    'FILE'          => 'Arquivo',
                    'ACTION'        => 'Ação',
                ],

                'PRE_RELEASE'   => 'pré-lançamento',
                'INSTALL'       => 'Instalar',
            ],
        ],

        /**
         * @refer templates/admin.backup.ajax.tpl
         * @author carloshenrq
         */
        'BACKUP'    => [
            'TITLE' => 'Administração &raquo; Backup',
            'MESSAGE'   => [
                'SUCCESS'   => 'O Arquivo de backup foi criado com sucesso! '.
                               'Nome: <strong>%s</strong> com <strong>%d</strong> arquivos no tamanho de <strong>%s</strong>',
                'CHEKCSUM'  => 'Soma de verificação:',
                'MD5'       => 'md5',
                'SHA1'      => 'sha1',
                'SHA512'    => 'sha512',
            ],
        ],
    ],

    /**
     * Mensagens de alerta.
     */
    'WARNING'   => [
        'CACHE.ON'  => 'As informações contidas nesta página, podem demorar alguns instantes para serem atualizadas!',

        // issue #11 - Informativos de Formatação de Campos
        'PATTERN'   => [
            'NORMAL'    => 'Somente letras e números. Entre 4 e 32 caracteres',
            'SPECIAL'   => 'Letras, números, espaços e caracteres especiais (@ $ # &#37; & * !). Entre 4 e 32 caracteres',
            'ALL'       => 'Sem restrições. Entre 4 e 32 caracteres',
        ]
    ],

    /**
     * Mensagens de erro padrão.
     */
    'ERRORS'    => [
        'RECAPTCHA' => 'Código de verificação inválido. Verifique por favor.',
        'NO_MOBILE' => 'Indisponivel para acesso mobile.',
        'MAINTENCE' => 'Em manutenção.',

        'REGEXP'    => 'Falha na restrição de pattern de alguns campos. Tente novamente.',

        'NOT.FOUND' => [
            'TITLE'         => 'Ops! Parece que a página que você estava procurando não foi encontrada!',
            'MESSAGE'       => 'x____x\' nos desculpe mas a página que você estava procurando, não foi encontrada...<br>'.
                                'Você pode voltar a página principal clicando <span class="url-link" data-href="'.BRACP_DIR_INSTALL_URL.'">aqui</span>.',
        ],

        'ACCESS'    => [
            'TITLE'         => 'Acesso negado',
            'DENIED'        => 'Você não possui direito de acesso a este local ou a página solicitada não existe!',
            'NEED_LOGOUT'   => 'Você não pode estar logado para realizar esta ação.<br>'.
                                'Para sair, <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body">clique aqui</span>.',
            'NEED_SIGNIN'   => 'Você precisa estar logado para realizar esta ação.<br>'.
                                'Para entrar, <label class="fake-link" for="modal-login">clique aqui</label>.',
        ],

    ],

    'VENDING'   => [
        'TITLE'     => 'Mercadores Online',
        'MESSAGE'   => 'Segue abaixo a lista de todos os mercadores online dentro do jogo e os itens que estes estão vendendo.',

        'NO.VENDING'    => 'Nenhum mercador com venda aberta online para exibir.',


        'BUTTONS'   =>  [
            'REFRESH'   => 'Atualizar Lista'
        ],
    ],

    /**
     *
     *
     * Configuração para e-mail.
     *
     *
     */
    'MAIL'  => [

        /**
         * @refer templates/mail.default.tpl
         * @author carloshenrq
         */
        'TITLE' => 'Olá, <strong>%s</strong>.<br>',

        'MESSAGE'   => [
            'FOOTER'    => '<i>Este e-mail foi enviado por <strong>%s</strong> '.
                            'através da solicitação feita pelo endereço ip <strong>%s</strong> '.
                            'às <strong>%s</strong>.<br>'.
                            'Se não foi você que fez essa solicitação, por favor, desconsidere esta mensagem.</i>',
        ],

        /**
         * @refer templates/mail.recover{.code}.tpl
         * @author carloshenrq
         */
        'RECOVER'   => [
            /**
             * @refer templates/mail.recover.tpl
             * @author carloshenrq
             */
            'MESSAGE'   => 'Sua senha foi recuperada com sucesso. Segue abaixo sua senha para login no jogo:<br>'.
                            '<br>'.
                            'Sua senha: <strong>%s</strong><br>',
                            '<br>'.
                            'Para realizar login utilize esta senha apartir de agora.',
            /**
             * @refer templates/mail.recover.code.tpl
             * @author carloshenrq
             */
            'CODE'      => 'Uma tentativa de recuperação de senha foi realizada na sua conta.<br>'.
                            'Para confirmar essa tentativa de recuperação, por favor, utilize o código de recuperação no painel de controle.<br>'.
                            '<br>'.
                            '<strong>Código de Recuperação</strong>: <u>%1$s</u><br>'.
                            '<i>Código válido até <strong>%2$s</strong>.</i><br>'.
                            '<br>'.
                            'Para aplicar o código de recuperação, você deve acessar o menu: <i><strong>Minha Conta &raquo; Recuperar</strong></i>.<br>'.
                            '<br>'.
                            'Após inserir o código de recuperação, você receberá sua nova senha por e-mail.',
        ],
        /**
         * @refer templates/mail.create{.code}.tpl
         * @author carloshenrq
         */
        'CREATE'    => [
            /**
             * @refer templates/mail.create.tpl
             * @author carloshenrq
             */
            'MESSAGE'   => 'Agradecemos seu registro e esperamos que você tenha muitas horas de diversão em nosso servidor.',

            /**
             * @refer templates/mail.create.code.tpl
             * @author carloshenrq
             */
            'CODE'      => 'Para confirmar a criação da sua conta, é necessário que você confirme sua identidade de e-mail inserindo o código abaixo no painel de controle.<br>'.
                            '<br>' .
                            '<strong>Código de Ativação:</strong> <u>%1$s</u><br>'.
                            '<i>Código válido até <strong>%2$s</strong>.</i><br>'.
                            '<br>'.
                            'Para aplicar o código de ativação, você deve acessar o menu: <i><strong>Minha Conta &raquo; Código de Ativação</strong></i>.<br>'.
                            '<br>'.
                            'Após ativar sua conta, você receberá informando que o servidor aceitou a ativação de sua conta.',
            /**
             * @refer templates/mail.create.code.success.tpl
             * @author carloshenrq
             */
            'ACTIVATED' => 'Sua conta foi ativada com sucesso! Você já pode realizar login.'
        ],

        /**
         * @refer templates/mail.change.password.tpl
         * @author carloshenrq
         */
        'CHANGEPASS'    => [
            'MESSAGE'   => 'Este e-mail é apenas uma notificação para informar que sua senha foi alterada.',
        ],

        /**
         * @refer templates/mail.change.mail.tpl
         * @author carloshenrq
         */
        'CHANGEMAIL'    => [
            'MESSAGE'   => 'Este e-mail é apenas uma notificação para informar que seu endereço de email foi alterado.<br>'.
                            'Antigo: <strong>%s</strong><br>'.
                            'Novo: <strong>%s</strong><br>',
        ],
    ],

    'SERVER.STATUS' =>  [
        'SERVER'    => 'Servidor',
        'TEXT'      => 'Estado',
        'PLAYER'    => 'Jogadores Online',

        'LOADING'   => 'Carregando informações do servidor...',

        'STATE'     => [
            0       => 'Offline',
            1       => 'Online',
        ],
    ],

    'FOOTER'    => [
        'LANGUAGE'  => 'Idioma',
        'THEME'     => 'Tema',
        'ADDRESS'   => 'IP',
        'NAVIGATOR' => 'Navegador',
    ],

    /**
     * Define o status do jogador com o texto formatado.
     */
    'STATUS' => [
        0 => 'Desconectado',
        1 => 'Conectado',
    ],

    'ITEM'  =>  [
        'NOT_IDENTIFY'  => 'Não identificado',
        'TYPE'  => [
            0 => 'Usavél (Cura)',
            2 => 'Usavél',
            3 => 'Não usavél',
            4 => 'Equipamento',
            5 => 'Armamento',
            6 => 'Carta',
            7 => 'Ovo Pet',
            8 => 'Acessório Pet',
            10 => 'Munição',
            11 => 'Usavél (Delay)',
            12 => 'Shadow Equipment',
            18 => 'Usavél',
        ],
    ],

    /**
     * Lista de classes para exibição no painel de controle.
     * 
     * @author Megasantos
     * @link https://github.com/Megasantos/Fluxcp/blob/master/config/jobs.php
     *
     * @var array
     */
    'JOBS' => [
        0    => 'Aprendiz',
        1    => 'Espadachim',
        2    => 'Mago',
        3    => 'Arqueiro',
        4    => 'Noviço',
        5    => 'Mercador',
        6    => 'Gatuno',
        7    => 'Cavaleiro',
        8    => 'Sacerdote',
        9    => 'Bruxo',
        10   => 'Ferreiro',
        11   => 'Caçador',
        12   => 'Mercenário',
        //13   => 'Cavaleiro (Peco)',
        14   => 'Templário',
        15   => 'Monge',
        16   => 'Sábio',
        17   => 'Arruaceiro',
        18   => 'Alquimista',
        19   => 'Bardo',
        20   => 'Odalisca',
        //21   => 'Templário (Peco)',
        22   => 'Casamento',
        23   => 'Super Aprendiz',
        24   => 'Justiceiro',
        25   => 'Ninja',
        26   => 'Xmas',
        27   => 'Summer',
        28   => 'Hanbok',
        4001 => 'Aprendiz T.',
        4002 => 'Espadachim T.',
        4003 => 'Mago T.',
        4004 => 'Arqueiro T.',
        4005 => 'Noviço T.',
        4006 => 'Mercador T.',
        4007 => 'Gatuno T.',
        4008 => 'Lorde',
        4009 => 'Sumo-Sacerdote',
        4010 => 'Arquimago',
        4011 => 'Mestre-Ferreiro',
        4012 => 'Atirador de Elite',
        4013 => 'Algoz',
        //4014 => 'Lorde (Peco)',
        4015 => 'Paladino',
        4016 => 'Mestre',
        4017 => 'Professor',
        4018 => 'Desordeiro',
        4019 => 'Criador',
        4020 => 'Menestrel',
        4021 => 'Cigana',
        //4022 => 'Paladino (Peco)',
        4023 => 'Aprendiz Bebê',
        4024 => 'Espadachim Bebê',
        4025 => 'Mago Bebê',
        4026 => 'Arqueiro Bebê',
        4027 => 'Noviço Bebê',
        4028 => 'Mercador Bebê',
        4029 => 'Gatuno Bebê',
        4030 => 'Cavaleiro Bebê',
        4031 => 'Sacerdote Bebê',
        4032 => 'Bruxo Bebê',
        4033 => 'Ferreiro Bebê',
        4034 => 'Caçador Bebê',
        4035 => 'Mercenário Bebê',
        //4036 => 'Cavaleiro Bebê (Peco)',
        4037 => 'Templário Bebê',
        4038 => 'Monge Bebê',
        4039 => 'Sábio Bebê',
        4040 => 'Arruaceiro Bebê',
        4041 => 'Alquimista Bebê',
        4042 => 'Bardo Bebê',
        4043 => 'Odalisca Bebê',
        //4044 => 'Templário Bebê (Peco',
        4045 => 'Super Aprendiz Bebê',
        
        4046 => 'Taekwon',
        4047 => 'Mestre Taekwon',
        //4048 => 'Mestre Taekwon (Voo)',
        4049 => 'Espiritualista',
        4050 => 'Jiang Shi',
        4051 => 'Death Knight',
        4052 => 'Dark Collector',
        4054 => 'Cavaleiro Rúnico',
        4055 => 'Arcano',
        4056 => 'Sentinela',
        4057 => 'Arcebispo',
        4058 => 'Mecânico',
        4059 => 'Sicário',
        4060 => 'Cavaleiro Rúnico T.',
        4061 => 'Arcano T.',
        4062 => 'Sentinela T.',
        4063 => 'Arcebispo T.',
        4064 => 'Mecânico T.',
        4065 => 'Sicário T.',
        4066 => 'Guardião Real',
        4067 => 'Feiticeiro',
        4068 => 'Trovador',
        4069 => 'Musa',
        4070 => 'Shura',
        4071 => 'Bioquímico',
        4072 => 'Renegado',
        4073 => 'Guardião Real T.',
        4074 => 'Feiticeiro T.',
        4075 => 'Trovador T.',
        4076 => 'Musa T.',
        4077 => 'Shura T.',
        4078 => 'Bioquímico T.',
        4079 => 'Renegado T.',
        //4080 => 'Cavaleiro Rúnico (Dragão)',
        //4081 => 'Cavaleiro Rúnico T. (Dragão)',
        //4082 => 'Guardião Real (Grifo)',
        //4083 => 'Guardião Real T. (Grifo)',
        //4084 => 'Sentinela (Lobo)',
        //4085 => 'Sentinela T. (Lobo)',
        //4086 => 'Mecânico (Robô)',
        //4087 => 'Mecânico T. (Robô)',
        4096 => 'Cavaleiro Rúnico Bebê',
        4097 => 'Arcano Bebê',
        4098 => 'Sentinela Bebê',
        4099 => 'Arcebispo Bebê',
        4100 => 'Mecânico Bebê',
        4101 => 'Sicário Bebê',
        4102 => 'Guardião Real Bebê',
        4103 => 'Feiticeiro Bebê',
        4104 => 'Trovador Bebê',
        4105 => 'Musa Bebê',
        4106 => 'Shura Bebê',
        4107 => 'Bioquímico Bebê',
        4108 => 'Renegado Bebê',
        
        //4109 => 'Cavaleiro Rúnico Bebê (Dragão)',
        //4110 => 'Guardião Real Bebê (Grifo)',
        //4111 => 'Sentinela Bebê (Lobo)',
        //4112 => 'Mecânico Bebê (Robô)',
        
        4190 => 'Super Aprendiz T.',
        4191 => 'Super Aprendiz Bebê T.',
        
        4211 => 'Kagerou',
        4212 => 'Oboro',
        4215 => 'Rebelde'
    ],
];
