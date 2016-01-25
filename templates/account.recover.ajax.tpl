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

<h1>Minha Conta &raquo; Recuperar</h1>

{if isset($message)}
    {if isset($message.success)}
        <p class="bracp-message-success">{$message.success}</p>
    {else}
        <p class="bracp-message-error">{$message.error}</p>
    {/if}
{/if}

<p>Para recuperar seu nome de usuário, você deve preencher abaixo as informações corretas para que seja possível realizar esta recuperação.</p>

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover" autocomplete="off" method="post" target=".bracp-body">
    <div class="bracp-form" style="width: 350px">
        <div class="bracp-form-field">
            <label>
                Usuário:<br>{literal}
                <input type="text" id="userid" name="userid" placeholder="Nome de usuário" size="24" maxlength="24" pattern="[a-zA-Z0-9]{4,24}" required/>{/literal}
            </label>
        </div>
        <div class="bracp-form-field">
            <label>
                E-mail:<br>{literal}
                <input type="text" id="email" name="email" placeholder="Endereço de e-mail" size="39" maxlength="39" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}" required/>{/literal}
            </label>
        </div>
        <div class="bracp-form-field">
            <div class="bracp-form-submit">
                <input type="submit" value="Recuperar"/>
                <input type="reset" value="Resetar"/>
            </div>
        </div>
        <div class="bracp-form-submit">
            Lembrou seus dados? <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login" data-target=".bracp-body">clique aqui</span>.<br>
            Não possui uma conta? <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register" data-target=".bracp-body">clique aqui</span>.
        </div>
    </div>
</form>
