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
<div class="modal">
    <div class="modal-header">
        ##LOGIN_TITLE##
        <label for="bracp-modal-login" class="modal-close">&times;</label>
    </div>
    <div class="modal-body">
        {if isset($login_message.success) eq true}
            <div class="bracp-message success">{$login_message.success}</div>

            <script>
                window.location.href = '{$smarty.const.BRACP_DIR_INSTALL_URL}';
            </script>
        {else if isset($login_message.error) eq true}
            <div class="bracp-message error">{$login_message.error}</div>
        {/if}

        ##LOGIN_MSG,0##

        <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login" autocomplete="off" method="post" target=".modal-login-body" data-block="1">
            <div class="input-forms">
                <input type="text" id="userid" name="userid" placeholder="##LOGIN_PLACEHOLDER,USERID##" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>

                <input type="password" id="user_pass" name="user_pass" placeholder="##LOGIN_PLACEHOLDER,PASSWD##" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

                {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                    <div class="bracp-g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
                {/if}

                <input class="btn btn-success" type="submit" value="##LOGIN_BUTTONS,SUBMIT##"/>
                <input class="btn" type="reset" value="##LOGIN_BUTTONS,RESET##"/>
            </div>
        </form>

        ##LOGIN_MSG,LOST_ACC##<br>
        ##LOGIN_MSG,CREATE_ACC##
    </div>
</div>
