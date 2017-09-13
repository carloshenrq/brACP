{extends file="firewall.dashboard.tpl"}
{block name="App_Firewall_Location"}
    <li>Requisições</li>
{/block}

{block name="App_Firewall_Content"}

    <h1>Requisições</h1>

    <div ng-controller="requests">

        <p class="message info">
            De acordo com os dados das requisições salvas, já foram transferidos cerca de <strong><em>{$formatter->bytes2str($trafficBytes)}</em></strong> de dados.<br>
            <em>* Se você realizar a limpeza da tabelas de requisições, a contagem acima pode não ser correta.</em>
        </p>

        <p>No gráfico abaixo, você pode visualizar o 10 paises que mais fazem requisições ao sistema.</p>
        <div class="app-chart medium">
            <chart-js type="pie" title="TOP 10 - Requisições por Paises" label-display="0" label="{base64_encode(json_encode(array_keys($graph_country)))}" value="{base64_encode(json_encode(array_values($graph_country)))}"></chart-js>
        </div>
        <table border="1">
            <caption>TOP 10 - Requisições por Paises - Tabela</caption>
            <thead>
                <tr>
                    <th align="left">País</th>
                    <th align="right">Quantidade de Acessos</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$graph_country item=uri_value key=uri_key}
                    <tr>
                        <td align="left">{$uri_key}</td>
                        <td align="right">{$uri_value}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

        <p>No gráfico abaixo, você pode visualizar o 10 ips que mais fazem requisições ao sistema.</p>
        <div class="app-chart medium">
            <chart-js type="pie" title="TOP 10 - Requisições por IP" label-display="0" label="{base64_encode(json_encode(array_keys($graph_data)))}" value="{base64_encode(json_encode(array_values($graph_data)))}"></chart-js>
        </div>
        <table border="1">
            <caption>TOP 10 - Requisições por IP - Tabela</caption>
            <thead>
                <tr>
                    <th align="left">Endereço IP</th>
                    <th align="right">Quantidade de Acessos</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$graph_data item=uri_value key=uri_key}
                    <tr>
                        <td align="left">{$uri_key}</td>
                        <td align="right">{$uri_value}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

        <p>No gráfico abaixo, você pode visualizar os 20 URI mais acessados do sistema.</p>
        <div class="app-chart medium">
            <chart-js type="pie" title="TOP 20 - Requisições por URI" label-display="0" label="{base64_encode(json_encode(array_keys($graph_uri)))}" value="{base64_encode(json_encode(array_values($graph_uri)))}"></chart-js>
        </div>
        <table border="1">
            <caption>TOP 20 - Requisições por URI - Tabela</caption>
            <thead>
                <tr>
                    <th align="left">URI</th>
                    <th align="right">Quantidade de Acessos</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$graph_uri item=uri_value key=uri_key}
                    <tr>
                        <td align="left">{$uri_key}</td>
                        <td align="right">{$uri_value}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

        <p>No gráfico abaixo, você pode conferir os navegadores mais utilizados para acesso ao sistema.</p>
        <div class="app-chart medium">
            <chart-js type="pie" title="Navegadores por Acesso" label-display="0" label="{base64_encode(json_encode(array_keys($graph_userAgent)))}" value="{base64_encode(json_encode(array_values($graph_userAgent)))}"></chart-js>
        </div>
        <table border="1">
            <caption>Navegadores por Acesso - Tabela</caption>
            <thead>
                <tr>
                    <th align="left">Navegador</th>
                    <th align="right">Quantidade de Acessos</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$graph_userAgent item=uri_value key=uri_key}
                    <tr>
                        <td align="left">{$uri_key}</td>
                        <td align="right">{$uri_value}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

    </div>

{/block}