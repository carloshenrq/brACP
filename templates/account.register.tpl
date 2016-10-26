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

<input type="checkbox" class="modal-trigger-check" id="modal-register"/>
<div class="modal" ng-controller="account.register">
    <div class="modal-title">
        {translate}@CREATE_TITLE@{/translate}
        <label for="modal-register" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>
    <div class="modal-body" ng-if="stage == 0">
        <div ng-if="accept_terms">

            <div ng-if="error_state > 0" class="message error">
                <div ng-switch="error_state">
                    <div ng-switch-when="-1">{translate}@CREATE_ERROR_DISABLED@{/translate}</div>
                    <div ng-switch-when="1">{translate}@CREATE_ERROR_USED@{/translate}</div>
                    <div ng-switch-when="2">{translate}@CREATE_ERROR_MISMATCH_PASSWORD@{/translate}</div>
                    <div ng-switch-when="3">{translate}@CREATE_ERROR_MISMATCH_EMAIL@{/translate}</div>
                    <div ng-switch-when="4">{translate}@CREATE_ERROR_MISMATCH_ADMIN.MODE@{/translate}</div>
                    <div ng-switch-when="5">{translate}@ERRORS_REGEXP@{/translate}</div>
                    <div ng-switch-when="6">{translate}@ERRORS_RECAPTCHA@{/translate}</div>
                </div>
            </div>

            <div ng-if="success_state" class="message success">
                {translate}@CREATE_SUCCESS@{/translate}
                {if $smarty.const.BRACP_ALLOW_MAIL_SEND && $smarty.const.BRACP_CONFIRM_ACCOUNT}
                    <br>
                    {translate}@RESEND_SUCCESS@{/translate}
                {/if}
            </div>

            <div style='max-width: 380px'>{translate}@CREATE_MESSAGE_HEADER@{/translate}</div>

            <form class="modal-form" ng-submit="submitRegister()">
                <input type="text" ng-model="userid" placeholder="{translate}@CREATE_HOLDER_USERID@{/translate}" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required title="@@WARNING,PATTERN({$userNameFormat})"/>
                <input type="password" ng-model="user_pass" placeholder="{translate}@CREATE_HOLDER_PASSWORD@{/translate}" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required title="@@WARNING,PATTERN({$passWordFormat})"/>
                <input type="password" ng-model="user_pass_conf" placeholder="{translate}@CREATE_HOLDER_PASSWORD.CONFIRM@{/translate}" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required title="@@WARNING,PATTERN({$passWordFormat})"/>
                <select ng-model="sex">
                    <option value="M">{translate}@CREATE_HOLDER_MALE@{/translate}</option>
                    <option value="F">{translate}@CREATE_HOLDER_FEMALE@{/translate}</option>
                </select>
                <input type="text" ng-model="email" placeholder="{translate}@CREATE_HOLDER_EMAIL@{/translate}" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
                <input type="text" ng-model="email_conf" placeholder="{translate}@CREATE_HOLDER_EMAIL.CONFIRM@{/translate}" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
                <input type="text" ng-model="birthdate" datetime="{translate}@CREATE_FORMAT_BIRTHDATE@{/translate}" placeholder="{translate}@CREATE_HOLDER_BIRTHDATE@{/translate}" required/>

                <input id="_submitRegister" type="submit"/>

                {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true and $needRecaptcha eq true}
                    <div class="recaptcha" ng-model="recaptcha_response" vc-recaptcha key="'{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}'"></div>
                {/if}
            </form>
        </div>

        <div ng-if="!accept_terms" class="message info">
            {include '../license.txt'}
        </div>

        <label class="input-checkbox">
            <input type="checkbox" ng-model="$parent.accept_terms" required/>
            {translate}@CREATE_HOLDER_ACCEPT.TERMS@{/translate}
        </label>
    </div>
    <div class="modal-body" ng-if="stage == 1">
        <div class="loading-ajax">
            <div class="loading-bar loading-bar-1"></div>
            <div class="loading-bar loading-bar-2"></div>
            <div class="loading-bar loading-bar-3"></div>
            <div class="loading-bar loading-bar-4"></div>
        </div>
    </div>
    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitRegister" ng-if="accept_terms">{translate}@@CREATE_BUTTONS_SUBMIT@{/translate}</label>
        <label class="button error icon" for="modal-register">{translate}@CREATE_BUTTONS_CLOSE@{/translate}</label>
    </div>
</div>
