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
    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}" data-target=".bracp-body"><label>
        @@MENU(HOME)
    </label></li>
    {if isset($session->BRACP_ISLOGGEDIN) eq true and $session->BRACP_ISLOGGEDIN eq true and
            $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL}
        <li style='color: red; font-weight: bold;'>
            <input id="menu-admin" type="checkbox" class="bracp-menu-item-check"/>
            <label for="menu-admin">@@MENU,ADMIN(TITLE)</label>
            <ul>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/players" data-target=".bracp-body"><label>
                    @@MENU,ADMIN(PLAYERS)
                </label></li>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/donation" data-target=".bracp-body"><label>
                    @@MENU,ADMIN(DONATION)
                </label></li>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/backup" data-target=".bracp-body"><label>
                    @@MENU,ADMIN(BACKUP)
                </label></li>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/theme" data-target=".bracp-body"><label>
                    @@MENU,ADMIN(THEMES)
                </label></li>
            </ul>
        </li>
    {/if}
    <li>
        <input id="menu-myaccount" type="checkbox" class="bracp-menu-item-check"/>
        <label for="menu-myaccount">@@MENU,MYACC(TITLE)</label>
        <ul>
        {if isset($session->BRACP_ISLOGGEDIN) eq false or $session->BRACP_ISLOGGEDIN eq false}
            <li><label for="bracp-modal-login">@@MENU,MYACC,UNAUTHENTICATED(LOGIN)</label></li>
            {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                <li><label for="bracp-modal-create">@@MENU,MYACC,UNAUTHENTICATED(CREATE)</label></li>
                {if $smarty.const.BRACP_CONFIRM_ACCOUNT eq true}
                    <li><label for="bracp-modal-create-resend">@@MENU,MYACC,UNAUTHENTICATED(CREATE_SEND)</label></li>
                {/if}
            {/if}
            {if $smarty.const.BRACP_ALLOW_RECOVER eq true}
                <li><label for="bracp-modal-recover">@@MENU,MYACC,UNAUTHENTICATED(RECOVER)</label></li>
            {/if}
        {else}
            <li><label for="bracp-modal-changepass">@@MENU,MYACC,AUTHENTICATED,CHANGE(PASS)</label></li>
            {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL eq true}
                <li><label for="bracp-modal-changemail">@@MENU,MYACC,AUTHENTICATED,CHANGE(MAIL)</label></li>
            {/if}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" data-target=".bracp-body"><label>
                @@MENU,MYACC,AUTHENTICATED(CHARS)
            </label></li>
            {if $smarty.const.PAG_INSTALL eq true}
                <li class="ajax-url no-mobile" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/pagseguro" data-target=".bracp-body"><label>
                    @@MENU,MYACC,AUTHENTICATED(DONATION, PagSeguro)
                </label></li>
            {/if}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body"><label>
                @@MENU,MYACC,AUTHENTICATED(LOGOUT, {$account->getUserid()})
            </label></li>
        {/if}
        </ul>
    </li>
    <li>
        <input id="menu-rankings" type="checkbox" class="bracp-menu-item-check"/>
        <label for="menu-rankings">@@MENU,RANKINGS(TITLE)</label>
        <ul>
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars" data-target=".bracp-body"><label>
                @@MENU,RANKINGS(CHARS)
            </label></li>
            {if $smarty.const.BRACP_ALLOW_RANKING_ZENY eq true}
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars/economy" data-target=".bracp-body"><label>
                    @@MENU,RANKINGS(ECONOMY)
                </label></li>
            {/if}
        </ul>
    </li>
</ul>
