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
<div class="modal" ng-app="account" ng-controller="register">
    <div class="modal-title">
        @@CREATE(TITLE)
        <label for="modal-register" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>
    <div class="modal-body" ng-if="stage == 0">

        <div style='max-width: 380px'>@@CREATE,MESSAGE(HEADER)</div>

        <form class="modal-form" ng-submit="submitRegister()">

            <input type="text" ng-model="userid" placeholder="@@CREATE,HOLDER(USERID)" size="32" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>
            <input type="password" ng-model="user_pass" placeholder="@@CREATE,HOLDER(PASSWORD)" size="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>
            <input type="password" ng-model="user_pass_conf" placeholder="@@CREATE,HOLDER(PASSWORD_CONFIRM)" size="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

            <input id="_submitRegister" type="submit"/>
        </form>

    </div>
    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitRegister">@@CREATE,BUTTONS(SUBMIT)</label>
        <label class="button error icon" for="modal-register">@@CREATE,BUTTONS(CLOSE)</label>
    </div>
</div>
