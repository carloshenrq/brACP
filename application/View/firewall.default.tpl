{*
    Este é apenas um arquivo de layout exemplo para
    Utilização do framework

    ->


*}
<!DOCTYPE html>
<html>
    <head>
        <script src="{$smarty.const.APP_URL_PATH}/asset/js"></script>
        <link rel="stylesheet" type="text/css" href="{$smarty.const.APP_URL_PATH}/asset/css"/>

        <title>Firewall</title>
    </head>
    <body ng-app="app-firewall">
        <div class="app-grid">
            {block name="App_Firewall_Body"}
            <div class="app-grid-row">
                <div class="app-grid-column-2"></div>
                <div class="app-grid-column-8">
                    {block name="App_Firewall_Content"}{literal}
                        teste
                    {/literal}{/block}
                </div>
                <div class="app-grid-column-2"></div>
            </div>
            {/block}
        </div>
        <input id="APP_LOCATION" type="hidden" value="{$smarty.const.APP_URL_PATH}"/>
    </body>
</html>
