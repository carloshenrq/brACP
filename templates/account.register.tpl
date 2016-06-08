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
        @@CREATE(TITLE)
        <label for="modal-register" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>
    <div class="modal-body" ng-if="stage == 0">

        <div ng-if="accept_terms">

            <div ng-if="error_state > 0" class="message error">
                <div ng-switch="error_state">
                    <div ng-switch-when="-1">@@CREATE,ERROR(DISABLED)</div>
                    <div ng-switch-when="1">@@CREATE,ERROR(USED)</div>
                    <div ng-switch-when="2">@@CREATE,ERROR,MISMATCH(PASSWORD)</div>
                    <div ng-switch-when="3">@@CREATE,ERROR,MISMATCH(EMAIL)</div>
                    <div ng-switch-when="4">@@CREATE,ERROR,MISMATCH(ADMIN_MODE)</div>
                    <div ng-switch-when="5">@@ERRORS(REGEXP)</div>
                </div>
            </div>

            <div style='max-width: 380px'>@@CREATE,MESSAGE(HEADER)</div>

            <form class="modal-form" ng-submit="submitRegister()">
                <input type="text" ng-model="userid" placeholder="@@CREATE,HOLDER(USERID)" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>
                <input type="password" ng-model="user_pass" placeholder="@@CREATE,HOLDER(PASSWORD)" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>
                <input type="password" ng-model="user_pass_conf" placeholder="@@CREATE,HOLDER(PASSWORD_CONFIRM)" maxlength="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>
                <select ng-model="sex">
                    <option value="M">@@CREATE,HOLDER(MALE)</option>
                    <option value="F">@@CREATE,HOLDER(FEMALE)</option>
                </select>
                <input type="text" ng-model="email" placeholder="@@CREATE,HOLDER(EMAIL)" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
                <input type="text" ng-model="email_conf" placeholder="@@CREATE,HOLDER(EMAIL_CONFIRM)" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                <input id="_submitRegister" type="submit"/>
            </form>
        </div>

        <div ng-if="!accept_terms" class="message info">
            {include '../license.txt'}
        </div>

        <label class="input-checkbox">
            <input type="checkbox" ng-model="accept_terms" required/>
            @@CREATE,HOLDER(ACCEPT_TERMS)
        </label>
    </div>
    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitRegister">@@CREATE,BUTTONS(SUBMIT)</label>
        <label class="button error icon" for="modal-register">@@CREATE,BUTTONS(CLOSE)</label>
    </div>
</div>
