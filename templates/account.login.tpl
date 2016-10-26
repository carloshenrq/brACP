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

<input type="checkbox" class="modal-trigger-check" id="modal-login"/>
<div class="modal" ng-controller="account.login">
    <div class="modal-title">
        {translate}@LOGIN_TITLE@{/translate}
        <label for="modal-login" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>

    <div class="modal-body" ng-if="stage == 0">
        
        <div ng-if="loginError" class="message error">
            {translate}@LOGIN_ERROR_MISMATCH@{/translate}
        </div>

        {translate}@LOGIN_MESSAGE_HEADER@{/translate}

        <form class="modal-form" ng-submit="submitLogin()">

            <input type="text" ng-model="userid" placeholder="{translate}@LOGIN_HOLDER_USERID@{/translate}" size="32" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required title="{translate}@WARNING_PATTERN_{$userNameFormat}@{/translate}"/>
            <input type="password" ng-model="user_pass" placeholder="{translate}@LOGIN_HOLDER_PASSWD@{/translate}" size="32" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required title="{translate}@WARNING_PATTERN_{$passWordFormat}@{/translate}"/>

            <input id="_submitLogin" type="submit"/>

            {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true and $needRecaptcha eq true}
                <div class="recaptcha" ng-model="recaptcha_response" vc-recaptcha key="'{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}'"></div>
            {/if}
        </form>

        {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
            <p class="link">{translate}@LOGIN_MESSAGE_CREATE@{/translate}</p>
        {/if}

        {if $smarty.const.BRACP_ALLOW_RECOVER}
            <p class="link">{translate}@LOGIN_MESSAGE_RECOVER@{/translate}</p>
        {/if}

    </div>

    <div class="modal-body" ng-if="stage == 1">

        <div class="loading-ajax" ng-if="!loginSuccess">
            <div class="loading-bar loading-bar-1"></div>
            <div class="loading-bar loading-bar-2"></div>
            <div class="loading-bar loading-bar-3"></div>
            <div class="loading-bar loading-bar-4"></div>
        </div>

        <div class="message success" ng-if="loginSuccess">
            {translate}@LOGIN_SUCCESS@{/translate}
        </div>
    </div>

    <div class="modal-footer" ng-if="stage == 0">
        <label class="button success icon" for="_submitLogin">{translate}@LOGIN_BUTTONS_SUBMIT@{/translate}</label>
        <label class="button error icon" for="modal-login">{translate}@LOGIN_BUTTONS_CLOSE@{/translate}</label>
    </div>
</div>
