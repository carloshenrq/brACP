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

// Obtém as permissões de arquivo para notificar o usuário sobre as informações.
$writeable = is_writable(__DIR__);

// Configurações padrão.
$config = [
    'BRACP_DEFAULT_TIMEZONE'                => @date_default_timezone_get(),
    'BRACP_URL'                             => 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
    'BRACP_DIR_INSTALL_URL'                 => $_SERVER['REQUEST_URI'],
    'BRACP_TEMPLATE_DIR'                    => __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
    'BRACP_ENTITY_DIR'                      => __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'Entity',
    'BRACP_MD5_PASSWORD_HASH'               => 1,
    'BRACP_MAIL_REGISTER_ONCE'              => 1,
    'BRACP_MAIL_SHOW_LOG'                   => 1,
    'BRACP_CHANGE_MAIL_DELAY'               => 60,
    'BRACP_ALLOW_CHANGE_MAIL'               => 1,
    'BRACP_ALLOW_CREATE_ACCOUNT'            => 1,
    'BRACP_ALLOW_RECOVER'                   => 1,
    'BRACP_ALLOW_ADMIN'                     => 1,
    'BRACP_ALLOW_ADMIN_GMLEVEL'             => 99,
    'BRACP_ALLOW_LOGIN_GMLEVEL'             => 0,
    'BRACP_ALLOW_ADMIN_CHANGE_PASSWORD'     => 0,
    'BRACP_ALLOW_RANKING'                   => 1,
    'BRACP_ALLOW_SHOW_CHAR_STATUS'          => 1,
    'BRACP_ALLOW_RANKING_ZENY'              => 1,
    'BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY'    => 1,
    'BRACP_DEVELOP_MODE'                    => 0,
];

?>
<!DOCTYPE html>
<html>
    <head>
        <title>brACP - Instalação do Painel de Controle</title>

        <link rel="stylesheet" type="text/css" href="css/install.css"/>
        <link rel="stylesheet" type="text/css" href="css/button.css"/>

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

            <?php if(!$writeable) { ?>

                <div class="bracp-install-error">
                    O Programa de instalação não será capaz criar o arquivo de instalação se você enviar os dados.<br>
                    Verifique as permissões do diretório e tente novamente.<br>
                    <br>
                    Talvez isso ajude!<br>
                    <strong>chmod -R 0665</strong>
                </div>

            <?php } else { ?>

                <div class="bracp-install-info">
                    <strong>Dica:</strong> Se você tiver dúvidas durante a instalação, você pode consultar
                    as váriaveis de configuração clicando <a href="https://github.com/carloshenrq/brACP#configura%C3%A7%C3%B5es" target="_blank">aqui</a>.
                </div>

                <br>

                <ul class="bracp-install-tabs">
                    <li><label for="conf-general" class="btn">Configurações Gerais</label></li>
                    <li><label for="conf-mysql" class="btn">MySQL</label></li>
                    <li><label for="conf-mail" class="btn">Servidor de E-mail</label></li>
                    <li><label for="conf-captcha" class="btn">reCAPTCHA</label></li>
                    <li><label for="conf-donation" class="btn">PagSeguro</label></li>
                    <li><label for="conf-others" class="btn">Outros</label></li>
                </ul>

                <form>
                    <input name="conf-tab" id="conf-general" class="bracp-install-tab-radio" type="radio" checked/>
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
                                Exibir alterações de e-mail:<br>
                                <select id="BRACP_MAIL_SHOW_LOG" name="BRACP_MAIL_SHOW_LOG">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Delay para mudar e-mail (minutos):<br>
                                <input id="BRACP_CHANGE_MAIL_DELAY" name="BRACP_CHANGE_MAIL_DELAY" type="text" value="" size="3"/>
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
                                Bloquear novas contas com e-mails duplicado:<br>
                                <select id="BRACP_MAIL_REGISTER_ONCE" name="BRACP_MAIL_REGISTER_ONCE">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Modo administrador:<br>
                                <select id="BRACP_ALLOW_ADMIN" name="BRACP_ALLOW_ADMIN">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                            <label>
                                Nivel administrador:<br>
                                <input id="BRACP_ALLOW_ADMIN_GMLEVEL" name="BRACP_ALLOW_ADMIN_GMLEVEL" type="text" value="" size="3"/>
                            </label>
                        </div>
                        <div class="bracp-install-label-data">
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
                        </div>
                    </div>

                    <input name="conf-tab" id="conf-mysql" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>MySQL</h1>
                    </div>

                    <input name="conf-tab" id="conf-mail" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>Servidor de E-mail</h1>
                        <div class="bracp-install-label-data">
                            <label>
                                Permitir recuperar contas:<br>
                                <select id="BRACP_ALLOW_RECOVER" name="BRACP_ALLOW_RECOVER">
                                    <option value="0">Não</option>
                                    <option value="1">Sim</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <input name="conf-tab" id="conf-captcha" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>reCAPTCHA</h1>
                    </div>

                    <input name="conf-tab" id="conf-donation" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>PagSeguro</h1>
                    </div>

                    <input name="conf-tab" id="conf-others" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>Outros</h1>
                    </div>

                    <div class="bracp-install-submit">
                        <input type="submit" class="btn btn-success" value="Salvar"/>
                        <input type="reset" class="btn btn-link" value="Limpar"/>
                    </div>

                </form>

            <?php } ?>
        </div>
    </body>
</html>