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

<input type="checkbox" class="modal-trigger-check" id="modal-donation"/>
<div class="modal" ng-controller="donation" ng-init="init('{if isset($userid)}{$userid}{/if}', '{$serverStatus->index}')">

    <div class="modal-title">
        @@DONATIONS(TITLE)
        <label for="modal-donation" class="modal-close">&times;</label>
    </div>

    <div class="modal-body" style="max-width: 580px;">
        <p>
            @@DONATIONS,MESSAGE,HEADER(0)
        </p>
        
        <p>
            @@DONATIONS,MESSAGE,HEADER(1)
        </p>

        <label class="input-checkbox">
            <input type="checkbox" ng-model="accept_terms">
            @@DONATIONS,MESSAGE(ACCEPT_TERMS)
        </label>

        <div ng-if="accept_terms">

            <p class="message info">@@DONATIONS(INFO)</p>

            <div class="loading-ajax" ng-if="$parent.state">
                <div class="loading-bar loading-bar-1"></div>
                <div class="loading-bar loading-bar-2"></div>
                <div class="loading-bar loading-bar-3"></div>
                <div class="loading-bar loading-bar-4"></div>
            </div>

            <div class="message error" ng-if="!$parent.state && $parent.error_state">
                <div ng-switch="$parent.error_state">
                    <div ng-switch-when="-1">@@DONATIONS,ERROR(DISABLED)</div>
                    <div ng-switch-when="1">@@DONATIONS,ERROR(INVALID_VALUE)</div>
                    <div ng-switch-when="2">@@DONATIONS,ERROR(INVALID_USERID)</div>
                    <div ng-switch-when="3">@@ERRORS(RECAPTCHA)</div>
                </div>
            </div>

            <div class="message success" ng-if="!$parent.state && $parent.success_state">
                @@DONATIONS(SUCCESS)
            </div>

            <form class="modal-form" ng-submit="submitDonation()" ng-if="!$parent.state">

                <span class="display-money">{literal}{{donationValue}}{/literal}</span>
                <input type="range" ng-model="donationValue" required min="{$smarty.const.BRACP_DONATION_MIN_VALUE}" max="{$smarty.const.BRACP_DONATION_MAX_VALUE}"/>

                <input type="text" ng-model="$parent.userid" placeholder="Nome de usuário (opcional)"/>
                <span class="display-notify">@@DONATIONS,WARNING(USERID)</span>

                {if $smarty.const.BRACP_SRV_COUNT > 1}
                    <select ng-model="$parent.serverIndex">
                        {for $i=0 to {($smarty.const.BRACP_SRV_COUNT-1)}}
                            {assign var="CNST_SRV" value="BRACP_SRV_{$i}_NAME"}
                            <option value="{$i}">{$smarty.const.$CNST_SRV}</option>
                        {/for}
                    </select>
                {/if}

                <input id="_submitDonation" type="submit"/>

            </form>
        </div>

    </div>

    <div class="modal-footer">
        <label class="button success icon" for="_submitDonation" ng-if="accept_terms">@@DONATIONS,BUTTONS(SUBMIT)</label>
        <label class="button error icon" for="modal-donation">@@DONATIONS,BUTTONS(CLOSE)</label>
    </div>
</div>
