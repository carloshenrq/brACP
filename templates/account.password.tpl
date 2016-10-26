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
        {translate}@CHANGEPASS_TITLE@{/translate}
        <label for="modal-password" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>

    <div class="modal-body" ng-if="stage == 0">
        <div ng-if="$parent.error_state != 0" class="message error">
            <div ng-switch="$parent.error_state">
                <div ng-switch-when="-1">{translate}@CHANGEPASS_ERROR_NOADMIN@{/translate}</div>
                <div ng-switch-when="1">{translate}@CHANGEPASS_ERROR_MISMATCH1@{/translate}</div>
                <div ng-switch-when="2">{translate}@CHANGEPASS_ERROR_MISMATCH2@{/translate}</div>
                <div ng-switch-when="3">{translate}@CHANGEPASS_ERROR_EQUALS@{/translate}</div>
                <div ng-switch-when="4">{translate}@ERRORS_REGEXP@{/translate}</div>
                <div ng-switch-when="5">{translate}@ERRORS_RECAPTCHA@{/translate}</div>
            </div>
        </div>

        <div ng-if="success_state" class="message success" style="max-width: 380px;">
            {translate}@CHANGEPASS_SUCCESS@{/translate}
        </div>

        {if $account->getGroup_id() >= BRACP_ALLOW_ADMIN_GMLEVEL && !$smarty.const.BRACP_ALLOW_ADMIN_CHANGE_PASSWORD}
            <div class="message warning" style="max-width: 380px;">
                {translate}@CHANGEPASS_MESSAGE_ADMIN@{/translate}
            </div>
            <br>
        {/if}

        <div style="max-width: 380px;">{translate}@CHANGEPASS_MESSAGE_HEADER@{/translate}</div>

        <form class="modal-form" ng-submit="submitPassword()">

            <input type="password" ng-model="user_pass" placeholder="{translate}@CHANGEPASS_HOLDER_ACTUAL.PASSWORD@{/translate}" size="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required title="@@WARNING,PATTERN({$passWordFormat})"/>
            <input type="password" ng-model="user_pass_new" placeholder="{translate}@CHANGEPASS_HOLDER_NEW.PASSWORD@{/translate}" size="32" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required title="@@WARNING,PATTERN({$passWordFormat})"/>
            <input type="password" ng-model="user_pass_conf" placeholder="{translate}@CHANGEPASS_HOLDER_CONFIRM.PASSWORD@{/translate}" size="32" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required title="@@WARNING,PATTERN({$passWordFormat})"/>

            <input id="_submitPassword" type="submit"/>
            
            {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true and $needRecaptcha eq true}
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
            {translate}@CHANGEPASS_ERROR_NOADMIN@{/translate}
        </div>

    </div>

    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitPassword">{translate}@CHANGEPASS_BUTTONS_SUBMIT@{/translate}</label>
        <label class="button error icon" for="modal-password">{translate}@CHANGEPASS_BUTTONS_CLOSE@{/translate}</label>
    </div>
</div>
