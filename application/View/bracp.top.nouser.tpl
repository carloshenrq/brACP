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

<div class="bracp-top-buttons" ng-controller="top-no-user" ng-init="init()">

    <button id="btnLoginTop" class="button link" ng-click="changeBox(1)">Entrar</button>
    {if $smarty.const.BRACP_ACCOUNT_CREATE}
        <button class="button link" ng-click="changeBox(2)">Cadastre-se</button>
    {/if}

    <input type="checkbox" class="app-checkbox-box" ng-checked="box == 1"/>
    <div class="app-box">
        <h1>Entrar</h1>

        <form ng-submit="login()">
            <input type="text" class="input" ng-model="user.id" placeholder="Endereço de e-mail" required pattern="{$smarty.const.BRACP_REGEXP_MAIL}"/>
            <input type="password" class="input" ng-model="user.pw" placeholder="Senha" required pattern="{$smarty.const.BRACP_REGEXP_PASS}"/>

            {if $smarty.const.APP_RECAPTCHA_ENABLED}
                <div class="recaptcha" vc-recaptcha ng-model="user.recaptcha_response" key="'{$smarty.const.APP_RECAPTCHA_SITE_KEY}'"></div>
            {/if}

            <div class="form-buttom">
                <button class="button fill">Entrar</button>
            </div>
        </form>

        {if $smarty.const.APP_FACEBOOK_ENABLED}
            <div class="app-facebook" ng-show="!fbLogin.loggedIn">
                <button class="button fill" ng-click="loginWithFb()">Entrar com o Facebook</button>
            </div>

            <div class="app-facebook" ng-show="fbLogin.loggedIn">{literal}
                <button class="button fill" ng-click="loginWithFb()">Entrar como <strong>{{fbLogin.name}}</strong>.</button>
                <button class="button fill link" ng-click="logoutFromFb()">Sair do Facebook</button>
            {/literal}</div>
        {/if}

    </div>

    {if $smarty.const.BRACP_ACCOUNT_CREATE}
        <input type="checkbox" class="app-checkbox-box" ng-checked="box == 2"/>
        <div class="app-box">
            <h1>Cadastre-se</h1>

            <form ng-submit="create()">
                <input type="text" class="input" ng-model="register.name" placeholder="Nome completo" required pattern="{$smarty.const.BRACP_REGEXP_NAME}"/>
                <input type="text" class="input" ng-model="register.email" placeholder="Endereço de e-mail" required pattern="{$smarty.const.BRACP_REGEXP_MAIL}"/>
                <input type="password" class="input" ng-model="register.password" placeholder="Senha" required pattern="{$smarty.const.BRACP_REGEXP_PASS}"/>
                <select class="input" ng-model="register.gender" required>
                    <option value="">* Com qual gênero você se identifica?</option>
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                    <option value="O">Outros</option>
                </select>
                <input type="text" class="input datetime-picker" ng-model="register.birthDate" placeholder="AAAA-MM-DD" required datetime-picker date-only date-format="yyyy-MM-dd" pattern="{literal}^[0-9]{4}-[0-9]{2}-[0-9]{2}${/literal}"/>

                {if $smarty.const.APP_RECAPTCHA_ENABLED}
                    <div class="recaptcha" vc-recaptcha ng-model="register.recaptcha_response" key="'{$smarty.const.APP_RECAPTCHA_SITE_KEY}'"></div>
                {/if}

                <div class="form-buttom">
                    <button class="button fill">Cadastrar</button>
                </div>
            </form>

            {if $smarty.const.APP_FACEBOOK_ENABLED}
                <div class="app-facebook" ng-show="!fbLogin.loggedIn">
                    <button class="button fill" ng-click="createWithFb()">Cadastrar com o Facebook</button>
                </div>

                <div class="app-facebook" ng-show="fbLogin.loggedIn">{literal}
                    <button class="button fill" ng-click="createWithFb()">Cadastrar como <strong>{{fbLogin.name}}</strong>.</button>
                    <button class="button fill link" ng-click="logoutFromFb()">Sair do Facebook</button>
                {/literal}</div>
            {/if}

        </div>
    {/if}

</div>
