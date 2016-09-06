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

<div ng-controller="vending.list" ng-init='init({$merchants|json_encode})' style='width: 100%;'>

    <h1>@@VENDING(TITLE)</h1>

    {if $smarty.const.BRACP_CACHE eq true}
        <div class="message warning">
            @@WARNING(CACHE_ON)
        </div>
    {/if}

    <p>@@VENDING(MESSAGE)</p>

    {literal}

    <div ng-if="list.length == 0" class="message info icon">
        @@VENDING(NO_VENDING)
    </div>

    <div ng-if="list.length > 0" style='width: 100%;'>
        <div class="vending" ng-repeat="vending in list">
            <div class="vending-title" data-map="{{vending.map}} ({{vending.x}}, {{vending.y}})">{{vending.title}}</div>
            <div class="vending-items">
                <div class="vending-item" ng-repeat="vending_item in vending.items">

                    <div class="vending-item-cell">{{vending_item.amount}}x</div>

                    <div class="vending-item-cell">
                        <div class="item-info"
                            ng-attr-data-slot="{{vending_item.item.slots > 0 ? vending_item.item.slots:undefined}}"
                            ng-attr-data-refine="{{vending_item.item.refine > 0 ? vending_item.item.refine:undefined}}"
                            style="background-image: url({{vending_item.item.icon}});">{{vending_item.item.name}}</div>
                    </div>

                    <div class="vending-item-cell">{{vending_item.price}}z</div>

                </div>
            </div>
        </div>
    </div>
    {/literal}

</div>

{/block}
