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

        <link rel="shortcut icon" href="fav.ico">
        <!-- Here loads all CSS files. -->
        <link rel="stylesheet" type="text/css" href="{$smarty.const.BRACP_DIR_INSTALL_URL}css/system.css"/>
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
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/jquery.ajax.js"></script>
        <script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/jquery.bracp.js"></script>

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
                <div class="bracp-logo bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}"></div>
                <div class="bracp-menu">
                    <ul>
                        <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}">Principal</li>
                        {if isset($smarty.session.BRACP_ISLOGGEDIN) eq false or $smarty.session.BRACP_ISLOGGEDIN eq false}
                            <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register">Criar Conta</li>
                            <li>Minha Conta
                                <ul>
                                    <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login">Entrar</li>
                                    <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover">Recuperar</li>
                                </ul>
                            </li>
                        {else}
                            <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/loggout">Sair ({$smarty.session.BRACP_USERID})</li>
                        {/if}
                        <li>Rankings
                            <ul>
                                <li>Guerra do Emperium
                                    <ul>
                                        <li>Clãs</li>
                                        <li>Castelos
                                            <ul>
                                                <li>Geral</li>
                                                <li>Econômia</li>
                                                <li>Defesa</li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li>Personagens
                                    <ul>
                                        <li>Geral</li>
                                        <li>Econômia</li>
                                        <li>Player vs Player (PvP)</li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>Sobre
                            <ul>
                                <li>Equipe</li>
                                <li>brAthena</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="bracp-body">
                {block name="brACP_Body"}
                {/block}
            </div>
            <div class="bracp-footer"></div>
        </div>
    {/block}
    </body>
</html>