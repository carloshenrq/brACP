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

        <!--
            2016-04-14, CHLFZ: Problemas de CHARSET identificado por pelo Sir Will e postado no fórum.
                                -> @issue 7
        -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1.0">

        <link rel="shortcut icon" href="{$smarty.const.BRACP_DIR_INSTALL_URL}fav.ico">
        <!-- Here loads all CSS files. -->
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/css/?file=default"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/css/?file=message"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/css/?file=button"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/css/?file=modal"/>
        <style>
        {block name="brACP_StyleCss"}
        {/block}
        </style>

        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/js/?file=Chart.bundle"></script>
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/js/?file=jquery-2.1.4"></script>
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/js/?file=angular"></script>

        {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
            <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/js/?file=angular-recaptcha"></script>
            <script src="https://www.google.com/recaptcha/api.js?onload=vcRecaptchaApiLoaded&render=explicit" async defer></script>
            <script>var vRecaptchaIsLoaded = true;</script>
        {else}
            <script>var vRecaptchaIsLoaded = false;</script>
        {/if}

        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/js/?file=jquery.bracp"></script>
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/js/?file=angular-datetime"></script>
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}asset/js/?file=bracp.angular"></script>

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
    <body ng-app="brACP">
    {block name="brACP_HtmlBody"}
        <div class="content">
            <div class="header">
                <div class="menu-top access">

                    <div class="server-status" ng-controller="serverStatus" ng-init="statusInit('SRV_{$serverStatus->index}', {(($serverStatus->login) ? 'true':'false')}, {(($serverStatus->char) ? 'true':'false')}, {(($serverStatus->map) ? 'true':'false')});">

                        <div ng-if="state == 0">
                            {if $smarty.const.BRACP_SRV_COUNT > 1}
                                <label>
                                    {translate}@SERVER.STATUS_SERVER@{/translate}
                                    <select ng-model="$parent.BRACP_SRV_SELECTED" ng-change="serverChange()">
                                        {for $i=0 to {($smarty.const.BRACP_SRV_COUNT-1)}}
                                            {assign var="CNST_SRV" value="BRACP_SRV_{$i}_NAME"}
                                            <option value="SRV_{$i}">{$smarty.const.$CNST_SRV}</option>
                                        {/for}
                                    </select>
                                </label>
                            {/if}

                            <label>
                                {translate}@SERVER.STATUS_TEXT@{/translate}:

                                    {literal}<span class="info-status online" ng-show="BRACP_SRV_LOGIN && BRACP_SRV_CHAR && BRACP_SRV_MAP">{/literal}{translate}@SERVER.STATUS_STATE_1@{/translate}</span>
                                    {literal}<span class="info-status offline" ng-show="!(BRACP_SRV_LOGIN && BRACP_SRV_CHAR && BRACP_SRV_MAP)">{/literal}{translate}@SERVER.STATUS_STATE_0@{/translate}</span>

                            </label>

                            <label>
                                {translate}@SERVER.STATUS_PLAYER@{/translate}

                                {literal}
                                    <span class="info-status">{{$parent.BRACP_SRV_PLAYERCOUNT}}</span>
                                {/literal}
                            </label>
                        </div>

                        <div ng-if="state == 1">
                            <i>{translate}@SERVER_STATUS_LOADING@{/translate}</i>
                        </div>

                    </div>

                    <div class="user-access">
                        {if isset($account) eq false}
                            <label for="modal-login" class="button small link">{translate}@MENU_MYACC_UNAUTHENTICATED_LOGIN@{/translate}</label>
                            {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                                <label for="modal-register" class="button small success">{translate}@MENU_MYACC_UNAUTHENTICATED_CREATE@{/translate}</label>
                            {/if}
                        {else}
                            <div class="url-link button small error" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout/">{translate}@MENU_MYACC_AUTHENTICATED_LOGOUT@{/translate}</div>
                        {/if}
                    </div>

                </div>

                <div class="menu-top logo url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}">
                </div>

                <div class="menu-top link">
                    {include 'menu.tpl'}
                </div>

            </div>

            <div class="body">
                {block name="brACP_Body"}
                    Layout em construção.
                {/block}
            </div>

            <div class="footer">
                <label>
                    {translate}@FOOTER_LANGUAGE@{/translate}:
                    <select id="BRACP_LANG_SELECTED">
                        {foreach from=$langs item=lang}
                            <option>{$lang}</option>
                        {/foreach}
                    </select>
                </label>

                {if $smarty.const.BRACP_ALLOW_CHOOSE_THEME}
                    <label>
                        {translate}@FOOTER_THEME@{/translate}:
                        <select id="BRACP_THEME_SELECTED">
                            {foreach from=$themes item=theme}
                                <option value="{$theme->getFolder()}">{$theme->getName()} ({$theme->getVersion()})</option>
                            {/foreach}
                        </select>
                    </label>
                {/if}

                <label>
                    {translate}@FOOTER_ADDRESS@{/translate}:
                    <span>{$ipAddress}</span>
                </label>

                <label>
                    <span class="navigator {$navigator->getClass()}">{$navigator->getName()}</span>
                </label>
            </div>

            <div class="modal-container">

                {if !isset($session->BRACP_ISLOGGEDIN) or !$session->BRACP_ISLOGGEDIN}

                    {include 'account.login.tpl'}

                    {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT}
                        {include 'account.register.tpl'}
                    {/if}

                    {if $smarty.const.BRACP_ALLOW_MAIL_SEND}
                        {if $smarty.const.BRACP_CONFIRM_ACCOUNT}
                            {include 'account.register.resend.tpl'}
                        {/if}

                        {if $smarty.const.BRACP_ALLOW_RECOVER}
                            {include 'account.recover.tpl'}
                        {/if}
                    {/if}

                {else}

                    {include 'account.password.tpl'}

                    {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL}
                        {include 'account.email.tpl'}
                    {/if}

                {/if}

            </div>

        </div>
    {/block}
        <input type="hidden" id="_BRACP_URL" value="{$smarty.const.BRACP_DIR_INSTALL_URL}"/>
    </body>
</html>