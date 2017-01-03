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
<ul>
    <li class="icon icon-home url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}home/index/">{translate}@MENU_HOME@{/translate}</li>

    <li class="icon icon-myacc sub-menu">{translate}@MENU_MYACC_TITLE@{/translate}
        <ul>
            {if isset($account) eq false}
                <li><label for="modal-login">{translate}@MENU_MYACC_UNAUTHENTICATED_LOGIN@{/translate}</label></li>
                {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                    <li><label for="modal-register">{translate}@MENU_MYACC_UNAUTHENTICATED_CREATE@{/translate}</label></li>
                {/if}
                {if $smarty.const.BRACP_ALLOW_MAIL_SEND}
                    {if $smarty.const.BRACP_CONFIRM_ACCOUNT}
                        <li><label for="modal-create-resend">{translate}@MENU_MYACC_UNAUTHENTICATED_CREATE.SEND@{/translate}</label></li>
                    {/if}
                    {if $smarty.const.BRACP_ALLOW_RECOVER}
                        <li><label for="modal-recover">{translate}@MENU_MYACC_UNAUTHENTICATED_RECOVER@{/translate}</label></li>
                    {/if}
                {/if}
            {else}

                {if $account->getGroup_id() < BRACP_ALLOW_ADMIN_GMLEVEL || BRACP_ALLOW_ADMIN_CHANGE_PASSWORD}
                    <li><label for="modal-password">{translate}@MENU_MYACC_AUTHENTICATED_CHANGE_PASS@{/translate}</label></li>
                {/if}

                {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL && $account->getGroup_id() < BRACP_ALLOW_ADMIN_GMLEVEL}
                    <li><label for="modal-mail">{translate}@MENU_MYACC_AUTHENTICATED_CHANGE_MAIL@{/translate}</label></li>
                {/if}

                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars/"><label>{translate}@MENU_MYACC_AUTHENTICATED_CHARS@{/translate}</label></li>

                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout/"><label>{translate p0="{$account->getUserid()}"}@MENU_MYACC_AUTHENTICATED_LOGOUT@{/translate}</label></li>
            {/if}
        </ul>
    </li>

    {if $smarty.const.BRACP_ALLOW_ADMIN && isset($account) eq true && $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL}
        <li class="icon icon-admin sub-menu">
            {translate}@MENU_ADMIN_TITLE@{/translate}

            <ul>
                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/themes/"><label>{translate}@MENU_ADMIN_THEMES@{/translate}</label></li>
                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/langs/"><label>{translate}@MENU_ADMIN_LANGS@{/translate}</label></li>

                {if $smarty.const.BRACP_ALLOW_MODS eq true}
                    <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/mods/"><label>{translate}@MENU_ADMIN_MODS@{/translate}</label></li>
                {/if}

                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/backup/"><label>{translate}@MENU_ADMIN_BACKUP@{/translate}</label></li>
                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/cache/"><label>{translate}@MENU_ADMIN_CACHE@{/translate}</label></li>
                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/players/"><label>{translate}@MENU_ADMIN_ACCOUNTS@{/translate}</label></li>
                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/webservice/"><label>{translate}@MENU_ADMIN_WEBSERVICE@{/translate}</label></li>
                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/firewall/"><label>{translate}@MENU_ADMIN_FIREWALL@{/translate}</label></li>
            </ul>

        </li>
    {/if}

    {if $smarty.const.BRACP_ALLOW_VENDING}
        <li class="url-link icon icon-vending" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}vending/index/">{translate}@MENU_MERCHANTS@{/translate}</li>
    {/if}

    {if $smarty.const.BRACP_ALLOW_RANKING}
        {* @TODO *}
    {/if}
</ul>
