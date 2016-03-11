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

?>
<!DOCTYPE html>
<html>
    <head>
        <title>brACP - Instalação do Painel de Controle</title>

        <link rel="stylesheet" type="text/css" href="css/install.css"/>
        <link rel="stylesheet" type="text/css" href="css/button.css"/>
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
                    <li><label for="conf-mysql" class="btn">Banco de Dados</label></li>
                    <li><label for="conf-mail" class="btn">MySQL</label></li>
                    <li><label for="conf-captcha" class="btn">reCAPTCHA</label></li>
                    <li><label for="conf-donation" class="btn">PagSeguro</label></li>
                    <li><label for="conf-others" class="btn">Outros</label></li>
                </ul>

                <form>
                    <input name="conf-tab" id="conf-general" class="bracp-install-tab-radio" type="radio" checked/>
                    <div class="bracp-install-tab-div">
                        <h1>Configurações Gerais</h1>
                    </div>

                    <input name="conf-tab" id="conf-mysql" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>MySQL</h1>
                    </div>

                    <input name="conf-tab" id="conf-mail" class="bracp-install-tab-radio" type="radio"/>
                    <div class="bracp-install-tab-div">
                        <h1>Servidor de E-mail</h1>
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