{**
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
 *}
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>brACP - {block name="brACP_Title"}Welcome{/block} {if $smarty.const.BRACP_DEVELOP_MODE eq true}(DEVELOPER MODE){/if}</title>

        <!--
            2016-04-14, CHLFZ: Problemas de CHARSET identificado por pelo Sir Will e postado no fórum.
                                -> @issue 7
        -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1.0">

        <link rel="shortcut icon" href="{$smarty.const.BRACP_DIR_INSTALL_URL}fav.ico">
        <!-- Here loads all CSS files. -->
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}themes/{$session->BRACP_THEME}/css/system.css"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}themes/{$session->BRACP_THEME}/css/modal.css"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}themes/{$session->BRACP_THEME}/css/button.css"/>
        <style>
        {block name="brACP_StyleCss"}
        {/block}
        </style>

        <!-- Here loads all JAVASCRIPTS files -->
        {if $smarty.const.BRACP_DEVELOP_MODE eq true}
            <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/jquery-2.1.4.js"></script>
        {else}
            <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/jquery-2.1.4.min.js"></script>
        {/if}
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/bracp.prototype.js"></script>
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/jquery.ajax.js"></script>
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/jquery.bracp.js"></script>

        {if $smarty.const.PAG_INSTALL eq true}
            <script src="{$smarty.const.PAG_STC_URL}/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>
        {/if}

        {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
            <script src="https://www.google.com/recaptcha/api.js"></script>
        {/if}

        <script>
        {if $smarty.const.BRACP_DEVELOP_MODE eq true}
            console.info("---------------------------------------\n" +
                            "brACP - Developer Mode ON!!!!\n" +
                            "---------------------------------------\n" +
                            "BRACP_TEMPLATE_DIR: {$smarty.const.BRACP_TEMPLATE_DIR} \n" + 
                            "BRACP_DEFAULT_TIMEZONE: {$smarty.const.BRACP_DEFAULT_TIMEZONE} \n");
        {/if}

        +function($)
        {

            $(document).ready(function() {
                {if isset($recover_message) eq true or isset($register_message) eq true}
                    {if isset($recover_message) eq true}
                        $('#bracp-modal-recover').prop('checked', true);
                    {else if isset($register_message) eq true}
                        $('#bracp-modal-create').prop('checked', true);
                    {/if}
                    window.history.replaceState("", "", "{$smarty.const.BRACP_DIR_INSTALL_URL}");
                {/if}

                $('.theme-select')
                    .val("{$session->BRACP_THEME}")
                    .on('change', function() {
                        // Altera o tema padrão do painel de controle.
                        changeTheme($(this).val(), '{$smarty.const.BRACP_DIR_INSTALL_URL}theme');
                    });
            });

        } (window.jQuery);

        {block name="brACP_JavaScript"}
        {/block}
        </script>
    </head>
    <body>
    {block name="brACP_HtmlBody"}
        <div class="bracp-content">
            <div class="bracp-header">
                <input type="checkbox" id="_bracp-menu-check-0" class="bracp-menu-check"/>
                <label class="btn" for="_bracp-menu-check-0">
                    Menu
                </label>
                <div class="bracp-menu">
                    {include 'menu.tpl'}
                </div>
                <div class="bracp-header-menu">
                    {if isset($session->BRACP_ISLOGGEDIN) eq false or $session->BRACP_ISLOGGEDIN eq false}
                        <label for="bracp-modal-login" class="btn btn-success">Entrar</label>
                        {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                            <label for="bracp-modal-create" class="btn btn-info">Cadastre-se</label>
                        {/if}
                    {else}
                        <button data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body" class="btn btn-error ajax-url">Sair ({$account->getUserid()})</button>
                    {/if}
                </div>
            </div>
            <div class="bracp-body-container">
                {if $smarty.const.BRACP_DEVELOP_MODE eq true}
                    <div class="bracp-message warning">
                        <h3>Lembrete!</h3>
                        O Sistema está sendo executado em modo desenvolvimento!<br>
                        Algumas configurações podem não responder ao esperado.
                    </div>
                {/if}
                {if preg_match('/beta$/i', $smarty.const.BRACP_VERSION) eq 1}
                    <div class="bracp-message info">
                        <h3>Você está executando uma versão beta! <i>({$smarty.const.BRACP_VERSION})</i></h3>
                        A Versão do sistema que está em execução não é estavel e ainda está em fase de testes!<br>
                        Por favor, fique atento as atualizações pois muitos erros podem ser corrigidos.
                    </div>
                {/if}
                {if isset($account) eq true && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL}
                    <div class="bracp-message error">
                        <h3>Lembrete aos adminsitradores</h3>
                        Por questões de segurança:
                        <ul>
                            <li>Você não pode realizar alterações de e-mail pelo painel de controle.</li>
                            {if $smarty.const.BRACP_ALLOW_ADMIN_CHANGE_PASSWORD eq false}
                                <li>Você não pode alterar sua senha pelo item do menu.<br>
                                    Verifique o menu administrativo.</li>
                            {/if}
                        </ul>
                    </div>
                {/if}
                <div class="bracp-body">
                    {block name="brACP_Body"}
                    {/block}
                </div>
            </div>
            <div class="bracp-footer">
                {if is_null($navigator) eq false}
                    <div class="bracp-navigator {$navigator->getClass()}">
                        <div class="nav-name">{$navigator->getName()}</div>
                        <div class="nav-version">{$navigator->getVersion()}</div>
                    </div>
                {/if}
                <div class="nav-ipaddress">{$ipAddress}</div>
                <div class="nav-theme">
                    <select class="theme-select">
                    {foreach from=$themes item=theme}
                        <option value="{$theme->getFolder()}">{$theme->getName()} ({$theme->getVersion()})</option>
                    {/foreach}
                    </select>
                </div>
            </div>
        </div>
    {/block}
        <div class="modal-container">
            {if isset($session->BRACP_ISLOGGEDIN) eq false or $session->BRACP_ISLOGGEDIN eq false}
                <input id="bracp-modal-login" class="modal-check" type="checkbox"/>
                <div class="modal-login-body">{include 'account.login.ajax.tpl'}</div>

                {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                    <input id="bracp-modal-create" class="modal-check" type="checkbox"/>
                    <div class="modal-create-body">{include 'account.register.ajax.tpl'}</div>
                {/if}

                {if $smarty.const.BRACP_ALLOW_RECOVER eq true}
                    <input id="bracp-modal-recover" class="modal-check" type="checkbox"/>
                    <div class="modal-recover-body">{include 'account.recover.ajax.tpl'}</div>
                {/if}
            {else}
                <input id="bracp-modal-changepass" class="modal-check" type="checkbox"/>
                <div class="modal-changepass-body">{include 'account.change.password.ajax.tpl'}</div>

                {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL eq true}
                    <input id="bracp-modal-changemail" class="modal-check" type="checkbox"/>
                    <div class="modal-changemail-body">{include 'account.change.mail.ajax.tpl'}</div>
                {/if}
            {/if}
        </div>

        <div class="bracp-ajax-loading">
            <div class="bracp-ajax-loading-div"></div>
        </div>
    </body>
</html>