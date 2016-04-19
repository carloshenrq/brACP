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
        ##CHANGEPASS_TITLE##
        <label for="bracp-modal-changepass" class="modal-close">&times;</label>
    </div>
    <div class="modal-body">
        {if $smarty.const.BRACP_ALLOW_ADMIN_CHANGE_PASSWORD eq false and $account->getGroup_id() >= $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
            <div class="bracp-message error">
                ##CHANGEPASS_NOADMIN##
            </div>
        {else}
            {if $account->getGroup_id() >= $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
                <div class="bracp-message warning">
                    ##CHANGEPASS_NOADMIN_MSG,0##<br>
                    ##CHANGEPASS_NOADMIN_MSG,1##<br>
                    ##CHANGEPASS_NOADMIN_MSG,2##
                </div>
            {/if}

            {if isset($password_message.success) eq true}
                <div class="bracp-message success">{$password_message.success}</div>
            {else if isset($password_message.error) eq true}
                <div class="bracp-message error">{$password_message.error}</div>
            {/if}

            ##CHANGEPASS_MSG,0##

            <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/password" autocomplete="off" method="post" target=".modal-changepass-body" data-block="1">
                <div class="input-forms">
                    <input type="password" id="user_pass" name="user_pass" placeholder="##CHANGEPASS_PLACEHOLDER,ACTUAL_PASSWORD##" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

                    <input type="password" id="user_pass_new" name="user_pass_new" placeholder="##CHANGEPASS_PLACEHOLDER,NEW_PASSWORD##" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>
                    
                    <input type="password" id="user_pass_conf" name="user_pass_conf" placeholder="##CHANGEPASS_PLACEHOLDER,CONFIRM_PASSWORD##" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                        <div class="bracp-g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
                    {/if}

                    <input class="btn btn-success" type="submit" value="##CHANGEPASS_BUTTONS,SUBMIT##"/>
                    <input class="btn" type="reset" value="##CHANGEPASS_BUTTONS,RESET##"/>
                </div>
            </form>
        {/if}
    </div>
</div>
