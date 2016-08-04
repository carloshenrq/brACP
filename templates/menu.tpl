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
    <li class="icon icon-home url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}">@@MENU(HOME)</li>
    <li class="icon icon-myacc sub-menu">@@MENU,MYACC(TITLE)
        <ul>
            {if isset($account) eq false}
                <li><label for="modal-login">@@MENU,MYACC,UNAUTHENTICATED(LOGIN)</label></li>
                {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                    <li><label for="modal-register">@@MENU,MYACC,UNAUTHENTICATED(CREATE)</label></li>
                {/if}
                {if $smarty.const.BRACP_ALLOW_MAIL_SEND}
                    {if $smarty.const.BRACP_CONFIRM_ACCOUNT}
                        <li><label for="modal-create-resend">@@MENU,MYACC,UNAUTHENTICATED(CREATE_SEND)</label></li>
                    {/if}
                    {if $smarty.const.BRACP_ALLOW_RECOVER}
                        <li><label for="modal-recover">@@MENU,MYACC,UNAUTHENTICATED(RECOVER)</label></li>
                    {/if}
                {/if}
            {else}

                {if $account->getGroup_id() < BRACP_ALLOW_ADMIN_GMLEVEL || BRACP_ALLOW_ADMIN_CHANGE_PASSWORD}
                    <li><label for="modal-password">@@MENU,MYACC,AUTHENTICATED,CHANGE(PASS)</label></li>
                {/if}

                {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL && $account->getGroup_id() < BRACP_ALLOW_ADMIN_GMLEVEL}
                    <li><label for="modal-mail">@@MENU,MYACC,AUTHENTICATED,CHANGE(MAIL)</label></li>
                {/if}

                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars"><label>@@MENU,MYACC,AUTHENTICATED(CHARS)</label></li>

                <li class="url-link" data-href="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout"><label>@@MENU,MYACC,AUTHENTICATED(LOGOUT, {$userid})</label></li>
            {/if}
        </ul>
    </li>
    {if $smarty.const.BRACP_ALLOW_RANKING}
        {* @TODO *}
    {/if}
</ul>
