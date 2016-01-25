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

<h1>Minha Conta &raquo; Alterar Senha</h1>

{if $smarty.const.BRACP_ALLOW_ADMIN_CHANGE_PASSWORD eq false and $acc_gmlevel >= $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
    <p class="bracp-message-error">
        Nenhum administrador está permitido a alterar sua senha aqui.
        {if $smarty.const.BRACP_ALLOW_ADMIN eq true}
            <br>
            Você pode alterar acessando o
                <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/manage/account" data-target=".bracp-body">painel administrativo</span>.
        {/if}
    </p>
{else}
    {if $acc_gmlevel >= $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
        <p class="bracp-message-warning">
            <strong>Nota.:</strong> Por motivos de segurança é recomendado que a alteração de senha para adminsitradores seja desabilitada!<br>
            <br>
            Para alterar, edite o arquivo <strong>config.php</strong> e mude a configuração <strong>BRACP_ALLOW_ADMIN_CHANGE_PASSWORD</strong> para <strong>false</strong>
        </p>
    {/if}

    {if isset($message) eq true}
        <p class="bracp-message-{if isset($message.success) eq true}success{else}error{/if}">
            {if isset($message.success) eq true}
                {$message.success}
            {else}
                {$message.error}
            {/if}
        </p>
    {/if}

    <p>
        Para realizar a alteração de sua senha é necessário que você digite sua senha atual, sua nova senha e confirme.
    </p>

    <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/password" autocomplete="off" method="post" target=".bracp-body">
        <div class="bracp-form" style="width: 250px">
            <div class="bracp-form-field">
                <label>
                    Senha atual:<br>{literal}
                    <input type="password" id="user_pass" name="user_pass" placeholder="Senha atual" size="24" maxlength="24" pattern="[a-zA-Z0-9]{4,24}" required/>{/literal}
                </label>
            </div>
            <div class="bracp-form-field">
                <label>
                    Senha nova:<br>{literal}
                    <input type="password" id="user_pass_new" name="user_pass_new" placeholder="Nova senha" size="24" maxlength="24" pattern="[a-zA-Z0-9\s]{4,20}" required/>{/literal}
                </label>
            </div>
            <div class="bracp-form-field">
                <label>
                    Confirme:<br>{literal}
                    <input type="password" id="user_pass_conf" name="user_pass_conf" placeholder="Confirme sua nova senha" size="24" maxlength="24" pattern="[a-zA-Z0-9\s]{4,20}" required/>{/literal}
                </label>

                <div class="bracp-form-submit">
                    <input class="btn" type="submit" value="Alterar"/>
                    <input class="btn" type="reset" value="Resetar"/>
                </div>
            </div>
        </div>
    </form>

{/if}
