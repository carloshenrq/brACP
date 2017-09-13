{*

*}
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">

        <title>FIREWALL - {$ipAddress}</title>
    </head>
    <body>
        <h1>Detalhamento para as 100 últimas requisições para seu endereço de IP.</h1>

        <p>Na listagem abaixo, segue o detalhamento para as 100 últimas requisições vindas do seu endereço de IP <em><strong>{$ipAddress}</strong></em>.</p>
    
        <table width="100%" border="1" style="border-collapse: collapse;">
            <thead>
                <tr>
                    <th align="center">Ord.</th>
                    <th align="center">Endereço</th>
                    <th align="center">Tipo</th>
                    <th align="center">Caminho Acessado</th>
                    <th align="center">Data de Requisição</th>
                    <th align="center">Código da Sessão</th>
                    <th align="center">Agente</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$ipLogDetails item=entry key=key}
                    <tr>
                        <td align="right">{(count($ipLogDetails) - $key)}</td>
                        <td align="center">{$entry->Address}</td>
                        <td align="center">{$entry->Method}</td>
                        <td align="center">{$entry->URI}</td>
                        <td align="center">{date('d/m/Y H:i:s', intval($entry->RequestTime))}</td>
                        <td align="center">{$entry->PHPSession}</td>
                        <td align="center">{$entry->UserAgent}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </body>
</html>
