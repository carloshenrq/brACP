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

{extends file="default.tpl"}
{block name="brACP_Title"}Fatal Error{/block}
{block name="brACP_Body"}
    <div class="exception">
        <div class="exception-container">
            <div class="exception-header">
                Um erro fatal aconteceu durante a execução do sistema
            </div>
            <div class="exception-body">
                Ocorreu um erro de execução e o sistem precisou ser interrompido e por não poder continuar em execução!<br>
                {if $smarty.const.BRACP_DEVELOP_MODE eq true}
                    <div class="exception-details">
                        <div class="exception-item">
                            <div class="exception-item-header">Mensagem (Cód: {$ex->getCode()})</div>
                            <div class="exception-item-body">{$ex->getMessage()}</div>
                        </div>
                        <div class="exception-item">
                            <div class="exception-item-header">Arquivo (Linha: {$ex->getLine()})</div>
                            <div class="exception-item-body">{$ex->getFile()}</div>
                        </div>
                        <div class="exception-item">
                            <div class="exception-item-header">Origem</div>
                            <div class="exception-item-body"><pre>{$ex->getTraceAsString()}</pre></div>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="exception-footer">
                <strong>Data do servidor:</strong> {date('d/m/Y H:i:s - e')}
            </div>
        </div>
    </div>
{/block}