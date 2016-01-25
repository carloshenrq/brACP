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

<h1>Rankings &raquo; Personagens &raquo; Econômia</h1>

{if count($chars) eq 0}
    <p class="bracp-message-warning">
        Não existem personagens para este ranking.
    </p>
{else}
    <table border="1" align="center" class="bracp-table">
        <caption><strong>Top 100 jogadores mais ricos</strong></caption>
        <thead>
            <tr>
                <th align="right" rowspan="2">Pos.</th>
                <th>Nome</th>
                <th>Classe</th>
                <th align="right">Nível</th>
                {if $smarty.const.BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY eq true}
                    <th align="right">Zeny</th>
                {/if}
                {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                    <th align="center">Status</th>
                {/if}
            </tr>
        </thead>
        <tbody>
            {foreach from=$chars key=i item=char}
                <tr class="{if isset($account_id) eq true and $char->getAccount_id() eq $account_id}char-myaccount{/if}">
                    <td align="right">{($i+1)}.º</td>
                    <td align="left">{$char->getName()}</td>
                    <td align="left">{Format::job($char->getClass())}</td>
                    <td align="right">{$char->getBase_level()}/{$char->getJob_level()}</td>
                    {if $smarty.const.BRACP_ALLOW_RANKING_ZENY_SHOW_ZENY eq true}
                        <td align="right">{Format::zeny($char->getZeny())}</td>
                    {/if}
                    {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                        <td align="center">{Format::status($char->getOnline())}</td>
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}