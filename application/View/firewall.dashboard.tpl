{extends file="firewall.default.tpl"}
{block name="App_Firewall_Body"}


    <div class="app-firewall-top app-grid-row" ng-controller="top">

        <div class="app-grid-column-2">
        </div>

        <div class="app-firewall-items app-grid-column-8">
            <a href="{$smarty.const.APP_URL_PATH}/firewall/admin/dashboard" class="button link">Principal</a>
            <a href="{$smarty.const.APP_URL_PATH}/firewall/admin/dashboard/blacklist" class="button link">Lista Negra</a>
            <a href="{$smarty.const.APP_URL_PATH}/firewall/admin/dashboard/rules" class="button link">Regras de Bloqueio</a>
            <a href="{$smarty.const.APP_URL_PATH}/firewall/admin/dashboard/requests" class="button link">Requisições</a>
            <a href="{$smarty.const.APP_URL_PATH}/firewall/admin/dashboard/users" class="button link">Usuários</a>
            <a href="{$smarty.const.APP_URL_PATH}/firewall/admin/dashboard/config" class="button link">Configurações</a>
        </div>

        <div class="app-grid-column-2">
            <button class="button error" ng-click="logout()">Encerrar</button>
        </div>

    </div>

    <div class="app-grid-row">
        <div class="app-grid-column-2"></div>
        <div class="app-firewall-container app-grid-column-8">
            <div class="app-firewall-body">
                <ul class="app-firewall-location">
                    <li>Gerenciamento Interno</li>
                    {block name="App_Firewall_Location"}{/block}
                </ul>

                <div class="app-firewall-content">{block name="App_Firewall_Content"}

                    <h1>Principal</h1>

                    <p>
                        Olá! Seja muito bem-vindo ao gerenciamento interno do sistema. Aqui você será capaz de configurar o sistema, assim como gerenciar dados do firewall, como manipular endereços ips a lista negra bem como regras do firewall.
                    </p>

                    <div class="message warning">
                        Quando alguma requisição for verdadeira contra as regras do firewall, ela será <strong>{if $smarty.const.APP_FIREWALL_RULE_CONFIG eq true}permitida{else}bloqueada e adicionada a lista negra{/if}</strong>.<br>
                        Caso você deseje mudar esta configuração, procure por <strong>APP_FIREWALL_RULE_CONFIG</strong> e inverta o valor atual no seu arquivo de configuração.
                    </div>

                    <p>
                        Você pode começar utilizando os menus ali cima.<br>
                        Boa sorte.
                    </p>

                    <div class="app-chart medium">
                        <chart-js type="pie" title="Países por Endereço IP" label-display="0" label="{$graph_labels}" value="{$graph_data}"></chart-js>
                    </div>

                    <p>
                        O Gráfico acima, é um pequeno demonstrativo das requisições recebidas pelo sistema.
                        <div class="message info">Os dados acima são informações reais.</div>
                    </p>

                {/block}</div>

            </div>
        </div>
        <div class="app-grid-column-2"></div>
    </div>

{/block}