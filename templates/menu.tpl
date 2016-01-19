<ul>
    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}" data-target=".bracp-body">Principal</li>
    {if isset($smarty.session.BRACP_ISLOGGEDIN) eq false or $smarty.session.BRACP_ISLOGGEDIN eq false}
        {if $smarty.const.BRACP_ALLOW_CREATE_ACCOUNT eq true}
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/register" data-target=".bracp-body">Criar Conta</li>
        {/if}
        <li>Minha Conta
            <ul data-back="Minha Conta">
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/login" data-target=".bracp-body">Entrar</li>
                {if $smarty.const.BRACP_ALLOW_RECOVER eq true}
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/recover" data-target=".bracp-body">Recuperar</li>
                {/if}
            </ul>
        </li>
    {else}
        {if $smarty.const.BRACP_ALLOW_ADMIN eq true and $acc_gmlevel gte $smarty.const.BRACP_ALLOW_ADMIN_GMLEVEL}
            <li>
                Administração
                <ul data-back="Minha Conta">
                    <li>Carregando.</li>
                </ul>
            </li>
        {/if}
        <li>Minha Conta
            <ul data-back="Minha Conta">
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/password" data-target=".bracp-body">Alterar Senha</li>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/change/mail" data-target=".bracp-body">Alterar Email</li>
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" data-target=".bracp-body">Personagens</li>
                {if $smarty.const.PAG_INSTALL eq true}
                    <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations" data-target=".bracp-body">Doações</li>
                {/if}
                <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/logout" data-target=".bracp-body">Sair ({$smarty.session.BRACP_USERID})</li>
            </ul>
        </li>
    {/if}
    {if $smarty.const.BRACP_ALLOW_RANKING eq true}
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
    {/if}
    <li>Sobre
        <ul data-back="Sobre">
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}about/staff" data-target=".bracp-body">Equipe</li>
            <li class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}about/brAthena" data-target=".bracp-body">brAthena</li>
        </ul>
    </li>
</ul>
