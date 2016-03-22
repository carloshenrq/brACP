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
        Minha Conta &raquo; Alterar Email
        <label for="bracp-modal-changemail" class="modal-close">&times;</label>
    </div>
    <div class="modal-body">
        {if $smarty.const.BRACP_ALLOW_CHANGE_MAIL eq false}
            <div class="bracp-message error">
                Alteração de e-mail está desativada.
            </div>
        {else if $account->getGroup_id() >= $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
            <div class="bracp-message error">
                Nenhum administrador está permitido a alterar seu endereço de email.
            </div>
        {else}
            {if isset($email_message) eq true}
                <p class="bracp-message {if isset($email_message.success) eq true}success{else}error{/if}">
                    {if isset($email_message.success) eq true}
                        {$email_message.success}
                    {else}
                        {$email_message.error}
                    {/if}
                </p>
            {/if}

            Para realizar a alteração de seu endereço de e-mail é necessário que você digite seu e-mail atual, seu novo endereço de email e confirme!

            <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/mail" autocomplete="off" method="post" target=".modal-changemail-body" data-block="1">
                <div class="input-forms">
                    <input type="text" id="email" name="email" placeholder="Email atual" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                    <input type="text" id="email_new" name="email_new" placeholder="Novo email" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
                    
                    <input type="text" id="email_conf" name="email_conf" placeholder="Confirme seu novo email" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                        <div class="bracp-g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
                    {/if}

                    <input class="btn btn-success" type="submit" value="Alterar"/>
                    <input class="btn" type="reset" value="Limpar"/>
                </div>
            </form>
        {/if}
    </div>
</div>
