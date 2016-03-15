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
    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}" data-target=".bracp-body">Principal</li>
    {if isset($smarty.session.BRACP_ISLOGGEDIN) eq false or $smarty.session.BRACP_ISLOGGEDIN eq false}
        {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register" data-target=".bracp-body">Criar Conta</li>
        {/if}
        <li>Minha Conta
            <ul data-back="Minha Conta">
                {if $smarty.const.BRACP_ALLOW_RECOVER eq true}
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover" data-target=".bracp-body">Recuperar</li>
                {/if}
            </ul>
        </li>
    {else}
        {if $smarty.const.BRACP_ALLOW_ADMIN eq true and $account->getGroup_id() gte $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
            <li>
                Administração
                <ul data-back="Administração">
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/donations" data-target=".bracp-body">Doações</li>
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/manage/account" data-target=".bracp-body">Gerênciar Contas</li>
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/config" data-target=".bracp-body">Configurações</li>
                </ul>
            </li>
        {/if}
        <li>Minha Conta
            <ul data-back="Minha Conta">
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/password" data-target=".bracp-body">Alterar Senha</li>
                {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL eq true}
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/mail" data-target=".bracp-body">Alterar Email</li>
                {/if}
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" data-target=".bracp-body">Personagens</li>
                {if $smarty.const.PAG_INSTALL eq true}
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations" data-target=".bracp-body">Doações</li>
                {/if}
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body">Sair ({$account->getUserid()})</li>
            </ul>
        </li>
    {/if}
    {if $smarty.const.BRACP_ALLOW_RANKING eq true}
    <li>Rankings
        <ul data-back="Rankings">
            <li>Personagens
                <ul data-back="Personagens">
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars" data-target=".bracp-body">Geral</li>
                    {if $smarty.const.BRACP_ALLOW_RANKING_ZENY eq true}
                        <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars/economy" data-target=".bracp-body">Economia</li>
                    {/if}
                </ul>
            </li>
        </ul>
    </li>
    {/if}
</ul>
