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

<h1>Minha Conta &raquo; Personagens</h1>

<p>Segue abaixo a lista dos personagens gerenciaveis para sua conta.</p>

{if count($chars) eq 0}
    <p class="bracp-message-warning">
        Você não possui personagens criados para gerênciar.
    </p>
{else}

{if isset($message['success']) eq true}
    <p class="bracp-message-success">
        {$message['success']}
    </p>
{else if isset($message['error']) eq true}
    <p class="bracp-message-error">
        {$message['error']}
    </p>
{/if}

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" autocomplete="off" method="post" target=".bracp-body">

    <table border="1" align="center" class="bracp-table">
        <caption style="text-align: right">
            <input type="submit" class="btn" value="Enviar"/>
            <input type="reset" class="btn" value="Limpar"/>
        </caption>
        <thead>
            <tr>
                <th align="right" rowspan="2">Cód.</th>
                <th align="left" rowspan="2">Nome</th>
                <th align="left" rowspan="2">Classe</th>
                <th align="right" rowspan="2">Nível</th>
                <th align="right" rowspan="2">Zeny</th>
                <th align="left" rowspan="2">Mapa</th>
                <th align="left" rowspan="2">Retorno</th>
                {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                    <th align="center" rowspan="2">Status</th>
                {/if}
                {if $actions neq 0}
                    <th colspan="3">Resetar</th>
                {/if}
            </tr>
            <tr>
                {if $actions&1 eq 1}
                    <th>Visual</th>
                {/if}
                {if $actions&2 eq 2}
                    <th>Local</th>
                {/if}
                {if $actions&4 eq 4}
                    <th>Equip</th>
                {/if}
            </tr>
        </thead>
        <tbody>
            {foreach from=$chars item=row}
                <tr>
                    <td align="right">{$row->getChar_id()}</td>
                    <td align="left">{$row->getName()}</td>
                    <td align="left">{Format::job($row->getClass())}</td>
                    <td align="right">{$row->getBase_level()}/{$row->getJob_level()}</td>
                    <td align="right">{Format::zeny($row->getZeny())}</td>
                    <td align="left">{$row->getLast_map()} ({$row->getLast_x()}, {$row->getLast_y()})</td>
                    <td align="left">{$row->getSave_map()} ({$row->getSave_x()}, {$row->getSave_y()})</td>
                    {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                        <td align="center">{Format::status($row->getOnline())}</td>
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
