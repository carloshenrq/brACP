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
    'MENU'                      => 'Menu',
    'MENU_HOME'                 => 'Principal',
    'MENU_ADMIN'                => 'Administração',
    'MENU_ADMIN_BACKUP'         => 'Criar Backup',
    'MENU_ADMIN_THEMES'         => 'Atualizar Temas',
    'MENU_MYACC'                => 'Minha Conta',
    'MENU_MYACC_LOGIN'          => 'Entrar',
    'MENU_MYACC_CREATE'         => 'Registrar',
    'MENU_MYACC_RECOVER'        => 'Recuperar Conta',
    'MENU_MYACC_CHANGEPASS'     => 'Mudar Senha',
    'MENU_MYACC_CHANGEMAIL'     => 'Mudar E-mail',
    'MENU_MYACC_CHARS'          => 'Personagens',
    'MENU_MYACC_DONATIONS'      => 'Doações',
    'MENU_MYACC_LOGOUT'         => 'Sair',
    'MENU_RANKINGS'             => 'Classificações',
    'MENU_RANKINGS_CHARS'       => 'Jogadores',
    'MENU_RANKING_ECONOMY'      => 'Economia',

    /**
     * @refer templates/default.tpl
     * @author carloshenrq
     */
    'DEFAULT_DEVELOP_TITLE'     => 'Lembrete!',
    'DEFAULT_DEVELOP_MESSAGE'   => [
        'O Sistema está sendo executado em modo desenvolvimento!',
        'Algumas configurações podem não responder ao esperado.',
    ],
    'DEFAULT_BETA_TITLE'        => 'Você está executando uma versão beta!',
    'DEFAULT_BETA_MESSAGE'      => [
        'A Versão do sistema que está em execução não é estavel e ainda está em fase de testes!',
        'Por favor, fique atento as atualizações pois muitos erros podem ser corrigidos.'
    ],
    'DEFAULT_ADMIN_TITLE'       => 'Lembrete aos adminsitradores',
    'DEFAULT_ADMIN_MESSAGE'     => 'Algumas opções podem não estar habilitadas para administradores devido a questões de segurança.',

    /**
     * @refer templates/home.tpl
     * @author carloshenrq
     */
    'HOME_TITLE'                => 'Principal',
    'HOME_MESSAGE'              => [
        'Seja muito bem vindo ao painel de controle.'
    ],

    /**
     * @refer templates/home.tpl
     * @author carloshenrq
     */
    'LOGIN_TITLE'               => 'Minha Conta &raquo; Entrar',
    'LOGIN_ERR'                 => [
        'MISMATCH'  => 'Combinação de usuário e senha incorretos.',
        'DENIED'    => 'Acesso negado. Você não pode realizar login.',
    ],
    'LOGIN_SUCCESS'             => 'Login realizado com sucesso. Aguarde...',
    'LOGIN_MSG'                 => [
        'Para acessar os dados de sua conta, você deve realizar o acesso utilizando seu nome de usuário e senha.',
        'LOST_ACC'      => 'Perdeu sua conta? <label class="lbl-link" for="bracp-modal-recover">clique aqui</label>',
        'CREATE_ACC'    => 'Não possui uma conta? <label class="lbl-link" for="bracp-modal-create">clique aqui</label>',
    ],
    'LOGIN_PLACEHOLDER'         => [
        'USERID'    => 'Nome de usuário',
        'PASSWD'    => 'Senha de usuário',
    ],
    'LOGIN_BUTTONS'             => [
        'SUBMIT'                => 'Entrar',
        'RESET'                 => 'Limpar',
    ],

    /**
     * @refer templates/account.change.mail.ajax.tpl
     * @author carloshenrq
     */
    'CHANGEPASS_TITLE'          => 'Minha Conta &raquo; Alterar Senha',
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
     * Mensagens de erro padrão.
     */
    'ERR_RECAPTCHA'             => 'Código de verificação inválido. Verifique por favor.',

    /**
     * Define o status do jogador com o texto formatado.
     */
    'STATUS' => [
        0 => 'Desconectado',
        1 => 'Conectado',
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
