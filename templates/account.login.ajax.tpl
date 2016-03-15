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
        Minha Conta &raquo; Entrar
        <label for="bracp-modal-login" class="modal-close">&times;</label>
    </div>
    <div class="modal-login-body modal-body">
        {if isset($message.success) eq true}
            <p class="bracp-message success">{$message.success}</p>

            <script>
                setTimeout(function() {
                    window.location.href = '{$smarty.const.BRACP_DIR_INSTALL_URL}';
                }, 2000);
            </script>
        {else}
            {if isset($message.error) eq true}
                <p class="bracp-message error">{$message.error}</p>
            {/if}

            Para acessar os dados de sua conta, você deve realizar o acesso utilizando seu nome de usuário e senha.

            <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login" autocomplete="off" method="post" target=".modal-login-body">
                <div class="input-forms">
                    <input type="text" id="userid" name="userid" placeholder="Nome de usuário" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>
                    <input type="password" id="user_pass" name="user_pass" placeholder="Senha de usuário" size="24" maxlength="24" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                        <div class="g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
                    {/if}

                    <input class="btn btn-success" type="submit" value="Entrar"/>
                    <input class="btn" type="reset" value="Limpar"/>
                </div>
            </form>

            Perdeu sua conta? <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover" data-target=".bracp-body">clique aqui</span>.<br>
            Não possui uma conta? <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register" data-target=".bracp-body">clique aqui</span>.

        {/if}
    </div>
</div>
