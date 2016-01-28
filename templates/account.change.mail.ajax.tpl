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

<h1>Minha Conta &raquo; Alterar Email</h1>

{if $account->getGroup_id() >= $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
    <p class="bracp-message-error">
        Nenhum administrador está permitido a alterar seu endereço de email.
        {if $smarty.const.BRACP_ALLOW_ADMIN eq true}
            <br>
            Você pode alterar acessando o
                <span class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}admin/manage/account" data-target=".bracp-body">painel administrativo</span>.
        {/if}
    </p>
{else}

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
        Para realizar a alteração de seu endereço de e-mail é necessário que você digite seu e-mail atual, seu novo endereço de email e confirme!
    </p>

    <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/mail" autocomplete="off" method="post" target=".bracp-body">
        <div class="bracp-form" style="width: 380px">
            <div class="bracp-form-field">
                <label>
                    Email atual:<br>{literal}
                    <input type="text" id="email" name="email" placeholder="Email atual" size="39" maxlength="39" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}" required/>{/literal}
                </label>
            </div>
            <div class="bracp-form-field">
                <label>
                    Novo email:<br>{literal}
                    <input type="text" id="email_new" name="email_new" placeholder="Novo email" size="39" maxlength="39" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}" required/>{/literal}
                </label>
            </div>
            <div class="bracp-form-field">
                <label>
                    Confirme:<br>{literal}
                    <input type="text" id="email_conf" name="email_conf" placeholder="Confirme seu novo email" size="39" maxlength="39" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}" required/>{/literal}
                </label>

                <div class="bracp-form-submit">
                    <input class="btn" type="submit" value="Alterar"/>
                    <input class="btn" type="reset" value="Resetar"/>
                </div>
            </div>
        </div>
    </form>

    {if $smarty.const.BRACP_MAIL_SHOW_LOG eq true}
        {if count($mailChange) gt 0}
            <br>
            <table border="1" align="center" class="bracp-table">
                <caption class="bracp-message-warning">{min(10, count($mailChange))} última(s) alteração(ões) de e-mail.</caption>
                <thead>
                    <tr>
                        <th>Cód.</th>
                        <th>Antigo</th>
                        <th>Novo</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$mailChange item=change}
                        <tr>
                            <td>{$change->getId()}</td>
                            <td>{Format::protectMail($change->getFrom())}</td>
                            <td>{Format::protectMail($change->getTo())}</td>
                            <td>{Format::date($change->getDate())}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <p class="bracp-message-warning">Você ainda não realizou nenhuma mudança de e-mail.</p>
        {/if}
    {/if}
{/if}
