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

{extends file="bracp.default.tpl"}
{block name="braCP_LocationPath"}
    <li>Perfil</li>
    <li>Código de Confirmação</li>
{/block}
{block name="brACP_Container"}

    {if $verifyResult}
        <div class="message success">
            Verificação concluída com sucesso!
        </div>
        {if !$loggedIn}
            <br>
            Você pode realizar o login clicando no botão <label for="btnLoginTop" class="button small">Entrar</label>.
        {/if}
    {else}
        <div class="message error">
            Não foi possível concluir a verificação, pois o código informado é inválido!<br>
        </div>
        {*
            Conteúdo abaixo não aparece quando você não está logado
            pois o método necessita de login para descobrir o último código de confirmação.
            *}
        {if $loggedIn}
            <div ng-controller="top-user">
                <br>
                Caso você queira tentar re-enviar o código de confirmação, você pode fazer isso clicando aqui <button class="button warning small" ng-click="profileResendCode()">aqui</button>.
            </div>
        {/if}
    {/if}

{/block}
