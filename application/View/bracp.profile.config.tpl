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
    <li>Configuração</li>
{/block}
{block name="brACP_Container"}


    <div class="profile-container" ng-controller="profile-edit" ng-init="init('{$profile}')">
        
            <div class="profile">
                <div class="profile-info form">
                    <div class="profile-actions-top">
                        <button class="button small" ng-click="save()">Salvar</button>
                        <button class="button small error" ng-click="reset()">Resetar</button>
                    </div>

                    <div ng-show="!loggedUser.gaAllowed">
                        <p class="message warning">
                            O <strong>Google Authenticator</strong> ainda não foi vinculado a sua conta.<br>
                            Recomendamos que você ative esta opção para maior segurança de sua conta.
                            <br><br>
                            <button class="button fill" ng-click="profileActivateGoogle()">
                                Ativar <strong>Google Authenticator</strong>
                            </button>
                        </p>
                        <br>
                    </div>

                    {literal}<img class="profile-image" src="{{loggedUser.avatarUrl}}"/>{/literal}
                    <center><em><strong>Limite de tamanho:</strong> 200kb</em></center>
        

                    <file-base64 id="profileImage" ng-model="loggedUser.avatarUrl"></file-base64>
                    <center>
                        <label for="profileImage" class="button warning small">Selecionar imagem</label>
                        <button class="button small info" ng-click="loggedUser.avatarUrl = savedUser.avatarUrl;">Desfazer</label>
                        <button class="button small error" ng-click="loggedUser.avatarUrl = '{$blankAvatar}';">Remover</label>
                    </center>
                    
                    <label class="input" data-before="Sobre mim">{literal}
                        <quill-editor extra="{modules : { toolbar : [['bold', 'italic', 'underline', 'strike'], [{ 'color': [] }], ['link', 'code-block', 'blockquote']] }}" ng-model="loggedUser.aboutMe"></quill-editor>
                    {/literal}</label>
        

                    <label class="input" data-before="Nome do Perfil">
                        <input type="text" class="input" ng-model="loggedUser.name" required pattern="{$smarty.const.BRACP_REGEXP_NAME}"/>
                    </label>

                    <label class="input" data-before="Gênero">
                        <select class="input" ng-model="loggedUser.gender" required>
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                            <option value="O">Outros</option>
                        </select>
                    </label>

                    <label class="input" data-before="Quem pode ver meu perfil">
                        <select class="input" ng-model="loggedUser.visibility" required>
                            <option value="P">Todos</option>
                            <option value="F">Apenas amigos</option>
                            <option value="M">Somente eu</option>
                        </select>
                    </label>

                    <label class="input" data-before="Quem pode ver meu e-mail">
                        <select class="input" ng-model="loggedUser.showEmail" required>
                            <option value="P">Todos</option>
                            <option value="F">Apenas amigos</option>
                            <option value="M">Somente eu</option>
                        </select>
                    </label>

                    <label class="input" data-before="Quem pode ver minha data de aniversário">
                        <select class="input" ng-model="loggedUser.showBirthdate" required>
                            <option value="P">Todos</option>
                            <option value="F">Apenas amigos</option>
                            <option value="M">Somente eu</option>
                        </select>
                    </label>

                    <label class="input" data-before="Quem pode me enviar mensagens">
                        <select class="input" ng-model="loggedUser.allowMessage" required>
                            <option value="P">Todos</option>
                            <option value="F">Apenas amigos</option>
                            <option value="M">Ninguém</option>
                        </select>
                    </label>

                    {if $smarty.const.APP_FACEBOOK_ENABLED}
                        <label class="input" data-before="Quem pode ver meu facebook">
                            <select class="input" ng-model="loggedUser.showFacebook" required>
                                <option value="P">Todos</option>
                                <option value="F">Apenas amigos</option>
                                <option value="M">Somente eu</option>
                            </select>
                        </label>
                    {/if}

                    <div ng-show="loggedUser.gaAllowed">
                        <p class="message error">
                            O <strong>Google Authenticator</strong> Está ativo em sua conta.<br>
                            Nós não recomendamos que você remova, mas caso necessite, favor, clique abaixo.
                            <br><br>
                            <button class="button fill error" ng-click="profileRemoveGoogle()">
                                Remover <strong>Google Authenticator</strong>
                            </button>
                        </p>
                        <br>
                    </div>
                 </div>

            </div>
    </div>


{/block}