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
<input type="checkbox" class="modal-trigger-check" id="modal-recover"/>
<div class="modal" ng-controller="account.recover">
    <div class="modal-title">
        {translate}@RECOVER_TITLE@{/translate}
        <label for="modal-recover" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>

    <div class="modal-body" ng-if="stage == 0">
        {if $smarty.const.BRACP_MD5_PASSWORD_HASH or $smarty.const.BRACP_RECOVER_BY_CODE}
            <div style='max-width: 380px' ng-if="!has_code">
        {/if}
                <div ng-if="$parent.error_state != 0" class="message error">
                    <div ng-switch="$parent.error_state">
                        <div ng-switch-when="-1">{translate}@RECOVER_ERROR_DISABLED@{/translate}</div>
                        <div ng-switch-when="1">{translate}@RECOVER_ERROR_MISMATCH@{/translate}</div>
                        <div ng-switch-when="2">{translate}@ERRORS_REGEXP@{/translate}</div>
                        <div ng-switch-when="3">{translate}@ERRORS_RECAPTCHA@{/translate}</div>
                    </div>
                </div>

                <div ng-if="success_state" class="message success">
                    {if $smarty.const.BRACP_MD5_PASSWORD_HASH or $smarty.const.BRACP_RECOVER_BY_CODE}
                        {translate}@RECOVER_SUCCESS_CODE@{/translate}
                    {else}
                        {translate}@RECOVER_SUCCESS_SEND@{/translate}
                    {/if}
                </div>

                {translate}@RECOVER_MESSAGE_HEADER.NO.CODE@{/translate}

                <form class="modal-form" ng-submit="submitRecover()">

                    <input type="text" ng-model="userid" placeholder="{translate}@RECOVER_HOLDER_USERID@{/translate}" size="32" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required title="{translate}@WARNING_PATTERN_{$userNameFormat}@{/translate}"/>
                    <input type="text" ng-model="email" placeholder="{translate}@RECOVER_HOLDER_EMAIL@{/translate}" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                    <input id="_submitRecover" type="submit"/>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true and $needRecaptcha eq true}
                        <div class="recaptcha" ng-model="recaptcha_response" vc-recaptcha key="'{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}'"></div>
                    {/if}
                </form>
        {if $smarty.const.BRACP_MD5_PASSWORD_HASH or $smarty.const.BRACP_RECOVER_BY_CODE}
            </div>
        {/if}

        {if $smarty.const.BRACP_MD5_PASSWORD_HASH or $smarty.const.BRACP_RECOVER_BY_CODE}
            <div style='max-width: 380px' ng-if="has_code">
                <div ng-if="$parent.error_state != 0" class="message error">
                    <div ng-switch="$parent.error_state">
                        <div ng-switch-when="-1">{translate}@RECOVER_ERROR_DISABLED@{/translate}</div>
                        <div ng-switch-when="1">{translate}@RECOVER_ERROR_USED@{/translate}</div>
                        <div ng-switch-when="2">{translate}@ERRORS_REGEXP@{/translate}</div>
                        <div ng-switch-when="3">{translate}@ERRORS_RECAPTCHA@{/translate}</div>
                    </div>
                </div>

                <div ng-if="success_state" class="message success">
                    {translate}@RECOVER_CONFIRMED@{/translate}
                </div>

                {translate}@RECOVER_MESSAGE_HEADER.HAS.CODE@{/translate}

                <form class="modal-form" ng-submit="submitRecoverConfirm()">

                    <input type="text" ng-model="code" placeholder="{translate}@RECOVER_HOLDER_CODE@{/translate}" maxlength="32" required/>

                    <input id="_submitRecoverConfirm" type="submit"/>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                        <div class="recaptcha" ng-model="recaptcha_response" vc-recaptcha key="'{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}'"></div>
                    {/if}
                </form>
            </div>

            <label class="input-checkbox">
                <input type="checkbox" ng-model="$parent.has_code" ng-click="$parent.error_state = 0; $parent.success_state = false;"/>
                {translate}@RECOVER_HOLDER_HAS.CODE@{/translate}
            </label>
        {/if}
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
        <label class="button success icon" for="_submitRecover" ng-if="!has_code">{translate}@RECOVER_BUTTONS_SUBMIT@{/translate}</label>
        <label class="button success icon" for="_submitRecoverConfirm" ng-if="has_code">{translate}@RECOVER_BUTTONS_CONFIRM@{/translate}</label>
        <label class="button error icon" for="modal-recover">{translate}@RECOVER_BUTTONS_CLOSE@{/translate}</label>
    </div>
</div>
