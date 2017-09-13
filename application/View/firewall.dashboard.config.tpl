{extends file="firewall.dashboard.tpl"}
{block name="App_Firewall_Location"}
    <li>Configurações</li>
{/block}

{block name="App_Firewall_Content"}

    <h1>Configurações</h1>

    <div ng-controller="config">
        <p>
            O Banco de dados do firewall, possui aproximadamente <strong><em>{$formatter->bytes2str($tables_totalSize)}</em></strong> de tamanho.
            <div class="message warning">
                Este tamanho é diferente do tamanho do arquivo de banco de dados, visto que as informações são comprimidas no arquivo.<br>
                O Tamanho acima representa o total de bytes utilizados para armazenar todas as informações.
            </div>
        </p>
    
        <div class="app-chart medium">
            <chart-js type="pie" title="Tamanho total por tabela" label-display="0" label="{base64_encode(json_encode(array_keys($tables)))}" value="{base64_encode(json_encode(array_values($tables)))}"></chart-js>
        </div>
        <table border="1">
            <thead>
                <tr>
                    <th align="left">Tabela</th>
                    <th align="right">Tamanho</th>
                    <th align="center">Ação</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$tables item=size key=table}
                    <tr>
                        <td align="left">{$table} ({sprintf('%.4f', ((intval($size)/intval($tables_totalSize))*100))}%)</td>
                        <td align="right">{$formatter->bytes2str(intval($size))}</td>
                        <td align="center">
                            <button class="button warning tiny" ng-click="cleanTable('{$table}')">Limpar</button>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

{/block}
