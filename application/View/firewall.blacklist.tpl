<!DOCTYPE html>
<html>
    <head>
        <title>FIREWALL - BlackList</title>
    </head>
    <body>
        <h1>Acesso Negado</h1>

        <p>
            Seu endereço ip <em><strong>{$ipAddress}</strong></em> possui <em><strong>{count($blackList)}</strong></em> entrada(s) em nossa lista negra e por isso foi barrado.<br>
            Você pode entrar em contato com o administrador para solicitar um desbloqueio, mas por hora, ficará com seu acesso negado.
        </p>

        <p>
            Abaixo, segue os detalhes de todas as suas entradas na lista negra.
        </p>

        <table border="1" style="border-collapse: collapse;" cellpadding="3">
            <thead>
                <tr>
                    <th>Motivo</th>
                    <th>Bloqueado em</th>
                    <th>Exipira em</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$blackList item=entry}
                    <tr>
                        <td>{$entry->Reason}</td>
                        <td>{date('d/m/Y H:i:s', $entry->TimeBlocked)}</td>
                        <td>{if $entry->Permanent eq true}Nunca{else}{date('d/m/Y H:i:s', $entry->TimeExpire)}{/if}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </body>
</html>
