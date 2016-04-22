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
        ##RESEND_TITLE##
        <label for="bracp-modal-create-resend" class="modal-close">&times;</label>
    </div>
    <div class="modal-body">
        {if $smarty.const.BRACP_CONFIRM_ACCOUNT eq false}
            <div class="bracp-message error">
                ##RESEND_ERR,DISABLED##
            </div>
        {else}
        
            {if isset($resend_message.success) eq true}
                <div class="bracp-message success">{$resend_message.success}</div>
            {/if}

            {if isset($resend_message.error) eq true}
                <div class="bracp-message error">{$resend_message.error}</div>
            {/if}

            ##RESEND_MSG,0##

            <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register/resend" autocomplete="off" method="post" target=".modal-create-resend-body" data-block="1">

                <div class="input-forms">
                    <input type="text" id="userid" name="userid" placeholder="##RESEND_PLACEHOLDER,USERID##" size="30" maxlength="30" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>

                    <input type="text" id="email" name="email" placeholder="##RESEND_PLACEHOLDER,EMAIL##" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                    <input class="btn btn-success" type="submit" value="##RESEND_BUTTONS,SUBMIT##"/>
                    <input class="btn" type="reset" value="##RESEND_BUTTONS,RESET##"/>
                </div>

            </form>
        {/if}
    </div>
</div>
