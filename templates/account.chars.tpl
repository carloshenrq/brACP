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

    {if $smarty.const.BRACP_CACHE eq true}
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

    <div class="char-info" ng-repeat="char in chars">
        {literal}
        <div class="char-status-info" ng-class="(char.online ? 'status-online' : 'status-offline')">
            {{(char.online ? '@@STATUS(1)' : '@@STATUS(0)')}}
        </div>
        {/literal}

        <div class="char-graphic" style="background-image: url({$smarty.const.BRACP_DIR_INSTALL_URL}data/jobs/images/{$account->getSex()}/{literal}{{char.classId}}.gif);"
            data-name="{{char.name}} ({{char.base_level}}/{{char.job_level}})"
            data-job="{{char.class}}">
        </div>

        <div class="char-data">
            <div class="char-data-cell">
                <div class="char-data-info">
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Nome</div>
                        <div>{{char.name}}</div>
                    </div>
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Classe</div>
                        <div>{{char.class}}</div>
                    </div>
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Grupo</div>
                        <div>{{char.party}}</div>
                    </div>
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Clã</div>
                        <div>{{char.guild}}</div>
                    </div>
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Level</div>
                        <div>{{char.base_level}}/{{char.job_level}}</div>
                    </div>
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Mapa</div>
                        <div>{{char.last_map}} ({{char.last_x}}, {{char.last_y}})</div>
                    </div>
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Retorno</div>
                        <div>{{char.save_map}} ({{char.save_x}}, {{char.save_y}})</div>
                    </div>
                    <div class="char-data-row">
                        <div class="char-data-cell-header">Zeny</div>
                        <div>{{char.zeny}}z</div>
                    </div>
                </div>
            </div>

            <div class="char-status-canvas">
                <canvas height="200" width="200" class="char-stats" data-stats="[{{char.stats.str}}, {{char.stats.agi}}, {{char.stats.vit}}, {{char.stats.int}}, {{char.stats.dex}}, {{char.stats.luk}}]" data-translator="Atributo"></div>
            </div>
        </div>
        {/literal}
    </div>
</div>
{/block}
