<!DOCTYPE html>

<html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1.0">

        <style>{$css}</style>
    </head>

    <body>
        <div class="mail-container">
            <div class="mail-header">
                {$subject}
            </div>

            <div class="mail-body">
                {block name="App_Mail_Body"}
                    Aqui vai o corpo do endereço de e-mail
                {/block}
            </div>

            <div class="mail-footer"><em>
                Este e-mail foi enviado por <strong><a href="{$urlSender}{$smarty.const.APP_URL_PATH}" target="_blank">{$urlSender}{$smarty.const.APP_URL_PATH}</a></strong> na data de <strong>{$formatter->date(date('Y-m-d H:i:s'), true)} GMT{date('P')}{if date('I') eq 1} (horário de verão){/if}.</strong>
                Solicitação pelo endereço ip <strong>{$ipAddress}</strong>
            </em></div>
        </div>
    </body>

</html>
