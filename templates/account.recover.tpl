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
        @@RECOVER(TITLE)
        <label for="modal-recover" class="modal-close" ng-if="stage == 0">&times;</label>
    </div>

    <div class="modal-body" ng-if="stage == 0">
        {if $smarty.const.BRACP_MD5_PASSWORD_HASH or $smarty.const.BRACP_RECOVER_BY_CODE}
            <div style='max-width: 380px' ng-if="!has_code">
        {/if}
                <div ng-if="$parent.error_state != 0" class="message error">
                    <div ng-switch="$parent.error_state">
                        <div ng-switch-when="-1">@@RECOVER,ERROR(DISABLED)</div>
                        <div ng-switch-when="1">@@RECOVER,ERROR(MISMATCH)</div>
                        <div ng-switch-when="2">@@ERRORS(REGEXP)</div>
                        <div ng-switch-when="3">@@ERRORS(RECAPTCHA)</div>
                    </div>
                </div>

                <div ng-if="success_state" class="message success">
                    {if $smarty.const.BRACP_MD5_PASSWORD_HASH or $smarty.const.BRACP_RECOVER_BY_CODE}
                        @@RECOVER,SUCCESS(CODE)
                    {else}
                        @@RECOVER,SUCCESS(SEND)
                    {/if}
                </div>

                @@RECOVER,MESSAGE(HEADER_NO_CODE)

                <form class="modal-form" ng-submit="submitRecover()">

                    <input type="text" ng-model="userid" placeholder="@@RECOVER,HOLDER(USERID)" size="32" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>
                    <input type="text" ng-model="email" placeholder="@@RECOVER,HOLDER(EMAIL)" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                    <input id="_submitRecover" type="submit"/>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
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
                        <div ng-switch-when="-1">@@RECOVER,ERROR(DISABLED)</div>
                        <div ng-switch-when="1">@@RECOVER,ERROR(USED)</div>
                        <div ng-switch-when="2">@@ERRORS(REGEXP)</div>
                        <div ng-switch-when="3">@@ERRORS(RECAPTCHA)</div>
                    </div>
                </div>

                <div ng-if="success_state" class="message success">
                    @@RECOVER(CONFIRMED)
                </div>

                @@RECOVER,MESSAGE(HEADER_HAS_CODE)

                <form class="modal-form" ng-submit="submitRecoverConfirm()">

                    <input type="text" ng-model="code" placeholder="@@RECOVER,HOLDER(CODE)" maxlength="32" required/>

                    <input id="_submitRecoverConfirm" type="submit"/>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                        <div class="recaptcha" ng-model="recaptcha_response" vc-recaptcha key="'{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}'"></div>
                    {/if}
                </form>
            </div>

            <label class="input-checkbox">
                <input type="checkbox" ng-model="$parent.has_code" ng-click="$parent.error_state = 0; $parent.success_state = false;"/>
                @@RECOVER,HOLDER(HAS_CODE)
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
        <label class="button success icon" for="_submitRecover" ng-if="!has_code">@@RECOVER,BUTTONS(SUBMIT)</label>
        <label class="button success icon" for="_submitRecoverConfirm" ng-if="has_code">@@RECOVER,BUTTONS(CONFIRM)</label>
        <label class="button error icon" for="modal-recover">@@RECOVER,BUTTONS(CLOSE)</label>
    </div>
</div>
