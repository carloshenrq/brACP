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

<h1>@@ERRORS,ACCESS(TITLE)</h1>

<p class="bracp-message error">
    {if $smarty.const.BRACP_MAINTENCE eq true}
        @@ERRORS(MAINTENCE)
    {else}
        @@ERRORS,ACCESS(DENIED)
        {if $smarty.const.BRACP_DEVELOP_MODE eq true and isset($exception) eq true}
            <br><br>
            <strong>{$exception->getMessage()}</strong>
        {/if}
    {/if}
</p>

