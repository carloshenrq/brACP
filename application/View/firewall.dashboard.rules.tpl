{extends file="firewall.dashboard.tpl"}
{block name="App_Firewall_Location"}
    <li>Regras de Bloqueio</li>
{/block}

{block name="App_Firewall_Content"}
    <h1>Regras de Bloqueio</h1>

    <div ng-controller="rules" ng-init="init('{$rules}')">
        
        <form ng-submit="add()">
            <div class="message info">
                A função de bloqueio deve ser um callback em PHP que recebe os seguintes parâmetros:<br>
                <ol>
                    <li>$ipData
                        <ul>
                            <li><strong>Tipo:</strong> object</li>
                            <li><em>Contém informações sobre o endereço de ip que está realizando a requisição.</em></li>
                            <li><em>Address, Hostname, City, Region, Country, Location, Origin</em></li>
                        </ul>
                    </li>
                    <li>$rule
                        <ul>
                            <li><strong>Tipo:</strong> object</li>
                            <li><em>Objeto que contém os dados da regra que estão no banco de dados.</em></li>
                        </ul>
                    </li>
                </ol>

                <pre class="message">
/**
 * Função simples para explicação de como deve ser inserido no campo.
 *
 * @param object $ipData Dados que existem no endereço ip.
 * @param object $rule Dados informativos da regra a ser aplicada.
 *
 * @return boolean O Retorno dependende de 'APP_FIREWALL_RULE_CONFIG', se o retorno desta função
 *                 for igual ao da configuração, então a requisição não será bloqueada.
 *                 Se for diferente, então, a requisição será bloqueada e o endereço ip da requisição será
 *                 adicionado a lista negra.
 */
function($ipData, $rule)
{
    // APP_FIREWALL_RULE_CONFIG = true, Irá permitir apenas conexões vindas do Brasil
    // APP_FIREWALL_RULE_CONFIG = false, Irá bloquear as conexões vindas do Brasil
    return ($ipData->Country == 'BR');
}
                </pre>

            </div>

            <label class="input" data-before="Motivo">
                <input type="text" class="input" ng-model="form.reason" placeholder="Justificativa para lista negra quando a requisição for bloqueada" required/>
            </label>

            <label class="input" data-before="Tempo de Bloqueio">{literal}
                <input type="text" class="input" ng-model="form.expire" placeholder="Tempo em segundos para permanecer na lista negra após bloqueio. -1 Para eterno" pattern="^[0-9]{1,6}|-1$" required/>
            {/literal}</label>

            <label class="input" data-before="Habilitada">
                <select class="input" ng-model="form.enabled">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </label>

            <label class="input" data-before="Regra">
                <textarea class="input" ng-model="form.code" rows="10"></textarea>
            </label>

            <div class="form-buttom">
                <button class="button success">Enviar</button>
                <input class="button info" type="reset" value="Limpar"/>
            </div>

        </form>

        <div class="message warning" ng-show="list.length == 0">
            Não existem regras configuradas.
        </div>

        <div ng-show="list.length > 0">
            <table border="0" ng-show="list.length > 0">
                <caption>{literal}Existe(m) {{list.length}} regra(s) adicionada(s){/literal}</caption>
                <thead>
                    <tr>
                        <th align="left">Cód.</th>
                        <th align="left">Motivo</th>
                        <th align="left">Tempo de Bloqueio</th>
                        <th align="left">Habilitada</th>
                        <th align="center">Ação</th>
                    </tr>
                </thead>
                <tbody>{literal}
                    <tr ng-repeat="entry in list">
                        <td align="left">{{entry.RuleID}}</td>
                        <td align="left">{{entry.RuleReason}}</td>
                        <td align="left">{{(entry.RuleExpire == -1 ? 'Eterno' : entry.RuleExpire)}}</td>
                        <td align="left">
                            <label class="label" ng-class="(entry.RuleEnabled == 1 ? 'label-success':'label-error')">
                                {{entry.RuleEnabled == 1 ? 'Sim' : 'Não'}}
                            </label>
                        </td>
                        <td align="center">
                            <button class="button tiny warning" ng-click="edit(entry)">Editar</button>
                            <button ng-show="entry.RuleEnabled == 1" class="button tiny error" ng-click="disable(entry)">Desabilitar</button>
                            <button ng-show="entry.RuleEnabled == 0" class="button tiny success" ng-click="enable(entry)">Habilitar</button>
                        </td>
                    </tr>
                {/literal}</tbody>
            </table>
        </div>

    </div>

{/block}
