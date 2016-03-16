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
<div class="modal-create-body">
    <div class="modal">
        <div class="modal-header">
            Criar Conta
            <label for="bracp-modal-create" class="modal-close">&times;</label>
        </div>
        <div class="modal-body">
            {if isset($register_message.success) eq true}
                <div class="bracp-message success">{$register_message.success}</div>
            {/if}

            {if isset($register_message.error) eq true}
                <div class="bracp-message error">{$register_message.error}</div>
            {/if}

            Para criar sua conta, é necessário que você informe os dados abaixo corretamente para que seja possivel seu acesso ao jogo e as funções do painel de controle.

            <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register" autocomplete="off" method="post" target=".modal-create-body" data-block="1">

                <div class="input-forms">
                    <input type="text" id="userid" name="userid" placeholder="Nome de usuário" size="30" maxlength="30" pattern="{$smarty.const.BRACP_REGEXP_USERNAME}" required/>

                    <input type="password" id="user_pass" name="user_pass" placeholder="Senha de usuário" size="30" maxlength="30" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

                    <input type="password" id="user_pass_conf" name="user_pass_conf" placeholder="Confirme a senha" size="30" maxlength="30" pattern="{$smarty.const.BRACP_REGEXP_PASSWORD}" required/>

                    <div>
                        <input id="sex_m" name="sex" value="M" type="radio" checked/>
                        <label for="sex_m">Masculino</label>

                        <input id="sex_f" name="sex" value="F" type="radio"/>
                        <label for="sex_f">Feminino</label>
                    </div>

                    <input type="text" id="email" name="email" placeholder="Endereço de e-mail" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                    <input type="text" id="email_conf" name="email_conf" placeholder="Confirme o email" size="39" maxlength="39" pattern="{$smarty.const.BRACP_REGEXP_EMAIL}" required/>

                    <div>
                        <input type="checkbox" id="terms" name="terms" title="Você precisa aceitar os termos para continuar." required/>
                        <label for="terms">
                            Eu concordo com os termos do servidor.
                        </label>
                    </div>

                    {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                        <div class="bracp-g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
                    {/if}

                    <input class="btn btn-success" type="submit" value="Entrar"/>
                    <input class="btn" type="reset" value="Limpar"/>
                </div>

            </form>
        </div>
    </div>
</div>
