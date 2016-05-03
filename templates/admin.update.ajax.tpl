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

<h1>@@ADMIN,UPDATE(TITLE)</h1>

<div class="bracp-message error mobile-only">
    @@ERRORS(NO_MOBILE)
</div>

<div class="no-mobile">

    <div class="bracp-message warning">
        <h1>@@ADMIN,UPDATE,WARNING(TITLE)</h1>
        @@ADMIN,UPDATE,WARNING(MESSAGE)
    </div>

    <div class="bracp-message info">
        @@ADMIN,UPDATE(VERSION, {$smarty.const.BRACP_VERSION})
    </div>

    <br>
    <table border="1" align="center" class="table">
        <caption><strong>@@ADMIN,UPDATE,TABLE(CAPTION)</strong></caption>
        <thead>
            <tr>
                <th align="left" width="20%">@@ADMIN,UPDATE,TABLE,COLUMNS(DESCRIPTION)</th>
                <th align="right" width="20%">@@ADMIN,UPDATE,TABLE,COLUMNS(VERSION)</th>
                <th align="center" width="20%">@@ADMIN,UPDATE,TABLE,COLUMNS(DATE)</th>
                <th align="left" width="30%">@@ADMIN,UPDATE,TABLE,COLUMNS(FILE)</th>
                <th align="center" width="10%">@@ADMIN,UPDATE,TABLE,COLUMNS(ACTION)</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$updates item=row}
            <tr>
                <td align="left">{$row->version->name} {if $row->version->prerelease eq true}(@@ADMIN,UPDATE,TABLE(PRE_RELEASE)){/if}</td>
                <td align="right">{$row->version->number}</td>
                <td align="center">{$row->version->published}</td>
                <td align="left">
                    <a href="{$row->files->link}" target="_blank">
                        <strong>{$row->files->name}</strong> <i>{Format::bytes($row->files->size)}</i>
                    </a>
                </td>
                <td align="center">
                    <button class="btn btn-small btn-success ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/update/{$row->version->number}">
                        @@ADMIN,UPDATE,TABLE(INSTALL)
                    </button>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
