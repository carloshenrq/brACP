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

<h1>##CHARS_TITLE##</h1>

<p>##CHARS_MSG,0##</p>

{if count($chars) eq 0}
    <p class="bracp-message warning">
        ##CHARS_ERR,NO_CHARS##
    </p>
{else}

{if isset($message['success']) eq true}
    <p class="bracp-message success">
        {$message['success']}
    </p>
{else if isset($message['error']) eq true}
    <p class="bracp-message error">
        {$message['error']}
    </p>
{/if}

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" autocomplete="off" method="post" target=".bracp-body">

    <table border="1" align="center" class="table">
        <caption style="text-align: right">
            <input type="submit" class="btn" value="##CHARS_BUTTONS,SUBMIT##"/>
            <input type="reset" class="btn" value="##CHARS_BUTTONS,RESET##"/>
        </caption>
        <thead>
            <tr>
                <th align="right" rowspan="2">##CHARS_TABLE,CHARID##</th>
                <th align="left" rowspan="2">##CHARS_TABLE,NAME##</th>
                <th align="left" rowspan="2">##CHARS_TABLE,CLASS##</th>
                <th align="right" rowspan="2" class="no-mobile">##CHARS_TABLE,LEVEL##</th>
                <th align="right" rowspan="2" class="no-mobile">##CHARS_TABLE,ZENY##</th>
                <th align="left" rowspan="2">##CHARS_TABLE,MAP##</th>
                <th align="left" rowspan="2" class="no-mobile">##CHARS_TABLE,MAP_RETURN##</th>
                {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                    <th align="center" rowspan="2" class="no-mobile">##CHARS_TABLE,STATUS##</th>
                {/if}
                {if $actions neq 0}
                    <th colspan="3">##CHARS_TABLE,RESET##</th>
                {/if}
            </tr>
            <tr>
                {if $actions&1 eq 1}
                    <th>##CHARS_TABLE,RESET_APPEAR##</th>
                {/if}
                {if $actions&2 eq 2}
                    <th>##CHARS_TABLE,RESET_POSIT##</th>
                {/if}
                {if $actions&4 eq 4}
                    <th>##CHARS_TABLE,RESET_EQUIP##</th>
                {/if}
            </tr>
        </thead>
        <tbody>
            {foreach from=$chars item=row}
                <tr>
                    <td align="right">{$row->getChar_id()}</td>
                    <td align="left">{$row->getName()}</td>
                    <td align="left">{Format::job($row->getClass())}</td>
                    <td align="right" class="no-mobile">{$row->getBase_level()}/{$row->getJob_level()}</td>
                    <td align="right" class="no-mobile">{Format::zeny($row->getZeny())}</td>
                    <td align="left">{$row->getLast_map()} ({$row->getLast_x()}, {$row->getLast_y()})</td>
                    <td align="left" class="no-mobile">{$row->getSave_map()} ({$row->getSave_x()}, {$row->getSave_y()})</td>
                    {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                        <td align="center" class="no-mobile">{Format::status($row->getOnline())}</td>
                    {/if}

                    {if $actions&1 eq 1}
                        <td align="center"><input type="checkbox" name="appear[]" value="{$row->getChar_id()}"/></td>
                    {/if}
                    {if $actions&2 eq 2}
                        <td align="center"><input type="checkbox" name="posit[]" value="{$row->getChar_id()}"/></td>
                    {/if}
                    {if $actions&4 eq 4}
                        <td align="center"><input type="checkbox" name="equip[]" value="{$row->getChar_id()}"/></td>
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>
</form>
{/if}
