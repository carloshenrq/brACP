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
        @@CHANGEMAIL(TITLE)
        <label for="modal-mail" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>

    <div class="modal-body" ng-if="stage == 0">
        <div ng-if="$parent.error_state != 0" class="message error">
            <div ng-switch="$parent.error_state">
                <div ng-switch-when="-1">@@CHANGEMAIL,ERROR(DISABLED)</div>
                <div ng-switch-when="1">@@CHANGEMAIL,ERROR(NOADMIN)</div>
                <div ng-switch-when="2">@@CHANGEMAIL,ERROR(MISMATCH1)</div>
                <div ng-switch-when="3">@@CHANGEMAIL,ERROR(MISMATCH2)</div>
                <div ng-switch-when="4">@@CHANGEMAIL,ERROR(EQUALS)</div>
                <div ng-switch-when="5">@@CHANGEMAIL,ERROR(DELAY)</div>
                <div ng-switch-when="6">@@ERRORS(REGEXP)</div>
                <div ng-switch-when="7">@@CHANGEMAIL,ERROR(TAKEN)</div>
                <div ng-switch-when="8">@@ERRORS(RECAPTCHA)</div>
            </div>
        </div>

        <div ng-if="success_state" class="message success" style="max-width: 380px;">
            @@CHANGEMAIL(SUCCESS)
        </div>

        <div style="max-width: 380px;">@@CHANGEMAIL,MESSAGE(HEADER)</div>

        <form class="modal-form" ng-submit="submitMail()">

            <input type="text" ng-model="email" placeholder="@@CHANGEMAIL,HOLDER(EMAIL)" size="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
            <input type="text" ng-model="email_new" placeholder="@@CHANGEMAIL,HOLDER(NEW_EMAIL)" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
            <input type="text" ng-model="email_conf" placeholder="@@CHANGEMAIL,HOLDER(CONFIRM)" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

            <input id="_submitMail" type="submit"/>

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

    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitMail">@@CHANGEMAIL,BUTTONS(SUBMIT)</label>
        <label class="button error icon" for="modal-mail">@@CHANGEMAIL,BUTTONS(CLOSE)</label>
    </div>
</div>

