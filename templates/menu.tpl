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
        Principal
    </label></li>
    <li>
        <input id="menu-myaccount" type="checkbox" class="bracp-menu-item-check"/>
        <label for="menu-myaccount">Minha Conta</label>
        <ul>
        {if isset($session->BRACP_ISLOGGEDIN) eq false or $session->BRACP_ISLOGGEDIN eq false}
            <li><label for="bracp-modal-login">Login</label></li>
            {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
                <li><label for="bracp-modal-create">Criar Conta</label></li>
            {/if}
            {if $smarty.const.BRACP_ALLOW_RECOVER eq true}
                <li><label for="bracp-modal-recover">Recuperar senha</label></li>
            {/if}
        {else}
            <li><label for="bracp-modal-changepass">Alterar Senha</label></li>
            {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL eq true}
                <li><label for="bracp-modal-changemail">Alterar Email</label></li>
            {/if}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" data-target=".bracp-body"><label>
                Personagens
            </label></li>
            {if $smarty.const.PAG_INSTALL eq true}
                <li class="ajax-url no-mobile" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations" data-target=".bracp-body"><label>
                    Doações
                </label></li>
            {/if}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body"><label>
                Sair ({$account->getUserid()})
            </label></li>
        {/if}
        </ul>
    </li>
    <li>
        <input id="menu-rankings" type="checkbox" class="bracp-menu-item-check"/>
        <label for="menu-rankings">Rankings</label>
        <ul>
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars" data-target=".bracp-body"><label>
                Personagens
            </label></li>
            {if $smarty.const.BRACP_ALLOW_RANKING_ZENY eq true}
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars/economy" data-target=".bracp-body"><label>
                    Economia
                </label></li>
            {/if}
        </ul>
    </li>
</ul>
