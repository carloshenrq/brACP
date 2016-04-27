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
            'MODS'      => 'Customizações',
            'PLAYERS'   => 'Jogadores',
            'DONATION'  => 'Doações',
        ],
        'MYACC'     => [
            'TITLE'         => 'Minha Conta',
            'UNAUTHENTICATED'   => [
                'LOGIN'         => 'Entrar',
                'CREATE'        => 'Registrar',
                'CREATE_SEND'   => 'Código de Ativação',
                'RECOVER'       => 'Recuperar',
            ],
            'AUTHENTICATED'     => [
                'CHANGE'    => [
                    'PASS'  => 'Alterar Senha',
                    'MAIL'  => 'Mudar E-mail',
                ],
                'STORAGE'   => 'Armazém',
                'CHARS'     => 'Personagens',
                'DONATION'  => 'Doações (%s)',
                'LOGOUT'    => 'Sair (%s)',
            ],
        ],
        'RANKINGS'  =>  [
            'TITLE'     => 'Classificações',
            'CHARS'     => 'Jogadores',
            'ECONOMY'   => 'Economia',
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
            'HEADER'     => 'Para acessar os dados de sua conta, você deve realizar o acesso utilizando seu nome de usuário e senha.',
            'LOST_ACC'      => 'Perdeu sua conta? <label class="lbl-link" for="bracp-modal-recover">clique aqui</label>',
            'CREATE_ACC'    => 'Não possui uma conta? <label class="lbl-link" for="bracp-modal-create">clique aqui</label>',
        ],
        'HOLDER'    => [
            'USERID'    => 'Nome de usuário',
            'PASSWD'    => 'Senha de usuário',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Entrar',
            'RESET'     => 'Limpar',
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
            'PASSWORD_CONFIRM'      => 'Confime a senha',
            'MALE'                  => 'Masculino',
            'FEMALE'                => 'Feminino',
            'EMAIL'                 => 'Endereço de e-mail',
            'EMAIL_CONFIRM'         => 'Confirme o e-mail',
            'ACCEPT_TERMS'          => 'Eu concordo com os termos do servidor.',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Registrar',
            'RESET'     => 'Limpar'
        ],
    ],

    /**
     * @refer templates/account.register.resend.ajax.tpl
     * @author carloshenrq
     */
    'RESEND'    => [
        'TITLE'     => 'Minhca Conta &raquo; Código de Ativação',
        'ERROR'     => [
            'DISABLED'  => 'O código de ativação de contas está desativado.',
            'NOACC'     => 'Dados para reenvio do código de confirmação é inválido.',
            'USED'      => 'O Código de confirmação já foi utilizado ou é inválido.',
        ],
        'MAIL'      => [
            'TITLE_CONFIRM'     => 'Confirme seu Registro',
            'TITLE_CONFIRMED'   => 'Conta Confirmada',
        ],
        'SUCCESS'   => 'Código de confirmação enviado com sucesso para o e-mail cadastrado.',
        'CONFIRMED' => 'Você confirmou com sucesso sua conta. Você já pode realizar login.',
        'MESSAGE'   => [
            'HEADER'    => 'Para reenviar o código de ativação de sua conta, você deve digitar seu nome de usuário e endereço de e-mail cadastrados para que sejam reenviados com sucesso.',
        ],
        'HOLDER'    => [
            'USERID' => 'Nome de usuário',
            'EMAIL'  => 'E-mail cadastradado',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Reenviar',
            'RESET'     => 'Limpar',
        ],
    ],

    /**
     * @refer templates/rankings.chars.ajax.tpl
     * @author carloshenrq
     */
    'RANKINGS'      => [
        'NO_CHARS'  => 'Não existem personagens para este ranking.',

        'CHARS' => [
            'TITLE'     => 'Rankings &raquo; Personagens &raquo; Geral',
            'CAPTION'   => 'Top 100 jogadores do servidor',
        ],

        'ECONOMY'   => [
            'TITLE'     => 'Rankings &raquo; Personagens &raquo; Economia',
            'CAPTION'   => 'Top 100 jogadores mais ricos',
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
            'INVALID'   => 'Código de recuperação inválido ou já utilizado.',
            'OTHER'     => 'Não foi possível recuperar a senha de usuário.',
        ],
        'MAIL'      => [
            'TITLE_CODE'     => 'Código de Recuperação',
            'TITLE_SEND'     => 'Recuperação de Usuário',
        ],
        'SUCCESS'   => [
            'CODE'  => 'Foi enviado um e-mail contendo os dados de recuperação. Verifique seu e-mail.',
            'SEND'  => 'Os dados de sua conta foram enviados ao seu endereço de e-mail.',
        ],
        'MESSAGE'   => [
            'HEADER'    => 'Para recuperar seu nome de usuário, você deve preencher abaixo as informações corretas para que seja possível realizar esta recuperação.',
        ],
        'HOLDER'    => [
            'USERID'    => 'Nome de usuário',
            'EMAIL'     => 'Endereço de e-mail',
        ],
        'BUTTONS'   => [
            'SUBMIT'    => 'Recuperar',
            'RESET'     => 'Limpar',
        ],
    ],

    /**
     * @refer templates/account.change.password.ajax.tpl
     * @author carloshenrq
     */
    'CHANGEPASS_TITLE'          => 'Minha Conta &raquo; Mudar Senha',
    'CHANGEPASS_NOADMIN'        => 'Nenhum administrador está permitido a alterar sua senha aqui.',
    'CHANGEPASS_NOADMIN_MSG'    => [
        '<strong>Nota.:</strong> Por motivos de segurança é recomendado que a alteração de senha para adminsitradores seja desabilitada!',
        '',
        'Para alterar, edite o arquivo <strong>config.php</strong> e mude a configuração <strong>BRACP_ALLOW_ADMIN_CHANGE_PASSWORD</strong> para <strong>false</strong>',
    ],
    'CHANGEPASS_ERR'            => [
        'ADMIN'                 => 'Usuários do tipo administrador não podem realizar alteração de senha.',
        'MISMATCH1'             => 'Senha atual digitada não confere.',
        'MISMATCH2'             => 'Novas senhas digitadas não conferem.',
        'EQUALS'                => 'Sua nova senha não pode ser igual a senha anterior.',
        'OTHER'                 => 'Ocorreu um erro durante a alteração de sua senha.',
    ],
    'CHANGEPASS_SUCCESS'        => 'Sua senha foi alterada com sucesso!',
    'CHANGEPASS_MSG'            => [
        'Para realizar a alteração de sua senha é necessário que você digite sua senha atual, sua nova senha e confirme.',
    ],
    'CHANGEPASS_MAIL'           => [
        'NOTIFY_CHANGED'    => 'Notificação: Alteração de Senha',
    ],
    'CHANGEPASS_PLACEHOLDER'    => [
        'ACTUAL_PASSWORD'       => 'Senha atual',
        'NEW_PASSWORD'          => 'Digite sua nova senha',
        'CONFIRM_PASSWORD'      => 'Confirme sua nova senha',
    ],
    'CHANGEPASS_BUTTONS'        => [
        'SUBMIT'                => 'Alterar',
        'RESET'                 => 'Limpar',
    ],

    /**
     * @refer templates/account.change.mail.ajax.tpl
     * @author carloshenrq
     */
    'CHANGEMAIL_TITLE'          => 'Minha Conta &raquo; Mudar Email',
    'CHANGEMAIL_ERR'            => [
        'DISABLED'      => 'Alteração de e-mail está desativada.',
        'NO_ADMIN'      => 'Nenhum administrador está permitido a alterar seu endereço de email.',

        'MISMATCH1'     => 'E-mail atual não confere com o digitado.',
        'MISMATCH2'     => 'Os e-mails digitados não conferem.',
        'EQUALS'        => 'O Novo endereço de e-mail não pode ser igual ao atual.',
        'OTHER'         => 'Ocorreu um erro durante a alteração do seu endereço.',
    ],
    'CHANGEMAIL_SUCCESS'        => 'Seu endereço de e-mail foi alterado com sucesso.',
    'CHANGEMAIL_MSG'            => [
        'Para realizar a alteração de seu endereço de e-mail é necessário que você digite seu e-mail atual, seu novo endereço de email e confirme!',
    ],
    'CHANGEMAIL_MAIL'           => [
        'NOTIFY_CHANGED' => 'Notificação: Alteração de E-mail'
    ],
    'CHANGEMAIL_PLACEHOLDER'    => [
        'EMAIL'     => 'Email atual',
        'NEW_EMAIL' => 'Novo email',
        'CONFIRM'   => 'Confirme seu novo email',
    ],
    'CHANGEMAIL_BUTTONS'        => [
        'SUBMIT'    => 'Mudar',
        'RESET'     => 'Limpar',
    ],

    /**
     * @refer templates/account.chars.ajax.tpl
     * @author carloshenrq
     */
    'CHARS_TITLE'               => 'Minha Conta &raquo; Personagens',
    'CHARS_MSG'                 => [
        'Segue abaixo a lista dos personagens gerenciaveis para sua conta.',
    ],
    'CHARS_ERR'                 => [
        'NO_CHARS'  => 'Você não possui personagens criados para gerênciar.',
        'OTHER'     => 'Impossível realizar ação solicitada.',
    ],
    'CHARS_SUCCESS'             => [
        'APPEAR'        => 'Visual resetado com sucesso.',
        'POSIT'         => 'Posição resetado com sucesso.',
        'EQUIP'         => 'Equipamento resetado com sucesso.',
        'NO_CHANGES'    => 'Comando(s) executado(s) com sucesso. Nenhum personagem foi alterado.',
    ],
    'CHARS_BUTTONS'             => [
        'SUBMIT'    => 'Enviar',
        'RESET'     => 'Limpar'
    ],
    'CHARS_TABLE' => [
        'CHARID'        => 'Cód.',
        'NAME'          => 'Nome',
        'CLASS'         => 'Classe',
        'ZENY'          => 'Zeny',
        'LEVEL'         => 'Nível',
        'STATUS'        => 'Status',
        'MAP'           => 'Mapa',
        'MAP_RETURN'    => 'Retorno',
        'RESET'         => 'Resetar',
        'RESET_APPEAR'  => 'Visual',
        'RESET_POSIT'   => 'Local',
        'RESET_EQUIP'   => 'Equip',
    ],

    /**
     * @refer templates/admin.backup.ajax.tpl
     * @author carloshenrq
     */
    'ADMIN_BACKUP_TITLE'        => 'Administração &raquo; Backup',
    'ADMIN_BACKUP_MSG'          => [
        'O Arquivo de backup foi criado com sucesso! Nome:',
        'arquivos no tamanho de',
        'CHECKSUM'  => 'Soma de verificação',
        'MD5'       => 'md5',
        'SHA1'      => 'sha1',
        'SHA512'    => 'sha512',
    ],

    /**
     * @refer templates/admin.players.ajax.tpl
     * @author carloshenrq
     */
    'ADMIN_PLAYER_TITLE'        => 'Administração &raquo; Jogadores',

    /**
     * @refer templates/admin.players.ajax.tpl
     * @author carloshenrq
     */
    'ADMIN_DONATION_TITLE'        => 'Administração &raquo; Doações',

    'ADMIN'     => [
    ],

    /**
     * Mensagens de erro padrão.
     */

    'ERRORS'    => [
        'RECAPTCHA' => 'Código de verificação inválido. Verifique por favor.',
        'NO_MOBILE' => 'Indisponivel para acesso mobile.',
        'MAINTENCE' => 'Em manutenção.',
    ],

    'ERR_TITLE'                 => 'Acesso negado',
    'ERR_ACCESS_DENIED'         => 'Você não possui direito de acesso a este local!',
    'ERR_NEED_SIGNIN'           => [
        'Você precisa estar logado para realizar esta ação.',
        'Para entrar, <label class="lbl-link" for="bracp-modal-login">clique aqui</label>.',
    ],
    'ERR_NEED_SIGNOUT'          => [
        'Você não pode estar logado para realizar esta ação.',
        'Para sair, <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body">clique aqui</span>.',
    ],

    /**
     *
     *
     * Configuração para e-mail.
     *
     *
     */
    'MAIL_TITLE'                => 'Olá, ',
    'MAIL_MSG'                  => [
        'Este e-mail foi enviado por',
        'através da solicitação feita pelo endereço ip',
        'às',
        'Se não foi você que fez essa solicitação, por favor, desconsidere esta mensagem.'
    ],

    'MAIL_RECOVER_MSG'          => [
        'Sua senha foi recuperada com sucesso. Segue abaixo sua senha para login no jogo:',
        'Sua senha:',
        'Para realizar login utilize esta senha apartir de agora.',
    ],

    'MAIL_RECOVERCODE_MSG'      => [
        'Uma tentativa de recuperação de senha foi realizada na sua conta.',
        'Para confirmar essa tentativa de recuperação, por favor, clique no link abaixo ou copie e cole o endereço em seu navegador.',
        'Link válido até',
        'Após acessar o link, você receberá um segundo e-mail com a nova senha gerada aleatóriamente pelo sistema.',
    ],

    'MAIL_CREATE_MSG'           => [
        'Agradecemos seu registro e esperamos que você tenha muitas horas de diversão em nosso servidor.',
    ],

    'MAIL_CREATECODE_MSG'       => [
        'Para confirmar a criação da sua conta, é necessário que você confirme sua identidade de e-mail clicando no link abaixo.',
        'Link válido até',
        'Após acessar o link, você receberá um segundo e-mail informando que sua conta foi confirmada com sucesso.',
    ],

    'MAIL_CHANGEPASS_MSG'       => [
        'Este e-mail é apenas uma notificação para informar que sua senha foi alterada.',
    ],

    'MAIL_CHANGEMAIL_MSG'       => [
        'Este e-mail é apenas uma notificação para informar que seu endereço de email foi alterado.',
        'Antigo:',
        'Novo:',
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
