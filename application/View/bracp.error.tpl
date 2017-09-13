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
 *
 *}

{extends file="bracp.default.tpl"}
{block name="braCP_LocationPath"}
    <li>Conteúdo não encontrado (ERR::404)</li>
{/block}

{block name="brACP_Container"}

    <div class="message warning" style="width: 80%; margin: 0 auto;">
        <center>
            <img src="{$smarty.const.APP_URL_PATH}/asset/img/error.png"/>
            <br>
            <br>
            Pedimos desculpas mas o conteúdo que você estava procurando, não foi encontrado.
        </center>
    </div>
    <br>
    {if $smarty.const.APP_DEVELOPER_MODE eq true && isset($ex)}
        <div class="message error">
            {$ex->getMessage()}
        </div>
        <pre class="message">{$ex->getTraceAsString()}</pre>
    {/if}

{/block}
