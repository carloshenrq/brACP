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

<h1>Criar Conta</h1>

{if isset($message.success) eq true}
    <p class="bracp-message-success">{$message.success}</p>
{else}

<p>Para criar sua conta, é necessário que você informe os dados abaixo corretamente para que seja possivel seu acesso ao jogo e as funções do painel de controle.</p>

    {if isset($message.error) eq true}
        <p class="bracp-message-error">{$message.error}</p>
    {/if}

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register" autocomplete="off" method="post" target=".bracp-body">
    <div class="bracp-form">
        <div class="bracp-form-field">
            <label>
                Usuário:<br>{literal}
                <input type="text" id="userid" name="userid" placeholder="Nome de usuário" size="24" maxlength="24" pattern="[a-zA-Z0-9]{4,24}" required/>{/literal}
            </label>
        </div>
        <div class="bracp-form-field">
            <label>
                Senha:<br>{literal}
                <input type="password" id="user_pass" name="user_pass" placeholder="Senha de usuário" size="20" maxlength="20" pattern="[a-zA-Z0-9\s]{4,20}" required/>{/literal}
            </label>
        </div>
        <div class="bracp-form-field">
            <label>
                Confirme:<br>{literal}
                <input type="password" id="user_pass_conf" name="user_pass_conf" placeholder="Confirme a senha" size="20" maxlength="20" pattern="[a-zA-Z0-9\s]{4,20}" required/>{/literal}
            </label>
        </div>
        <div class="bracp-form-field">
            <label>
                Sexo:<br>
                <select id="sex" name="sex">
                    <option value="M" selected>Masculino</option>
                    <option value="F">Feminino</option>
                </select>
            </label>
        </div>
        <div class="bracp-form-field">
            <label>
                Email:<br>{literal}
                <input type="text" id="email" name="email" placeholder="Endereço de e-mail" size="39" maxlength="39" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}" required/>{/literal}
            </label>
        </div>
        <div class="bracp-form-field">
            <label>
                Confirme:<br>{literal}
                <input type="text" id="email_conf" name="email_conf" placeholder="Confirme o email" size="39" maxlength="39" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}" required/>{/literal}
            </label>
        </div>
        <div class="bracp-form-field">
            <label>
                <input type="checkbox" id="terms" name="terms" title="Você precisa aceitar os termos para continuar." required/>
                Eu concordo com os termos de uso do servidor.
            </label>

            {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                <div class="g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
            {/if}

            <div class="bracp-form-submit">
                <input class="btn" type="submit" value="Cadastrar"/>
                <input class="btn" type="reset" value="Resetar"/>
            </div>
            <div class="bracp-form-submit">
                Perdeu sua conta? <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover" data-target=".bracp-body">clique aqui</span>.<br>
                Já possui uma conta? <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login" data-target=".bracp-body">Faça login</span>.
            </div>
        </div>
    </div>

</form>
{/if}