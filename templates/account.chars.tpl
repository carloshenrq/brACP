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

{extends file="default.tpl"}
{block name="brACP_Body"}
<div ng-controller="account.chars" ng-init='init({$chars|json_encode})'>

    <h1>@@CHARS(TITLE)</h1>

    {if $smarty.const.BRACP_MEMCACHE eq true}
        <div class="message warning">
            @@WARNING(CACHE_ON)
        </div>
    {/if}

    <p>
        Abaixo, segue a lista dos personagens que você possui no jogo para você realizar algumas ações como resetar posição, equipamentos e apararência...
    </p>

    <div ng-if="chars.length == 0" class="message error">
        @@CHARS,ERROR(NO_CHAR)
    </div>

    <table class="table" ng-if="chars.length > 0">
        <thead>
            <tr>
                <th align="right">N.º</th>
                <th align="left">Nome</th>
                <th align="left">Classe</th>
                <th align="center">Grupo</th>
                <th align="center">Clã</th>
                <th align="left">Local</th>
                <th align="left">Retorno</th>
                <th align="right">Zeny</th>
                {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                    <th align="center">Status</th>
                {/if}
                <th align="center" colspan="3">Resetar</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="char in chars">
                {literal}
                <td align="right">{{char.num}}</td>
                <td align="left">{{char.name}}</td>
                <td align="left">{{char.class}}</td>
                <td align="center" ng-if="char.party == null">
                    <i>Sem grupo</i>
                </td>
                <td align="center" ng-if="char.party != null">
                    @Todo
                </td>
                <td align="center" ng-if="char.guild == null">
                    <i>Sem clã</i>
                </td>
                <td align="center" ng-if="char.guild != null">
                    @Todo
                </td>
                <td align="left">{{char.last_map}} ({{char.last_x}}, {{char.last_y}})</td>
                <td align="left">{{char.save_map}} ({{char.save_x}}, {{char.save_y}})</td>
                <td align="right">{{char.zeny}}</td>
                {/literal}
                {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                    {literal}
                        <td align="center">
                            <span class="info-status" ng-class="{'online' : char.online, 'offline' : !char.online}">
                                {{(char.online ? 'Online' : 'Offline')}}
                            </span>
                        </td>
                    {/literal}
                {/if}
                {literal}
                <td align="center">
                    <button class="button small success" ng-click="resetPosit(char.char_id)">Local</button>
                </td>
                <td align="center">
                    <button class="button small warning" ng-click="resetAppear(char.char_id)">Visual</button>
                </td>
                <td align="center">
                    <button class="button small info" ng-click="resetEquips(char.char_id)">Equips</button>
                </td>
                {/literal}
            </tr>
        </tbody>
    </table>

</div>
{/block}
