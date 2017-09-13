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
    <li>Perfil</li>
    <li>Registro de Atividades</li>
{/block}
{block name="brACP_Container"}

    <table border=1>
        <caption>Últimas <strong>{count($logs)}</strong> atividades para o seu perfil.</caption>
        <thead>
            <tr>
                <th align="left">Endereço</th>
                <th align="left">Mensagem</th>
                <th align="left">Data</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$logs item=log}
                <tr>
                    <td align="left">{$log->address}</td>
                    <td align="left">{$log->message}</td>
                    <td align="left">{utf8_encode($formatter->date($log->date))}</td>
                </tr>
            {/foreach}
        </tbody>
        <tfooter>
            <tr>
                <td colspan="3" align="left"><em>* A Consulta acima está limitada nas 200 últimas atividades de seu perfil.</em></td>
            </tr>
        </tfooter>
    </table>

{/block}
