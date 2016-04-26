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
    @@STORAGE,MESSAGE(HEADER)

    <input id="item-radio-0" name="item-radio" type="radio" class="item-radio item-radio-0" checked/>
    <input id="item-radio-2" name="item-radio" type="radio" class="item-radio item-radio-2"/>
    <input id="item-radio-3" name="item-radio" type="radio" class="item-radio item-radio-3"/>
    <input id="item-radio-4" name="item-radio" type="radio" class="item-radio item-radio-4"/>
    <input id="item-radio-5" name="item-radio" type="radio" class="item-radio item-radio-5"/>
    <input id="item-radio-6" name="item-radio" type="radio" class="item-radio item-radio-6"/>
    <input id="item-radio-7" name="item-radio" type="radio" class="item-radio item-radio-7"/>
    <input id="item-radio-8" name="item-radio" type="radio" class="item-radio item-radio-8"/>
    <input id="item-radio-10" name="item-radio" type="radio" class="item-radio item-radio-10"/>
    <input id="item-radio-11" name="item-radio" type="radio" class="item-radio item-radio-11"/>
    <input id="item-radio-12" name="item-radio" type="radio" class="item-radio item-radio-12"/>
    <input id="item-radio-18" name="item-radio" type="radio" class="item-radio item-radio-18"/>

    <table border="1" align="center" class="table">
        <caption>
            <label class="btn btn-item-radio-0" for="item-radio-0">@@ITEM,TYPE(0)</label>
            <label class="btn btn-item-radio-2" for="item-radio-2">@@ITEM,TYPE(2)</label>
            <label class="btn btn-item-radio-3" for="item-radio-3">@@ITEM,TYPE(3)</label>
            <label class="btn btn-item-radio-4" for="item-radio-4">@@ITEM,TYPE(4)</label>
            <label class="btn btn-item-radio-5" for="item-radio-5">@@ITEM,TYPE(5)</label>
            <label class="btn btn-item-radio-6" for="item-radio-6">@@ITEM,TYPE(6)</label>
            <label class="btn btn-item-radio-7" for="item-radio-7">@@ITEM,TYPE(7)</label>
            <label class="btn btn-item-radio-8" for="item-radio-8">@@ITEM,TYPE(8)</label>
            <label class="btn btn-item-radio-10" for="item-radio-10">@@ITEM,TYPE(10)</label>
            <label class="btn btn-item-radio-11" for="item-radio-11">@@ITEM,TYPE(11)</label>
            <label class="btn btn-item-radio-12" for="item-radio-12">@@ITEM,TYPE(12)</label>
            <label class="btn btn-item-radio-18" for="item-radio-18">@@ITEM,TYPE(18)</label>
        </caption>
        <thead>
            <tr>
                <th align="left">Descrição do Item</th>
                <th align="right">Qtde</th>
                <th align="left">Tipo</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$storage item=row}
                <tr class="{if $row->getAttribute() > 0}error{/if} item-type item-type-{$row->getItem()->getType()}">
                    <td align="left">{Format::inventory($row)}</td>
                    <td align="right">{Format::zeny($row->getAmount())}</td>
                    <td align="left">@@ITEM,TYPE({$row->getItem()->getType()})</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}
