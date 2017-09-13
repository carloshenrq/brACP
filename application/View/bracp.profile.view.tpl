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
    <li>Visualização</li>
    <li>
        {if $selfProfile or $allowedSee}
            {$profile->name}
        {else}
            <strong>Privado</strong>
        {/if}
        (Cód.: {$profile->id})
    </li>
{/block}
{block name="brACP_Container"}


    <div class="profile-container" ng-controller="profile-view" ng-init="profileId = {$profile->id};">
        {if $selfProfile}
            <div class="message warning small">
                <strong>Lembre-se:</strong> Você está visualizando o seu próprio perfil
            </div>
        {else if $admin}
            <div class="message success small">
                <strong>Lembre-se:</strong> Seu acesso é administrador.
            </div>
        {/if}
        
        <div class="profile">
            <div class="profile-info">
                {if isset($loggedUser)}
                    <div class="profile-actions-top">
                        {if !$selfProfile}
                            {if !$blocked}
                                <button class="button small">Adicionar amigo</button>
                            {/if}
                            {if $profile->allowMessage eq $viewerVisibility}
                                <button class="button info small">Nova Mensagem</button>
                            {/if}
                        {else}
                            <button class="button warning small" ng-click="profileEdit()">Editar</button>
                        {/if}
                    </div>
                {/if}

                {if empty($profile->avatarUrl)}
                    <div class="profile-image" style="background-image: url({$smarty.const.APP_URL_PATH}/asset/img/default.png);"></div>
                {else}
                    <div class="profile-image" style="background-image: url({$profile->avatarUrl});"></div>
                {/if}

                <div class="profile-name">
                    {if $selfProfile or $allowedSee}
                        {$profile->name}
                    {else}
                        <strong>Privado</strong>
                    {/if}
                </div>

                {if $selfProfile or $allowedSee}
                    <div class="profile-about">
                        {if empty($profile->aboutMe)}
                            {if $selfProfile}
                                "Você ainda não escreveu um pouco sobre você..."
                            {else}
                                "Nada a se visualizar por aqui..."
                            {/if}
                        {else}
                            {$profile->aboutMe}
                        {/if}
                    </div>
                    <div class="profile-other">
                        <table width="100%">
                            {if $smarty.const.BRACP_ACCOUNT_VERIFY}
                                <tr>
                                    <td align="left" width="100px"><strong>Estado:</strong></td>
                                    <td align="left">
                                        {if $profile->blocked}
                                            <label class="label error">Bloqueado</label>
                                        {else if !$profile->verified}
                                            <label class="label warning">Não verificado</label>
                                        {else}
                                            <label class="label success">Verificado</label>
                                        {/if}
                                    </td>
                                </tr>
                            {/if}
                            {if !empty($profile->email)}
                                <tr>
                                    <td align="left" width="100px"><strong>E-mail:</strong></td>
                                    <td align="left">
                                        {if $selfProfile or $viewerVisibility eq $profile->showEmail}
                                            {$profile->email}
                                        {else}
                                            <label class="label error">Privado</label> <em>*Necessário permissão do usuário</em>
                                        {/if}
                                    </td>
                                </tr>
                            {/if}
                            <tr>
                                <td align="left" width="100px"><strong>Nascimento:</strong></td>
                                <td align="left">
                                    {if $selfProfile or $viewerVisibility eq $profile->showBirthdate}
                                        {$formatter->date($profile->birthdate->format('Y-m-d'), true, false)}
                                    {else}
                                        <label class="label error">Privado</label> <em>*Necessário permissão do usuário</em>
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <td align="left" width="100px"><strong>Gênero:</strong></td>
                                <td align="left">{$formatter->gender($profile->gender)}</td>
                            </tr>
                            <tr>
                                <td align="left" width="100px"><strong>Membro desde:</strong></td>
                                <td align="left">{$formatter->date($profile->registerDate->format('Y-m-d H:i:s'))}</td>
                            </tr>
                            {if $smarty.const.APP_FACEBOOK_ENABLED}
                                <tr>
                                    <td align="left" width="100px"><strong>Facebook:</strong></td>
                                    <td align="left">
                                        {if $selfProfile or $viewerVisibility eq $profile->showFacebook}
                                            {$profile->facebookId}
                                        {else}
                                            <label class="label error">Privado</label> <em>*Necessário permissão do usuário</em>
                                        {/if}
                                    </td>
                                </tr>
                            {/if}
                        </table>
                    </div>
                {else if !$allowedSee}
                    <div class="message error" style="margin-top: 1em; text-align: center;">
                        {if $blocked}
                            <strong><em>Você bloqueou este perfil.</em></strong>
                        {else}
                            As informações deste perfil são restritas.
                        {/if}
                    </div>
                {/if}

                {if isset($loggedUser) && !$selfProfile}
                    <div class="profile-actions-bottom">
                        {if $loggedUser->canReportProfiles}
                            <button class="button error small" ng-click="profileReport()">Denunciar</button>
                        {/if}

                        {if !$blocked}
                            <button class="button warning small" ng-click="profileBlock()">Bloquear</button>
                        {else}
                            <button class="button info small" ng-click="profileUnblock()">Desbloquear</button>
                        {/if}
                    </div>
                {/if}
            </div>

        </div>

    </div>


{/block}