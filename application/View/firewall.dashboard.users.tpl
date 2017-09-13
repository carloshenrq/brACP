{extends file="firewall.dashboard.tpl"}
{block name="App_Firewall_Location"}
    <li>Usuários</li>
{/block}

{block name="App_Firewall_Content"}

    <h1>Usuários</h1>

    <div ng-controller="users" ng-init="init('{$users}', {$loggedUserID})">

        <form ng-submit="add()">

            <p class="message info">
                Aqui, você pode adicionar novos usuários para acesso ao firewall.
            </p>
            {literal}
                <label class="input" data-before="Usuário">
                    <input type="text" class="input" ng-model="form.user" placeholder="Nome de usuário a ser utilizado" pattern="^[a-zA-ZÀ-ú0-9]{3}[a-zA-ZÀ-ú0-9\s]{1,20}$" required/>
                </label>
                <label class="input" data-before="Senha" data-after="Deve ter no mínimo 6 caracteres e possuir pelo menos: 1 Letra (inclui espaços), 1 Número e 1 Caractere Especial ($%@)">
                    <input type="password" class="input" ng-model="form.pass" placeholder="Senha do usuário" pattern="^((?=.*\d)(?=.*[a-zA-Z\s])(?=.*[@#$%])[a-zA-Z0-9\s@$$%]{6,})$" required/>
                </label>
            {/literal}
            <div class="form-buttom">
                <button class="button success">Salvar</button>
                <input class="button info" type="reset" value="Limpar"/>
            </div>

        </form>


        <div class="message warning" ng-show="list.length == 0">
            Nenhum usuário cadastrado
        </div>

        <div ng-show="list.length > 0">
            <table border="0" ng-show="list.length > 0">
                <caption>{literal}Existe(m) {{list.length}} usuário(s) cadastrados(s){/literal}</caption>
                <thead>
                    <tr>
                        <th align="left">Cód.</th>
                        <th align="left">Usuário</th>
                        <th align="left">Senha</th>
                        <th align="left">Vezes Logadas</th>
                        <th align="left">Habilitado</th>
                        <th align="center">Ação</th>
                    </tr>
                </thead>
                <tbody>{literal}
                    <tr ng-repeat="entry in list">
                        <td align="left">{{entry.UserID}}</td>
                        <td align="left">{{entry.User}}</td>
                        <td align="left">{{entry.UserPass.toUpperCase()}}</td>
                        <td align="left">{{entry.LoginCount}}</td>
                        <td align="left">
                            <label class="label" ng-class="(entry.LoginEnabled == 1 ? 'label-success':'label-error')">
                                {{entry.LoginEnabled == 1 ? 'Sim' : 'Não'}}
                            </label>
                        </td>
                        <td align="center">
                            <button class="button tiny warning" ng-click="edit(entry)">Editar</button>
                            <button class="button tiny success" ng-show="entry.LoginEnabled == 0" ng-click="enableDisable(entry, 1)">Habilitar</button>
                            <button class="button tiny error" ng-show="entry.LoginEnabled == 1 && entry.UserID != loggedUserID" ng-click="enableDisable(entry, 0)">Desabilitar</button>
                        </td>
                    </tr>
                {/literal}</tbody>
            </table>
        </div>

    </div>

{/block}