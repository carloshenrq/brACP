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
<input type="checkbox" class="modal-trigger-check" id="modal-password"/>
<div class="modal" ng-controller="account.password" ng-init="passwordInit({if $smarty.const.BRACP_ALLOW_ADMIN_CHANGE_PASSWORD}true{else}false{/if}, {$account->getGroup_id()}, {$smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL})">
    <div class="modal-title">
        @@CHANGEPASS(TITLE)
        <label for="modal-password" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>

    <div class="modal-body" ng-if="stage == 0">
        <div ng-if="$parent.error_state != 0" class="message error">
            <div ng-switch="$parent.error_state">
                <div ng-switch-when="-1">@@CHANGEPASS,ERROR(NOADMIN)</div>
                <div ng-switch-when="1">@@CHANGEPASS,ERROR(MISMATCH1)</div>
                <div ng-switch-when="2">@@CHANGEPASS,ERROR(MISMATCH2)</div>
                <div ng-switch-when="3">@@CHANGEPASS,ERROR(EQUALS)</div>
                <div ng-switch-when="4">@@ERRORS(REGEXP)</div>
                <div ng-switch-when="5">@@ERRORS(RECAPTCHA)</div>
            </div>
        </div>

        <div ng-if="success_state" class="message success" style="max-width: 380px;">
            @@CHANGEPASS(SUCCESS)
        </div>

        {if $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL && !$smarty.const.BRACP_ALLOW_ADMIN_CHANGE_PASSWORD}
            <div class="message warning" style="max-width: 380px;">
                @@CHANGEPASS,MESSAGE(ADMIN)
            </div>
            <br>
        {/if}

        <div style="max-width: 380px;">@@CHANGEPASS,MESSAGE(HEADER)</div>

        <form class="modal-form" ng-submit="submitPassword()">

            <input type="password" ng-model="user_pass" placeholder="@@CHANGEPASS,HOLDER(ACTUAL_PASSWORD)" size="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>
            <input type="password" ng-model="user_pass_new" placeholder="@@CHANGEPASS,HOLDER(NEW_PASSWORD)" size="32" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>
            <input type="password" ng-model="user_pass_conf" placeholder="@@CHANGEPASS,HOLDER(CONFIRM_PASSWORD)" size="32" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

            <input id="_submitPassword" type="submit"/>
            
            {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                <div class="recaptcha" ng-model="recaptcha_response" vc-recaptcha key="'{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}'"></div>
            {/if}
        </form>
    </div>

    <div class="modal-body" ng-if="stage == 1">

        <div class="loading-ajax">
            <div class="loading-bar loading-bar-1"></div>
            <div class="loading-bar loading-bar-2"></div>
            <div class="loading-bar loading-bar-3"></div>
            <div class="loading-bar loading-bar-4"></div>
        </div>

    </div>

    <div class="modal-body" ng-if="stage == 2">

        <div class="message error">
            @@CHANGEPASS,ERROR(NOADMIN)
        </div>

    </div>

    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitPassword">@@CHANGEPASS,BUTTONS(SUBMIT)</label>
        <label class="button error icon" for="modal-password">@@CHANGEPASS,BUTTONS(CLOSE)</label>
    </div>
</div>
