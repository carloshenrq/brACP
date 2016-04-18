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
        {_('MENU_HOME')}
    </label></li>
    {if isset($session->BRACP_ISLOGGEDIN) eq true and $session->BRACP_ISLOGGEDIN eq true and
            $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL}
        <li style='color: red; font-weight: bold;'>
            <input id="menu-admin" type="checkbox" class="bracp-menu-item-check"/>
            <label for="menu-admin">{_('MENU_ADMIN')}</label>
            <ul>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/backup" data-target=".bracp-body"><label>
                    {_('MENU_ADMIN_BACKUP')}
                </label></li>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/theme" data-target=".bracp-body"><label>
                    {_('MENU_ADMIN_THEMES')}
                </label></li>
            </ul>
        </li>
    {/if}
    <li>
        <input id="menu-myaccount" type="checkbox" class="bracp-menu-item-check"/>
        <label for="menu-myaccount">{gettext('MENU_MYACC')}</label>
        <ul>
        {if isset($session->BRACP_ISLOGGEDIN) eq false or $session->BRACP_ISLOGGEDIN eq false}
            <li><label for="bracp-modal-login">{_('MENU_MYACC_LOGIN')}</label></li>
            {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                <li><label for="bracp-modal-create">{_('MENU_MYACC_CREATE')}</label></li>
            {/if}
            {if $smarty.const.BRACP_ALLOW_RECOVER eq true}
                <li><label for="bracp-modal-recover">{_('MENU_MYACC_RECOVER')}</label></li>
            {/if}
        {else}
            <li><label for="bracp-modal-changepass">{_('MENU_MYACC_CHANGEPASS')}</label></li>
            {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL eq true}
                <li><label for="bracp-modal-changemail">{_('MENU_MYACC_CHANGEMAIL')}</label></li>
            {/if}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" data-target=".bracp-body"><label>
                {_('MENU_MYACC_CHARS')}
            </label></li>
            {if $smarty.const.PAG_INSTALL eq true}
                <li class="ajax-url no-mobile" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations" data-target=".bracp-body"><label>
                    {_('MENU_MYACC_DONATIONS')}
                </label></li>
            {/if}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body"><label>
                {_('MENU_MYACC_LOGOUT')} ({$account->getUserid()})
            </label></li>
        {/if}
        </ul>
    </li>
    <li>
        <input id="menu-rankings" type="checkbox" class="bracp-menu-item-check"/>
        <label for="menu-rankings">{_('MENU_RANKINGS')}</label>
        <ul>
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars" data-target=".bracp-body"><label>
                {_('MENU_RANKINGS_CHARS')}
            </label></li>
            {if $smarty.const.BRACP_ALLOW_RANKING_ZENY eq true}
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars/economy" data-target=".bracp-body"><label>
                    {_('MENU_RANKING_ECONOMY')}
                </label></li>
            {/if}
        </ul>
    </li>
</ul>
