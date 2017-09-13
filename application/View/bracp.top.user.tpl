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
 *
 *}

<div class="bracp-top-buttons" ng-controller="top-user" ng-init="init({$loggedUser->id}, {(($loggedUser->blocked) ? 'true':'false')}, {(($loggedUser->verified) ? 'true':'false')})">

    {* Verifica permissões de administrador e exibe informações *}
    {if $loggedUser->privileges eq 'A'}
        <button class="button error" ng-click="admin()">
            Administração
        </button>

        <input type="checkbox" class="app-checkbox-box" ng-checked="box == 2"/>
        <div class="app-box app-list app-admin">
            <div class="app-menu-list">
                <div class="app-menu-item app-menu-title">Configurações</div>
                <div class="app-menu-item">Manutenção</div>
                <div class="app-menu-item">Avisos</div>
                <div class="app-menu-item">Alterar Configurador (config.php)</div>
            </div>
        </div>
    {/if}

    <button class="button link" ng-click="profile()">
        {$loggedUser->name}
        {if $loggedUser->blocked}
            <label class="label error">Bloqueado</label>
        {else if !$loggedUser->verified}
            <label class="label warning">Não verificado</label>
        {/if}
    </button>
    <button class="button link" ng-click="logout()">Encerrar</button>

    <input type="checkbox" class="app-checkbox-box" ng-checked="box == 1"/>
    <div class="app-box app-list">
        <div class="app-menu-list">
            <div class="app-menu-item app-menu-title">Conteúdo</div>
            <div class="app-menu-item" ng-click="profileShowMe()">Meu Perfil</div>
            <div class="app-menu-item" ng-click="profileShowGameAccess()">Acessos ao Jogo</div>

            {* Configurações para facebook *}
            {if $smarty.const.APP_FACEBOOK_ENABLED}

                {* Se não possuir perfil de facebook, permite que o mesmo seja vinculado. *}
                {if empty($loggedUser->facebookId)}
                    <div class="app-menu-item">Vincular <label class="label info">Facebook</label></div>
                {/if}

                {* Se possuir facebook vinculado, e possuir endereço de e-mail + senha cadastrados, então permite
                 * que o facebook seja desvinculado. *}
                {if !empty($loggedUser->facebookId) && !empty($loggedUser->email) && !empty($loggedUser->password)}
                    <div class="app-menu-item">Desvincular <label class="label info">Facebook</label></div>
                {/if}

            {/if}

            <div class="app-menu-item app-menu-title">Configurações</div>

            {* Se o usuário possui uma senha cadastrada, então ele pode fazer a alteração da senha *}
            {if !empty($loggedUser->password)}
                <div class="app-menu-item" ng-click="profileChangePass()">Alterar Senha</div>
            {/if}

            {* Se o usuário não possui e-mail nem senha cadastrada, irá abrir menu para criar acesso. *}
            {if empty($loggedUser->email) && empty($loggedUser->password)}
                <div class="app-menu-item">Criar dados de acesso</div>
            {/if}

            {if !$loggedUser->verified && $smarty.const.APP_MAILER_ALLOWED && $smarty.const.BRACP_ACCOUNT_VERIFY}
                <div class="app-menu-item" ng-click="profileConfirmCode()">Código de Confirmação</div>
            {/if}
            <div class="app-menu-item" ng-click="profileShowLog()">Registro de Atividades</div>
            <div class="app-menu-item" ng-click="profileShowConfig()">Configuração do Perfil</div>
        </div>
    </div>

</div>
