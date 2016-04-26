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

<h1>@@STORAGE(TITLE)</h1>

{if count($storage) eq 0}
    <div class="bracp-message warning">
        @@STORAGE,ERROR(NO_ITEMS)
    </div>
{else}

    <table border="1" align="center" class="table">
        <caption>@@STORAGE,MESSAGE(HEADER)</caption>
        <thead>
            <tr>
                <th align="left">Descrição do Item</th>
                <th align="right">Qtde</th>
                <th align="left">Tipo</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$storage item=row}
                <tr class="{if $row->getAttribute() > 0}error{/if}">
                    <td align="left">{Format::storage($row)}</td>
                    <td align="right">{Format::zeny($row->getAmount())}</td>
                    <td align="left">@@ITEM,TYPE({$row->getItem()->getType()})</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
