<ul>
    <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}">Principal</li>
    {if isset($smarty.session.BRACP_ISLOGGEDIN) eq false or $smarty.session.BRACP_ISLOGGEDIN eq false}
        <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register">Criar Conta</li>
        <li>Minha Conta
            <ul data-back="Minha Conta">
                <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login">Entrar</li>
                <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover">Recuperar</li>
            </ul>
        </li>
    {else}
        <li class="bracp-link" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/loggout">Sair ({$smarty.session.BRACP_USERID})</li>
    {/if}
    <li>Rankings
        <ul data-back="Rankings">
            <li>Guerra do Emperium
                <ul data-back="Guerra do Emperium">
                    <li>Clãs</li>
                    <li>Castelos
                        <ul data-back="Castelos">
                            <li>Geral</li>
                            <li>Econômia</li>
                            <li>Defesa</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>Personagens
                <ul data-back="Personagens">
                    <li>Geral</li>
                    <li>Econômia</li>
                    <li>Player vs Player (PvP)</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>Sobre
        <ul data-back="Sobre">
            <li>Equipe</li>
            <li>brAthena</li>
        </ul>
    </li>
</ul>
