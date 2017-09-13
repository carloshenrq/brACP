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
 *
 *}
<!DOCTYPE html>
<html>
    <head>
        {* Informações das tags meta, utf8, viewport etc... *}
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1.0">

        {* Titulo da página *}
        <title>{block name="brACP_Title"}brACP - brAthena{/block}</title>

        {* Icone e informações de estilo para o sistema *}
        <link rel="shortcut icon" type="image/x-icon" href="{$smarty.const.APP_URL_PATH}/asset/img/fav.ico"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/angularjs-datetime-picker.css"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.body.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.grid.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.input.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.label.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.button.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.message.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.modal.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.chart.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.table.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.ajax.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/app.box.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/bracp.scss"/>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css/quill.snow.css"/>

        {* Carrega os dados de javascript *}
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/js.prototype.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/quill.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/js.cookie.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/Chart.bundle.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/jquery.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/jquery.mask.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-quill.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-uri-parser.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-mask.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-storage.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-chartjs.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angularjs-datetime-picker.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-recaptcha.js"></script>
        {if $smarty.const.APP_RECAPTCHA_ENABLED eq true}
            <script src="https://www.google.com/recaptcha/api.js?onload=vcRecaptchaApiLoaded&render=explicit" async defer></script>
        {/if}
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-app-location.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-app-ajax.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-app-modal.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-app-fb.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-app-input-file.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-app-location.js"></script>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js/angular-app.js"></script>
    </head>
    <body ng-app="bracp">
        {if $smarty.const.APP_FACEBOOK_ENABLED eq true}
            <fb-api app-id="{$smarty.const.APP_FACEBOOK_APP_ID}"></fb-api>
        {/if}

        <div class="bracp-container-wrapper">
            <div class="bracp-top-wrapper">
                {if !isset($loggedUser)}
                    {include 'bracp.top.nouser.tpl'}
                {else}
                    {include 'bracp.top.user.tpl'}
                {/if}
            </div>
            <div class="bracp-middle-wrapper">
                <div class="bracp-logo"></div>
                <div class="bracp-body-wrapper">
                    <ul class="title">
                        {block name="braCP_LocationPath"}
                            <li>Principal</li>
                        {/block}
                    </ul>
                    {if count($announces) > 0}
                        <div class="message-announce">
                        {foreach from=$announces item=announce}
                            <div class="message {if $announce->type eq 'I'}info{else if $announce->type eq 'W'}warning{else}error{/if}">
                                {if empty($announce->title) eq false}
                                    <h1>{$announce->title}</h1>
                                {/if}
                                {$announce->content}
                                {if is_null($announce->endDt) eq false}
                                    <div class="announce-expire">
                                        {$formatter->date($announce->endDt->format('Y-m-d H:i:s'))}
                                    </div>
                                {else}
                                    <div class="announce-expire-fixed">
                                        Fixado
                                    </div>
                                {/if}
                            </div>
                        {/foreach}
                        </div>
                    {/if}
                    <div class="bracp-body-content">
                        {block name="brACP_Container"}
                            Hallou!!!!
                        {/block}
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="APP_LOCATION" value="{$smarty.const.APP_URL_PATH}"/>
        <input type="hidden" id="BRACP_PATTERN_PASS" value="{$smarty.const.BRACP_REGEXP_PASS}"/>
        <input type="hidden" id="BRACP_PATTERN_MAIL" value="{$smarty.const.BRACP_REGEXP_MAIL}"/>

        {**
         * Avisos de janela somente irão aparecer quando o jogador
         * estiver logado em sua conta.
         *}
        {if isset($loggedUser)}
            <div class="bracp-announce" ng-controller="announces" ng-init="init('')"></div>
        {/if}
    </body>
</html>
