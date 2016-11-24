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
<input type="checkbox" class="modal-trigger-check" id="modal-mail"/>
<div class="modal" ng-controller="account.email">
    <div class="modal-title">
        {translate}@CHANGEMAIL_TITLE@{/translate}
        <label for="modal-mail" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>

    <div class="modal-body" ng-if="stage == 0">
        <div ng-if="$parent.error_state != 0" class="message error">
            <div ng-switch="$parent.error_state">
                <div ng-switch-when="-1">{translate}@CHANGEMAIL_ERROR_DISABLED@{/translate}</div>
                <div ng-switch-when="1">{translate}@CHANGEMAIL_ERROR_NOADMIN@{/translate}</div>
                <div ng-switch-when="2">{translate}@CHANGEMAIL_ERROR_MISMATCH1@{/translate}</div>
                <div ng-switch-when="3">{translate}@CHANGEMAIL_ERROR_MISMATCH2@{/translate}</div>
                <div ng-switch-when="4">{translate}@CHANGEMAIL_ERROR_EQUALS@{/translate}</div>
                <div ng-switch-when="5">{translate}@CHANGEMAIL_ERROR_DELAY@{/translate}</div>
                <div ng-switch-when="6">{translate}@ERRORS_REGEXP@{/translate}</div>
                <div ng-switch-when="7">{translate}@CHANGEMAIL_ERROR_TAKEN@{/translate}</div>
                <div ng-switch-when="8">{translate}@ERRORS_RECAPTCHA@{/translate}</div>
                <div ng-switch-default>{literal}{{$parent.error_state}}{/literal}</div>
            </div>
        </div>

        <div ng-if="success_state" class="message success" style="max-width: 380px;">
            {translate}@CHANGEMAIL_SUCCESS@{/translate}
        </div>

        <div style="max-width: 380px;">{translate}@CHANGEMAIL_MESSAGE_HEADER@{/translate}</div>

        <form class="modal-form" ng-submit="submitMail()">

            <input type="text" ng-model="email" placeholder="{translate}@CHANGEMAIL_HOLDER_EMAIL@{/translate}" size="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
            <input type="text" ng-model="email_new" placeholder="{translate}@CHANGEMAIL_HOLDER_NEWEMAIL@{/translate}" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
            <input type="text" ng-model="email_conf" placeholder="{translate}@CHANGEMAIL_HOLDER_CONFIRM@{/translate}" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

            <input id="_submitMail" type="submit"/>

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

    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitMail">{translate}@CHANGEMAIL_BUTTONS_SUBMIT@{/translate}</label>
        <label class="button error icon" for="modal-mail">{translate}@CHANGEMAIL_BUTTONS_CLOSE@{/translate}</label>
    </div>
</div>

