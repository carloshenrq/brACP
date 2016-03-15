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

<h1>Administração &raquo; Configurações</h1>

{if isset($configs) eq false and isset($message) eq true}
    {if isset($message.success) eq true}
        <p class="bracp-message success">{$message.success}</p>
    {else}
        <p class="bracp-message error">{$message.error}</p>
    {/if}
{else}
    Segue abaixo todas as configurações do arquivo 'config.php' que podem ser alteradas.

    <p class="bracp-message error">
        <strong><u>OBS.:</u> <i>Uma vez alteradas estas configurações, a operação não poderá ser desfeita!</i></strong>
    </p>

    <p class="bracp-message warning">
        Para previnir que as configurações antigas sejam perdidas, ao salvar, o arquivo <strong>config.php</strong> atual será renomeado para <strong>config.php.bkp</strong>
    </p>

    <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/config" autocomplete="off" method="post" target=".bracp-body">
        <table border="1" align="center" class="bracp-table">
            <caption style="padding: 2px; text-align: right">
                <input class="btn" type="submit" value="Salvar"/>
                <input class="btn" type="reset" value="Limpar"/>
            </caption>
            <thead>
                <tr>
                    <th>Variavel</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$configs key=key item=value}
                    <tr>
                        <td><strong>{$key}</strong></td>
                        <td align="left"><input type="text" name="{$key}" value="{$value}" size="100"/></td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </form>
{/if}
