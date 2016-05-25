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

<script src="{$smarty.const.BRACP_DIR_INSTALL_URL}js/bracp.ranking.js"></script>

<div ng-app="ranking" ng-controller="economy" ng-init="_init()">

    <h1>@@RANKINGS,ECONOMY(TITLE)</h1>

    <div ng-if="chars.length == 0" class="message warning">
        @@RANKINGS(NO_CHARS)
    </div>

    {literal}

    <table ng-if="chars.length > 0" border="1" class="table">
        <caption>
            @@RANKINGS,ECONOMY(CAPTION, {{chars.length}})
        </caption>
        <thead>
            <tr>
                <th align="right">@@RANKINGS,TABLE(POSIT)</th>
                <th align="left">@@RANKINGS,TABLE(NAME)</th>
                <th align="left">@@RANKINGS,TABLE(CLASS)</th>
                <th align="right">@@RANKINGS,TABLE(LEVEL)</th>
                <th align="right">@@RANKINGS,TABLE(ZENY)</th>
                <th align="left">@@RANKINGS,TABLE(STATUS)</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="char in chars">
                <td align="right">{{char.pos}}.º</td>
                <td align="left">{{char.name}}</td>
                <td align="left">{{char.class}}</td>
                <td align="right">{{char.baseLevel}}/{{char.jobLevel}}</td>
                <td align="right">{{char.zeny}}</td>
                <td align="left" ng-style="{color: (char.online == 0 ? 'red' : 'green')}">{{char.status}}</td>
            </tr>
        </tbody>
    </table>

    {/literal}
<div>

{/block}
