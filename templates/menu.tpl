<ul>
    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}" data-target=".bracp-body">Principal</li>
    {if isset($smarty.session.BRACP_ISLOGGEDIN) eq false or $smarty.session.BRACP_ISLOGGEDIN eq false}
        <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register" data-target=".bracp-body">Criar Conta</li>
        <li>Minha Conta
            <ul data-back="Minha Conta">
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login" data-target=".bracp-body">Entrar</li>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover" data-target=".bracp-body">Recuperar</li>
            </ul>
        </li>
    {else}
        <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/loggout" data-target=".bracp-body">Sair ({$smarty.session.BRACP_USERID})</li>
    {/if}
    <li>Rankings
        <ul data-back="Rankings">
            <li>Guerra do Emperium
                <ul data-back="Guerra do Emperium">
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/woe/guilds" data-target=".bracp-body">Clãs</li>
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/woe/gvg" data-target=".bracp-body">Gladiadores</li>
                    <li>Castelos
                        <ul data-back="Castelos">
                            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/woe/castle" data-target=".bracp-body">Geral</li>
                            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/woe/castle/economy" data-target=".bracp-body">Econômia</li>
                            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/woe/castle/defense" data-target=".bracp-body">Defesa</li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li>Personagens
                <ul data-back="Personagens">
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars" data-target=".bracp-body">Geral</li>
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars/economy" data-target=".bracp-body">Econômia</li>
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}rankings/chars/pvp" data-target=".bracp-body">Player vs Player (PvP)</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>Sobre
        <ul data-back="Sobre">
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}about/staff" data-target=".bracp-body">Equipe</li>
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}about/brAthena" data-target=".bracp-body">brAthena</li>
        </ul>
    </li>
</ul>
