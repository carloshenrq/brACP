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
        Minha Conta &raquo; Recuperar
        <label for="bracp-modal-recover" class="modal-close">&times;</label>
    </div>
    <div class="modal-body">
        {if $smarty.const.BRACP_ALLOW_RECOVER eq false}
            <div class="bracp-message error">
                Recuperação de contas está desativada.
            </div>
        {else}
            {if isset($recover_message)}
                {if isset($recover_message.success)}
                    <div class="bracp-message success">{$recover_message.success}</div>
                {else}
                    <div class="bracp-message error">{$recover_message.error}</div>
                {/if}
            {/if}

            Para recuperar seu nome de usuário, você deve preencher abaixo as informações corretas para que seja possível realizar esta recuperação.

            <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover" autocomplete="off" method="post" target=".modal-recover-body" data-block="1">
                <div class="input-forms">
                    <input type="text" id="userid" name="userid" placeholder="Nome de usuário" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>
                    <input type="text" id="email" name="email" placeholder="Endereço de e-mail" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>
            
                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                        <div class="bracp-g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
                    {/if}

                    <input class="btn btn-success" type="submit" value="Recuperar"/>
                    <input class="btn" type="reset" value="Limpar"/>
                </div>
            </form>
        {/if}
    </div>
</div>
