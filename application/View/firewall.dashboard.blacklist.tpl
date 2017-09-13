{extends file="firewall.dashboard.tpl"}
{block name="App_Firewall_Location"}
    <li>Lista Negra</li>
{/block}

{block name="App_Firewall_Content"}

    <h1>Lista Negra</h1>

    <div ng-controller="blacklist" ng-init="init('{$blackList}')">

        {* Aqui ficam as solicitações de entrada de novos endereços ips de forma manual *}
        <form ng-submit="add()">

            <p class="message info">
                Aqui, você pode adicionar novos endereços ips a lista negra para que o acesso deles, seja negado.
            </p>

            <label class="input" data-before="Endereço IP">
                <input type="text" class="input" ng-model="form.ipAddress" placeholder="Endereço IP para ser bloqueado" required/>
            </label>
            <label class="input" data-before="Motivo">
                <input type="text" class="input" ng-model="form.reason" placeholder="Motivo para o bloqueio" required/>
            </label>
            <label class="input" data-before="Tempo de Bloqueio">{literal}
                <input type="text" class="input" ng-model="form.time" placeholder="Tempo para bloqueio em segundos. Bloqueio permanente digite -1" pattern="^[0-9]{1,6}|-1$" required/>
            {/literal}</label>

            <div class="form-buttom">
                <button class="button success">Adicionar</button>
                <input class="button info" type="reset" value="Limpar"/>
            </div>

        </form>

        <br>
        <br>
        {* Daqui para baixo fica a área onde os ips serão exibidos. *}

        <div class="message warning" ng-show="list.length == 0">
            Não existem entradas na lista negra.
        </div>

        <div ng-show="list.length > 0">
            <table border="0" ng-show="list.length > 0">
                <caption>{literal}Existe(m) {{list.length}} endereço(s) com acesso bloqueado{/literal}</caption>
                <thead>
                    <tr>
                        <th align="left">Endereço</th>
                        <th align="left">Motivo</th>
                        <th align="left">Bloqueado em</th>
                        <th align="left">Libera em</th>
                        <th align="center">Ação</th>
                    </tr>
                </thead>
                <tbody>{literal}
                    <tr ng-repeat="entry in list">
                        <td align="left">{{entry.Address}}</td>
                        <td align="left">{{entry.Reason}}</td>
                        <td align="left">
                            {{entry.TimeBlocked}}
                        </td>
                        <td align="left">
                            {{entry.TimeExpire}}
                        </td>
                        <td align="center">
                            <button class="button error tiny" ng-click="free(entry.BlacklistID)">Liberar</button>
                        </td>
                    </tr>
                {/literal}</tbody>
            </table>
        </div>
    </div>

{/block}
