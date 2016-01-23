{**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2015  brAthena, CHLFZ
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *}

<h1>Minha Conta &raquo; Personagens</h1>

{if count($chars) eq 0}
    <p class="bracp-message-warning">
        Você não possui nenhum personagem criado. Realize login no jogo e crie seu personagem.
    </p>
{else}

{if count($appear) > 0}
    <p class="bracp-message-success">
        A aparência do(s) personagem(ns) <strong>{implode(', ', $appear)}</strong> foi(ram) resetada(s) com sucesso!
    </p>
{/if}
{if count($posit) > 0}
    <p class="bracp-message-success">
        A posição do(s) personagem(ns) <strong>{implode(', ', $posit)}</strong> foi(ram) resetada(s) com sucesso!
    </p>
{/if}
{if count($equip) > 0}
    <p class="bracp-message-success">
        Os equipamentos do(s) personagem(ns) <strong>{implode(', ', $equip)}</strong> foi(ram) resetada(s) com sucesso!
    </p>
{/if}

<p>
    Segue abaixo a lista dos seus personagens.
</p>

<p class="bracp-message-warning">
    <strong>OBS.:</strong>
    <i>Personagens que estiverem online, não serão resetados. Por favor, faça logout do jogo para resetar o personagem.</i>
</p>

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/chars" autocomplete="off" method="post" target=".bracp-body">
    <table border="1" align="center" class="bracp-table">
        <caption style="padding: 2px; text-align: right">
            <input type="submit" value="Enviar"/>
            <input type="reset" value="Limpar"/>
        </caption>
        <thead>
            <tr class="tiny">
                <th rowspan="2" align="right">Cód.</th>
                <th rowspan="2" align="left">Nome</th>
                <th rowspan="2" align="left">Classe</th>
                <th rowspan="2" align="right">Nível</th>
                <th rowspan="2" align="center">Localização</th>
                <th rowspan="2" align="center">Ponto de Retorno</th>
                <th rowspan="2" align="center">Online</th>
                {if $resetCount > 0}
                    <th colspan="{$resetCount}" align="center">Resetar</th>
                {/if}
            </tr>
            <tr class="tiny">
                {if $resetCount > 0}
                    {if $smarty.const.BRACP_ALLOW_RESET_APPEAR eq true}
                        <th align="center">Aparência</th>
                    {/if}
                    {if $smarty.const.BRACP_ALLOW_RESET_POSIT eq true}
                        <th align="center">Posição</th>
                    {/if}
                    {if $smarty.const.BRACP_ALLOW_RESET_EQUIP eq true}
                        <th align="center">Equip</th>
                    {/if}
                {/if}
            </tr>
        </thead>
        <tbody>
            {foreach from=$chars item=char}
                <tr>
                    <td align="right">{$char->getChar_id()}</td>
                    <td align="left">{$char->getName()}</td>
                    <td align="left">{$char->getClass()}</td>
                    <td align="right">{$char->getBase_level()}/{$char->getJob_level()}</td>
                    <td align="left">{$char->getLast_map()} ({$char->getLast_x()}, {$char->getLast_y()})</td>
                    <td align="left">{$char->getSave_map()} ({$char->getSave_x()}, {$char->getSave_y()})</td>
                    <td align="center">{if $char->getOnline() eq true}Sim{else}Não{/if}</td>
                    {if $resetCount > 0}
                        {if $smarty.const.BRACP_ALLOW_RESET_APPEAR eq true}
                            <td align="center"><input type="checkbox" name="char_id_appear[]" value="{$char->getChar_id()}" {if $char->getOnline() eq true}disabled{/if} /></td>
                        {/if}
                        {if $smarty.const.BRACP_ALLOW_RESET_POSIT eq true}
                            <td align="center"><input type="checkbox" name="char_id_posit[]" value="{$char->getChar_id()}" {if $char->getOnline() eq true}disabled{/if} /></td>
                        {/if}
                        {if $smarty.const.BRACP_ALLOW_RESET_EQUIP eq true}
                            <td align="center"><input type="checkbox" name="char_id_equip[]" value="{$char->getChar_id()}" {if $char->getOnline() eq true}disabled{/if} /></td>
                        {/if}
                    {/if}
                </tr>
            {/foreach}
        </tbody>
    </table>
</form>

{/if}