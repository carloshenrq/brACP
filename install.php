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

// Se o arquivo de configurações existe, não existe motivos para entrar na tela de instalação
//  do painel.
// @issue 5
if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config.php'))
    exit;

if(!defined('PHP_VERSION'))
    define('PHP_VERSION', phpversion(), false);

// Adicionado leitura da classe principal para teste dos temas.
require_once __DIR__ . '/app/Themes.php';
require_once __DIR__ . '/app/Language.php';

// Obtém todos os temas contidos na pasta
$themes = Themes::readAll();

// Obtém todas as linguagens disponiveis.
$langs = Language::readAll();

// Obtém as permissões de arquivo para notificar o usuário sobre as informações.
$writeable = is_writable(__DIR__);

// Configurações padrão.
$config = [
    // Configurações Gerais
    'BRACP_DEFAULT_TIMEZONE'                => @date_default_timezone_get(),
    'BRACP_URL'                             => 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
    'BRACP_DIR_INSTALL_URL'                 => $_SERVER['REQUEST_URI'],
    'BRACP_TEMPLATE_DIR'                    => __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
    'BRACP_ENTITY_DIR'                      => __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'Entity',
    'BRACP_MD5_PASSWORD_HASH'               => 1,
    'BRACP_MAIL_REGISTER_ONCE'              => 1,
    'BRACP_CHANGE_MAIL_DELAY'               => 60,
    'BRACP_ALLOW_CHANGE_MAIL'               => 1,
    'BRACP_ALLOW_CREATE_ACCOUNT'            => 1,
    'BRACP_CONFIRM_ACCOUNT'                 => 0,
    'BRACP_ALLOW_ADMIN'                     => 1,
    'BRACP_ALLOW_ADMIN_GMLEVEL'             => 99,
    'BRACP_ALLOW_LOGIN_GMLEVEL'             => 0,
    'BRACP_ALLOW_ADMIN_CHANGE_PASSWORD'     => 0,
    'BRACP_ALLOW_RANKING'                   => 1,
    'BRACP_ALLOW_SHOW_CHAR_STATUS'          => 1,
    'BRACP_ALLOW_RANKING_ZENY'              => 1,
    'BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY'    => 1,
    'BRACP_DEVELOP_MODE'                    => 0,
    'BRACP_MAINTENCE'                       => 0,
    'BRACP_VERSION'                         =>'0.2.1-beta',

    // MySQL
    'BRACP_SQL_DRIVER'                      => 'pdo_mysql',
    'BRACP_SQL_HOST'                        => '127.0.0.1:3306',
    'BRACP_SQL_USER'                        => 'ragnarok',
    'BRACP_SQL_PASS'                        => 'ragnarok',
    'BRACP_SQL_DBNAME'                      => 'ragnarok',

    // Servidor de E-mail
    'BRACP_ALLOW_MAIL_SEND'                 => 1,
    'BRACP_MAIL_HOST'                       => '127.0.0.1',
    'BRACP_MAIL_PORT'                       => 25,
    'BRACP_MAIL_USER'                       => 'ragnarok',
    'BRACP_MAIL_PASS'                       => 'ragnarok',
    'BRACP_MAIL_FROM'                       => 'noreply@127.0.0.1',
    'BRACP_MAIL_FROM_NAME'                  => 'noreply',
    'BRACP_NOTIFY_CHANGE_PASSWORD'          => 1,
    'BRACP_NOTIFY_CHANGE_MAIL'              => 1,
    'BRACP_ALLOW_RECOVER'                   => 1,
    'BRACP_RECOVER_BY_CODE'                 => 1,
    'BRACP_RECOVER_CODE_EXPIRE'             => 120,
    'BRACP_RECOVER_STRING_LENGTH'           => 8,
    'BRACP_RECOVER_RANDOM_STRING'           => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',

    // reCAPTCHA
    'BRACP_RECAPTCHA_ENABLED'               => 1,
    'BRACP_RECAPTCHA_PUBLIC_KEY'            => '',
    'BRACP_RECAPTCHA_PRIVATE_KEY'           => '',
    'BRACP_RECAPTCHA_PRIVATE_URL'           => 'https://www.google.com/recaptcha/api/siteverify',

    // PagSeguro
    'PAG_INSTALL'                           => 1,
    'PAG_EMAIL'                             => '',
    'PAG_TOKEN'                             => '',
    'DONATION_AMOUNT_MULTIPLY'              => 100,
    'DONATION_AMOUNT_USE_RATE'              => 1,
    'DONATION_AMOUT_SHOW_RATE_CALC'         => 1,
    'DONATION_SHOW_NEXT_PROMO'              => 1,
    'DONATION_INTERVAL_DAYS'                => 3,

    // Outros
    'BRACP_ALLOW_RESET_APPEAR'              => 1,
    'BRACP_ALLOW_RESET_POSIT'               => 1,
    'BRACP_ALLOW_RESET_EQUIP'               => 1,
    'BRACP_REGEXP_USERNAME'                 => '[a-zA-Z0-9]{4,24}',
    'BRACP_REGEXP_PASSWORD'                 => '[a-zA-Z0-9]{4,20}',
    'BRACP_REGEXP_EMAIL'                    => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}',
    'BRACP_ALLOW_CHOOSE_THEME'              => 1,
    'BRACP_DEFAULT_THEME'                   => 'default',
    'BRACP_DEFAULT_LANGUAGE'                => 'pt_BR',
]; 


// Verifica se dados de instalação foram recebidos e pode escrever o arquivo
//  de configuração no disco sem problemas.
if($writeable && isset($_POST) && !empty($_POST))
{
    // Inicializa o cabeçalho do arquivo de configurações que será escrito.
    $configFile = "<?php\n";
    $configFile .= "/**\n";
    $configFile .= " * Arquivo de configuração gerado pela instalação do sistema.\n";
    $configFile .= " */\n";
    $configFile .= "\n";

    // Varre todas as variaveis de configuração para gravar no arquivo.
    foreach($config as $k => $v)
    {
        // Verifica se a chave enviada pelo post existe no arquivo de configuração
        //  se existir, substitui o valor e grava a configuração no arquivo.
        if(array_key_exists($k, $_POST))
            $v = $_POST[$k];

        // Caso necessário adiciona o escape ao valor.
        $v = addslashes($v);

        // Se for apenas valores númericos, então converte para inteiro.
        if(preg_match('/^([0-9]+)$/', $v))
            $configFile .= "DEFINE('{$k}', {$v}, false);\n";
        else
            $configFile .= "DEFINE('{$k}', '{$v}', false);\n";
    }

    // Configurações finais para o painel de controle.
    $configFile .= "\n";
    $configFile .= "if(BRACP_DEVELOP_MODE)\n";
    $configFile .= "{\n";
    $configFile .= "    DEFINE('PAG_URL', 'https://sandbox.pagseguro.uol.com.br', false);\n";
    $configFile .= "    DEFINE('PAG_WS_URL', 'https://ws.sandbox.uol.com.br', false);\n";
    $configFile .= "    DEFINE('PAG_STC_URL', 'https://stc.sandbox.pagseguro.uol.com.br', false);\n";
    $configFile .= "}\n";
    $configFile .= "else\n";
    $configFile .= "{\n";
    $configFile .= "    DEFINE('PAG_URL', 'https://pagseguro.uol.com.br', false);\n";
    $configFile .= "    DEFINE('PAG_WS_URL', 'https://ws.pagseguro.uol.com.br', false);\n";
    $configFile .= "    DEFINE('PAG_STC_URL', 'https://stc.pagseguro.uol.com.br', false);\n";
    $configFile .= "}\n";
    $configFile .= "\n";

    // Finaliza o arquivo e escreve os dados no arquivo de configuração.
    file_put_contents('config.php', $configFile);

    header('Refresh: 3');
    header('Content-Type: text/php');
    header('Content-Disposition: attachment; filename="config.php"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize('config.php'));
    readfile('config.php');

    exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>brACP - Instalação do Painel de Controle</title>

        <!--
            2016-04-14, CHLFZ: Problemas de CHARSET identificado por pelo Sir Will e postado no fórum.
                                -> @issue 7
        -->
        <meta charset="UTF-8">

        <link rel="stylesheet" type="text/css" href="themes/default/css/install.css"/>
        <link rel="stylesheet" type="text/css" href="themes/default/css/button.css"/>

        <script src="js/jquery-2.1.4.js"></script>
        <script>
            var defaultConfig = <?php echo json_encode($config); ?>;

            function defaultConfigLoad()
            {
                $.each(defaultConfig, function(index, value) {
                    $('#' + index).val(value);
                });
            }

            +function($)
            {
                $(document).on('click', 'input[type="reset"]', function(e) {
                    e.preventDefault();

                    $(this).closest('form').each(function() {
                        this.reset();
                    });

                    defaultConfigLoad();
                });

                $(document).ready(function() {
                    defaultConfigLoad();
                });
            } (window.jQuery);
        </script>
    </head>
    <body>
        <div class="bracp-install-body">
            <h1>Instalação do brACP - Painel de Controle</h1>

            <?php if(version_compare(PHP_VERSION, '5.4.0', '<')) { ?>
                <div class="bracp-install-error">
                    Sua versão de instalação do PHP é inferior a requerida para execução do painel de controle.<br>
                    A Versão minima para execução é a 5.4.0 ou superior.<br>
                    <br>
                    <strong><i>Hey, psiu! Talvez isso te ajude:
                    <a href="http://php.net/" target="_blank">PHP.net</a></i></strong>
                </div>
            <?php } else if(!file_exists('vendor') || !is_dir('vendor') || file_exists('vendor') && !file_exists('composer.lock')) { ?>

                <div class="bracp-install-error">
                    Os arquivos do composer não foram baixados!<br>
                    Verifique sua instalação e tente novamente.<br>
                    <br>
                    <strong><i>Hey, psiu! Talvez isso te ajude:
                    <a href="http://getcomposer.org/" target="_blank">Composer</a>.</i></strong>
                </div>

            <?php } else if(!$writeable) { ?>

                <div class="bracp-install-error">
                    O Programa de instalação não será capaz criar o arquivo de instalação se você enviar os dados.<br>
                    Verifique as permissões do diretório e tente novamente.<br>
                    <br>
                    Talvez isso ajude!<br>
                    <strong>chmod -R 0777</strong>
                </div>

            <?php } else { ?>

                <div class="bracp-install-info">
                    <strong>Dica:</strong> Se você tiver dúvidas durante a instalação, você pode consultar
                    as váriaveis de configuração clicando <a href="https://github.com/carloshenrq/brACP#configura%C3%A7%C3%B5es" target="_blank">aqui</a>.
                </div>

                <div class="bracp-install-info">
                    <strong>Guarde as informações!</strong> Ao final da instalação você receberá uma cópia do arquivo de instalação do painel de controle!
                </div>

                <div class="bracp-install-warning">
                    Se você desejar alterar as configurações do painel de controle no futuro, verifique o arquivo <strong><i>config.php</i></strong>
                </div>


                <br>

                <ul class="bracp-install-tabs">
                    <li><label for="conf-general" class="btn">Configurações Gerais</label></li>
                    <li><label for="conf-mysql" class="btn">MySQL</label></li>
                    <li><label for="conf-mail" class="btn">Servidor de E-mail (SMTP)</label></li>
                    <li><label for="conf-captcha" class="btn">reCAPTCHA</label></li>
                    <li><label for="conf-donation" class="btn">PagSeguro</label></li>
                    <li><label for="conf-others" class="btn">Outros</label></li>
                </ul>

                <form method="post" enctype="application/x-www-form-urlencoded">
                    <input name="_conf-tab" id="conf-general" class="bracp-install-tab-radio" type="radio" checked/>
                    <div class="bracp-install-tab-div">
                        <h1>Configurações Gerais</h1>

                        <br>
                        <div class="bracp-install-label-data">
                            <label>
                                Fuso Horário:<br>
                                <select id="BRACP_DEFAULT_TIMEZONE" name="BRACP_DEFAULT_TIMEZONE">
                                    <?php foreach(timezone_identifiers_list() as $timeZone) { ?>
                                        <option value="<?php echo $timeZone; ?>"><?php echo $timeZone; ?></option>
                                    <?php } ?>
                                </select>
                            </label>

                            <label>
                                URL de Acesso:<br>
                                <input id="BRACP_URL" name="BRACP_URL" type="text" value="" size="50"/>
                            </label>
                            <label>
                                URL de Instalação:<br>
                                <input id="BRACP_DIR_INSTALL_URL" name="BRACP_DIR_INSTALL_URL" type="text" value="" size="20"/>
                            </label>
                        </div>
                        <br>
                        <div class="bracp-install-label-data">
                            <label>
                                Caminho arquivos de template:<br>
                                <input id="BRACP_TEMPLATE_DIR" name="BRACP_TEMPLATE_DIR" type="text" value="" size="118"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Caminho arquivos de entidades:<br>
                                <input id="BRACP_ENTITY_DIR" name="BRACP_ENTITY_DIR" type="text" value="" size="118"/>
                            </label>
                        </div>
                        <br>
                        <br>
                        <div class="bracp-install-label-data">
                            <label>
                                MD5 em Senhas:<br>
                                <select id="BRACP_MD5_PASSWORD_HASH" name="BRACP_MD5_PASSWORD_HASH">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Permitir alterar e-mail:<br>
                                <select id="BRACP_ALLOW_CHANGE_MAIL" name="BRACP_ALLOW_CHANGE_MAIL">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Delay para mudar e-mail (minutos):<br>
                                <input id="BRACP_CHANGE_MAIL_DELAY" name="BRACP_CHANGE_MAIL_DELAY" type="text" value="" size="3"/>
                            </label>
                            <label>
                                Tema padrão:<br>
                                <select id="BRACP_DEFAULT_THEME" name="BRACP_DEFAULT_THEME"><?php
                                    foreach($themes as $theme) { ?>
                                        <option value="<?php echo $theme->folder; ?>">
                                            <?php echo $theme->name; ?> (<?php echo $theme->version; ?>)
                                        </option>
                                    <?php } ?>
                                ?></select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Permitir novas contas:<br>
                                <select id="BRACP_ALLOW_CREATE_ACCOUNT" name="BRACP_ALLOW_CREATE_ACCOUNT">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Confirmar novas contas:<br>
                                <select id="BRACP_CONFIRM_ACCOUNT" name="BRACP_CONFIRM_ACCOUNT">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Bloquear novas contas com e-mails duplicado:<br>
                                <select id="BRACP_MAIL_REGISTER_ONCE" name="BRACP_MAIL_REGISTER_ONCE">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Modo administrador:<br>
                                <select id="BRACP_ALLOW_ADMIN" name="BRACP_ALLOW_ADMIN">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Idioma Padrão:<br>
                                <select id="BRACP_DEFAULT_LANGUAGE" name="BRACP_DEFAULT_LANGUAGE">
                                    <?php foreach($langs as $lang) { ?>
                                        <option value="<?php echo $lang; ?>">
                                            <?php echo $lang; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Nivel administrador:<br>
                                <input id="BRACP_ALLOW_ADMIN_GMLEVEL" name="BRACP_ALLOW_ADMIN_GMLEVEL" type="text" value="" size="3"/>
                            </label>
                            <label>
                                Nivel para login:<br>
                                <input id="BRACP_ALLOW_LOGIN_GMLEVEL" name="BRACP_ALLOW_LOGIN_GMLEVEL" type="text" value="" size="3"/>
                            </label>
                            <label>
                                Permitir administrar trocar senha:<br>
                                <select id="BRACP_ALLOW_ADMIN_CHANGE_PASSWORD" name="BRACP_ALLOW_ADMIN_CHANGE_PASSWORD">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Exibir rankings:<br>
                                <select id="BRACP_ALLOW_RANKING" name="BRACP_ALLOW_RANKING">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Exibir online/offline nos rankings:<br>
                                <select id="BRACP_ALLOW_SHOW_CHAR_STATUS" name="BRACP_ALLOW_SHOW_CHAR_STATUS">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Permite ranking zeny:<br>
                                <select id="BRACP_ALLOW_RANKING_ZENY" name="BRACP_ALLOW_RANKING_ZENY">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Exibir zenys no ranking:<br>
                                <select id="BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY" name="BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Modo desenvolvedor:<br>
                                <select id="BRACP_DEVELOP_MODE" name="BRACP_DEVELOP_MODE">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Modo manutenção:<br>
                                <select id="BRACP_MAINTENCE" name="BRACP_MAINTENCE">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <input name="_conf-tab" id="conf-mysql" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>MySQL</h1>
                        <div class="bracp-install-warning">
                            Essas configurações são de grande importância para a execução correta do painel de controle.<br>
                            Tome cuidado para não configurar de forma incorreta.
                        </div>
                        <br>
                        <div class="bracp-install-label-data">
                            <label>
                                Drive de Conexão:<br>
                                <select id="BRACP_SQL_DRIVER" name="BRACP_SQL_DRIVER">
                                    <option value="ibm_db2">DB2</option>
                                    <option value="pdo_sqlsrv">SQL-Server</option>
                                    <option value="pdo_mysql">MySQL</option>
                                    <option value="pdo_pgsql">PostgreSQL</option>
                                    <option value="pdo_sqlite">SQLite</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Servidor:<br>
                                <input id="BRACP_SQL_HOST" name="BRACP_SQL_HOST" type="text" value="" size="40"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Usuário:<br>
                                <input id="BRACP_SQL_USER" name="BRACP_SQL_USER" type="text" value="" size="20"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Senha:<br>
                                <input id="BRACP_SQL_PASS" name="BRACP_SQL_PASS" type="text" value="" size="20"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Banco:<br>
                                <input id="BRACP_SQL_DBNAME" name="BRACP_SQL_DBNAME" type="text" value="" size="20"/>
                            </label>
                        </div>
                    </div>

                    <input name="_conf-tab" id="conf-mail" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>Servidor de E-mail (SMTP)</h1>
                        <div class="bracp-install-warning">
                            Essas configurações são de grande importância para a execução correta do painel de controle.<br>
                            Tome cuidado para não configurar de forma incorreta.
                        </div>
                        <br>
                        <div class="bracp-install-label-data">
                            <label>
                                Permite o envio de e-mails:<br>
                                <select id="BRACP_ALLOW_MAIL_SEND" name="BRACP_ALLOW_MAIL_SEND">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Servidor:<br>
                                <input id="BRACP_MAIL_HOST" name="BRACP_MAIL_HOST" type="text" value="" size="40"/>
                            </label>
                            <label>
                                Porta:<br>
                                <input id="BRACP_MAIL_PORT" name="BRACP_MAIL_PORT" type="text" value="" size="3"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Usuário:<br>
                                <input id="BRACP_MAIL_USER" name="BRACP_MAIL_USER" type="text" value="" size="20"/>
                            </label>
                            <label>
                                Senha:<br>
                                <input id="BRACP_MAIL_PASS" name="BRACP_MAIL_PASS" type="text" value="" size="20"/>
                            </label>
                            <label>
                                Remetente e-mail:<br>
                                <input id="BRACP_MAIL_FROM" name="BRACP_MAIL_FROM" type="text" value="" size="30"/>
                            </label>
                            <label>
                                Remetente:<br>
                                <input id="BRACP_MAIL_FROM_NAME" name="BRACP_MAIL_FROM_NAME" type="text" value="" size="25"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Notificar alterações de senha:<br>
                                <select id="BRACP_NOTIFY_CHANGE_PASSWORD" name="BRACP_NOTIFY_CHANGE_PASSWORD">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Notificar alterações de e-mail:<br>
                                <select id="BRACP_NOTIFY_CHANGE_MAIL" name="BRACP_NOTIFY_CHANGE_MAIL">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Permitir recuperação de contas:<br>
                                <select id="BRACP_ALLOW_RECOVER" name="BRACP_ALLOW_RECOVER">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Recuperar contas por código:<br>
                                <select id="BRACP_RECOVER_BY_CODE" name="BRACP_RECOVER_BY_CODE">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Código de recuperação expira em (minutos):<br>
                                <input id="BRACP_RECOVER_CODE_EXPIRE" name="BRACP_RECOVER_CODE_EXPIRE" type="text" value="" size="3"/>
                            </label>
                            <label>
                                Tamanho da senha recuperada:<br>
                                <input id="BRACP_RECOVER_STRING_LENGTH" name="BRACP_RECOVER_STRING_LENGTH" type="text" value="" size="3"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Cadeia de caracteres para gerar senha recuperada:<br>
                                <input id="BRACP_RECOVER_RANDOM_STRING" name="BRACP_RECOVER_RANDOM_STRING" type="text" value="" size="118"/>
                            </label>
                        </div>
                    </div>

                    <input name="_conf-tab" id="conf-captcha" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>reCAPTCHA</h1>
                        <div class="bracp-install-info">
                            O <strong>reCAPTCHA</strong> ajuda você a se proteger de requisições maliciosas, spans e boots.<br>
                            <br>
                            Para mais informações sobre o <strong>reCAPTCHA</strong>:
                                <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">https://www.google.com/recaptcha/intro/index.html</a>
                        </div>
                        <div class="bracp-install-warning">
                            Esta é uma configuração opcional. Você poderá configura-la mais tarde.
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Permitir uso do reCAPTCHA:<br>
                                <select id="BRACP_RECAPTCHA_ENABLED" name="BRACP_RECAPTCHA_ENABLED">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Chave pública:<br>
                                <input id="BRACP_RECAPTCHA_PUBLIC_KEY" name="BRACP_RECAPTCHA_PUBLIC_KEY" type="text" value="" size="118"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Chave privada:<br>
                                <input id="BRACP_RECAPTCHA_PRIVATE_KEY" name="BRACP_RECAPTCHA_PRIVATE_KEY" type="text" value="" size="118"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Servidor para testar captcha:<br>
                                <input id="BRACP_RECAPTCHA_PRIVATE_URL" name="BRACP_RECAPTCHA_PRIVATE_URL" type="text" value="" size="118"/>
                            </label>
                        </div>
                    </div>

                    <input name="_conf-tab" id="conf-donation" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>PagSeguro</h1>
                        <div class="bracp-install-success">
                            <strong>Viva!</strong> O Painel de controle possui suporte nativo ao PagSeguro!
                        </div>
                        <div class="bracp-install-info">
                            <strong>Verifique seu token!</strong> Você deve estar atento a suas configurações do PagSeguro!<br>
                            Você deverá informar o seu endereço de e-mail e código do token para configurar corretamente o PagSeguro!<br>
                            Não se esqueça de configurar também o retorno das notificações!<br>
                            <br>
                            <a href="https://pagseguro.uol.com.br/preferencias/integracoes.jhtml" target="_blank">Criar token de segurança</a><br>
                            <a href="https://pagseguro.uol.com.br/v2/guia-de-integracao/como-comecar.html" target="_blank">Como começar</a>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Permitir uso do PagSeguro:<br>
                                <select id="PAG_INSTALL" name="PAG_INSTALL">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                E-mail:<br>
                                <input id="PAG_EMAIL" name="PAG_EMAIL" type="text" value="" size="60"/>
                            </label>
                            <label>
                                Token:<br>
                                <input id="PAG_TOKEN" name="PAG_TOKEN" type="text" value="" size="40"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Quantidade de bônus a cada R$ 1,00:<br>
                                <input id="DONATION_AMOUNT_MULTIPLY" name="DONATION_AMOUNT_MULTIPLY" type="text" value="" size="6"/>
                            </label>
                            <label>
                                Permitir cliente assumir taxa:<br>
                                <select id="DONATION_AMOUNT_USE_RATE" name="DONATION_AMOUNT_USE_RATE">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Exibir cálculo da taxa:<br>
                                <select id="DONATION_AMOUT_SHOW_RATE_CALC" name="DONATION_AMOUT_SHOW_RATE_CALC">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Exibir próximas promoções:<br>
                                <select id="DONATION_SHOW_NEXT_PROMO" name="DONATION_SHOW_NEXT_PROMO">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Tempo (em dias) para exibir as próximas promoções:<br>
                                <input id="DONATION_INTERVAL_DAYS" name="DONATION_INTERVAL_DAYS" type="text" value="" size="3"/>
                            </label>
                        </div>
                    </div>
                    <input name="_conf-tab" id="conf-others" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>Outros</h1>
                        <div class="bracp-install-info">
                            <strong>Personagens!</strong> Essas configurações permitem os jogadores gerenciar os personagens de suas contas!<br>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Permitir resetar aparência:<br>
                                <select id="BRACP_ALLOW_RESET_APPEAR" name="BRACP_ALLOW_RESET_APPEAR">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Permitir resetar posição:<br>
                                <select id="BRACP_ALLOW_RESET_POSIT" name="BRACP_ALLOW_RESET_POSIT">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Permitir resetar equipamentos:<br>
                                <select id="BRACP_ALLOW_RESET_EQUIP" name="BRACP_ALLOW_RESET_EQUIP">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                        <br>                  
                        <div class="bracp-install-info">
                            <strong>Expressões regulares!</strong> As expressões regulares estão aqui para ajudar!<br>
                            Para alterar as configurações saiba bem o que está fazendo!<br>
                            <br>
                            <i>Psiu! Se você precisar de uma pequena ajudinha, <a href="http://www.w3schools.com/tags/att_input_pattern.asp" target="_blank">clique aqui</a>.</i>
                        </div>
                        <div class="bracp-install-warning">
                            <strong>Não se esqueça!</strong> Essas expressões são para HTML5 e não para PHP.
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Expressão para nome de usuário:<br>
                                <input id="BRACP_REGEXP_USERNAME" name="BRACP_REGEXP_USERNAME" type="text" value="" size="55"/>
                            </label>
                            <label>
                                Expressão para senha de usuário:<br>
                                <input id="BRACP_REGEXP_PASSWORD" name="BRACP_REGEXP_PASSWORD" type="text" value="" size="55"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
                            <label>
                                Expressão para e-mail:<br>
                                <input id="BRACP_REGEXP_EMAIL" name="BRACP_REGEXP_EMAIL" type="text" value="" size="100"/>
                            </label>
                        </div>
                    </div>

                    <div class="bracp-install-submit">
                        <input type="submit" class="btn btn-success" value="Salvar"/>
                        <input type="reset" class="btn btn-link" value="Limpar"/>
                    </div>

                    <?php foreach($themes as $theme) { ?>
                        <input type="hidden" name="themes[]" value="<?php echo base64_encode(json_encode($theme)); ?>"/>
                    <?php } ?>

                </form>

            <?php } ?>
        </div>
    </body>
</html>