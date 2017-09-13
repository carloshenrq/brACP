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
    <li>Acessos ao Jogo</li>
{/block}
{block name="brACP_Container"}
   

    <div class="game-access" ng-controller="game-access" ng-init="init('{$accountCount}', '{$servers}', '{$serverSelected}')">



        <div style="width: 60%; margin: 0 auto;">
            <div class="game-top-actions">
                {if $smarty.const.BRACP_RAG_ACCOUNT_CREATE eq true}
                    <button class="button" ng-click="createAccess()" {if $smarty.const.BRACP_RAG_ACCOUNT_LIMIT > 0 && $smarty.const.BRACP_RAG_ACCOUNT_LIMIT <= $accountCount}disabled{/if}>Criar</button>
                {/if}
                <button class="button info" ng-click="linkAccess()" {if $smarty.const.BRACP_RAG_ACCOUNT_LIMIT > 0 && $smarty.const.BRACP_RAG_ACCOUNT_LIMIT <= $accountCount}disabled{/if}>Vincular</button>
            </div>
            <div class="message warning" ng-show="accounts == 0">
                Você não possui nenhum acesso ao jogo.
            </div>
            {if $accountCount > 0}
                <table border="1">
                    <thead>
                        <tr>
                            <th colspan="5">
                                Você possui <strong>{$accountCount}</strong> {if $smarty.const.BRACP_RAG_ACCOUNT_LIMIT > 0}de <strong>{$smarty.const.BRACP_RAG_ACCOUNT_LIMIT}</strong> {/if}acessos vinculados
                            </th>
                        </tr>
                        <tr>
                            <th align="right">Cód.</th>
                            <th align="left">Usuário</th>
                            <th align="left">Gênero</th>
                            <th align="left">Vinculado em:</th>
                            <th align="center">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$accounts item=account}
                            <tr>
                                <td align="right">{$account->account_id}</td>
                                <td align="left">{$account->userid}</td>
                                <td align="left">{$formatter->gender($account->sex)}</td>
                                <td align="left">{utf8_encode($formatter->date($account->verifyDt))}</td>
                                <td align="center">
                                    <button class="button small warning" ng-click="changePass({$account->account_id})">Alterar Senha</button>
                                    <button class="button small info" ng-click="charManage({$account->account_id})">Personagens</button>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {/if}
        </div>

    </div>


{/block}
