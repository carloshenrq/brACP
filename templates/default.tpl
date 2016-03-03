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
<html>
    <head>
        <title>brACP - {block name="brACP_Title"}Welcome{/block} {if $smarty.const.BRACP_DEVELOP_MODE eq true}(DEVELOPER MODE){/if}</title>

        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1.0">

        <link rel="shortcut icon" href="{$smarty.const.BRACP_DIR_INSTALL_URL}fav.ico">
        <!-- Here loads all CSS files. -->
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}css/system.css"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}css/menu.css"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}css/modal.css"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}css/button.css"/>
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
        {block name="brACP_JavaScript"}
        {/block}
        </script>
    </head>
    <body>
    {block name="brACP_HtmlBody"}
        <div class="bracp-content">
            <div class="bracp-header">
                <div class="bracp-logo ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}" data-target=".bracp-body"></div>
                <div class="bracp-menu">
                    {include 'menu.tpl'}
                </div>
            </div>
            <div class="bracp-body-container">
                {if $smarty.const.BRACP_DEVELOP_MODE eq true}
                    <div class="bracp-message-warning">
                        <h3>Lembrete!</h3>
                        O Sistema está sendo executado em modo desenvolvimento!<br>
                        <i><u>Algumas configurações podem não responder ao esperado.</u></i>
                    </div>
                {/if}
                {if isset($account) eq true && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL}
                    <div class="bracp-message-error">
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
            <div class="bracp-footer"></div>
        </div>
    {/block}
        <div class="bracp-ajax-loading">
            <div class="bracp-ajax-loading-div"></div>
        </div>
    </body>
</html>