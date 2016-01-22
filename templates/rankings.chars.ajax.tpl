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

<h1>Rankings &raquo; Personagens &raquo; Geral</h1>

{if count($chars) eq 0}
    <p class="bracp-message-warning">
        Não existem personagens para este ranking.
    </p>
{else}
    <table border="1" align="center" class="bracp-table">
        <caption><strong>Top 30 jogadores do servidor</strong></caption>
        <thead>
            <tr class="tiny">
                <th align="right" rowspan="2">Pos.</th>
                <th rowspan="2">Nome</th>
                <th rowspan="2">Classe</th>
                <th align="center" colspan="2">Nível</th>
                <th rowspan="2" align="center">Status</th>
            </tr>
            <tr class="tiny">
                <th align="right">Base</th>
                <th align="right">Classe</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$chars key=i item=char}
                <tr>
                    <td align="right">{($i+1)}.º</td>
                    <td align="left">{$char->getName()}</td>
                    <td align="left">{$char->getClass()}</td>
                    <td align="right">{$char->getBase_level()}</td>
                    <td align="right">{$char->getJob_level()}</td>
                    <td align="center">{if $char->getOnline() eq true}<span style="color: green">Online</span>{else}<span style="color: red">Offline</span>{/if}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}