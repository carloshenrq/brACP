²{**
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
        @@CHARS(MESSAGE)
    </p>

    <p ng-if="resetState > 0" ng-switch="resetState" class="message success icon">
        <span ng-switch-when="1">@@CHARS,SUCCESS(POSIT)</span>
        <span ng-switch-when="2">@@CHARS,SUCCESS(APPEAR)</span>
        <span ng-switch-when="3">@@CHARS,SUCCESS(EQUIP)</span>
    </p>

    <div class="loading-ajax" ng-if="state">
        <div class="loading-bar loading-bar-1"></div>
        <div class="loading-bar loading-bar-2"></div>
        <div class="loading-bar loading-bar-3"></div>
        <div class="loading-bar loading-bar-4"></div>
    </div>

    <div ng-if="!state && chars.length == 0" class="message error">
        @@CHARS,ERROR(NO_CHAR)
    </div>

    <table class="table" ng-if="!state && chars.length > 0">
        <thead>
            <tr>
                <th align="right">No.</th>
                <th align="left">@@CHARS,TABLE(NAME)</th>
                <th align="left">@@CHARS,TABLE(CLASS)</th>
                <th align="center">@@CHARS,TABLE(PARTY)</th>
                <th align="center">@@CHARS,TABLE(GUILD)</th>
                <th align="left">@@CHARS,TABLE(POSIT_NOW)</th>
                <th align="left">@@CHARS,TABLE(POSIT_SAVE)</th>
                <th align="right">@@CHARS,TABLE(ZENY)</th>
                {if $smarty.const.BRACP_ALLOW_SHOW_CHAR_STATUS eq true}
                    <th align="center">@@CHARS,TABLE(STATUS)</th>
                {/if}
                <th align="center" colspan="{($smarty.const.BRACP_ALLOW_RESET_POSIT + $smarty.const.BRACP_ALLOW_RESET_APPEAR + $smarty.const.BRACP_ALLOW_RESET_POSIT)}">@@CHARS,TABLE(ACTION)</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="char in chars">
                {literal}
                <td align="right">{{char.num}}</td>
                <td align="left">{{char.name}}</td>
                <td align="left">{{char.class}}</td>
                <td align="center" ng-if="char.party == null">
                    <i>@@CHARS,TABLE(NO_PARTY)</i>
                </td>
                <td align="center" ng-if="char.party != null">
                    @Todo
                </td>
                <td align="center" ng-if="char.guild == null">
                    <i>@@CHARS,TABLE(NO_GUILD)</i>
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
                                {{(char.online ? '@@STATUS(1)' : '@@STATUS(0)')}}
                            </span>
                        </td>
                    {/literal}
                {/if}

                {if $smarty.const.BRACP_ALLOW_RESET_POSIT eq true}
                    <td align="center">
                        <button class="button small success" ng-click="resetPosit(char.char_id)">@@CHARS,BUTTONS(RESET_POSIT)</button>
                    </td>
                {/if}

                {if $smarty.const.BRACP_ALLOW_RESET_APPEAR eq true}
                    <td align="center">
                        <button class="button small warning" ng-click="resetAppear(char.char_id)">@@CHARS,BUTTONS(RESET_APPEAR)</button>
                    </td>
                {/if}

                {if $smarty.const.BRACP_ALLOW_RESET_EQUIP}
                    <td align="center">
                        <button class="button small info" ng-click="resetEquips(char.char_id)">@@CHARS,BUTTONS(RESET_EQUIP)</button>
                    </td>
                {/if}

            </tr>
        </tbody>
    </table>

</div>
{/block}
