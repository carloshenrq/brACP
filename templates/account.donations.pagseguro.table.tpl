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

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/pagseguro/check" autocomplete="off" method="post" target=".donation-table" data-block="1">
    <table border="1" align="center" class="table">
        <caption style="text-align: right">
            <input class="btn" type="submit" value="Verificar"/>
            <input class="btn" type="reset" value="Limpar"/>
        </caption>
        <thead>
            <tr>
                <th align="right" width="50px">Cód.</th>
                <th align="center" width="80px">Data</th>
                <th align="center" width="80px">Status</th>
                <th align="right" width="100px">Valor R$</th>
                <th align="right" width="80px">Bônus</th>
                <th align="right" width="100px">Pago R$</th>
                <th align="left">Promoção</th>
                <th align="center">Bônus</th>
                <th align="center">Ação</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$donations item=row}
                <tr class="{if $row->getStatus() eq 'CANCELADO'}error{else if $row->getStatus() eq 'PAGO'}success{/if}">
                    <td align="right">{$row->getId()}</td>
                    <td align="center">{Format::date($row->getDate(), 'd/m/Y')}</td>
                    <td align="center">{$row->getStatus()}</td>
                    <td align="right">{Format::money($row->getValue())}</td>
                    <td align="right">{$row->getBonus()}</td>
                    <td align="right">{Format::money($row->getTotalValue())}</td>
                    <td align="left">
                        {if is_null($row->getPromotion()) eq false}
                            <span title="{$row->getPromotion()->getDescription()}">
                                {substr($row->getPromotion()->getDescription(), 0, 30)}...
                            </span>
                        {/if}
                    </td>
                    <td align="center">
                        {if $row->getReceiveBonus() eq true}
                            Sim
                        {else}
                            <strong>Não</strong>
                        {/if}
                    </td>
                    <td align="center" width="100px">
                        {if $row->getStatus() eq 'INICIADA' && !empty($row->getCheckoutCode())}
                            {if empty($row->getTransactionCode())}
                                <button class="btn btn-success btn-tiny donation-checkout" data-id="{$row->getId()}" data-checkout="{$row->getCheckoutCode()}" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations/transaction">
                                    Doar
                                </button>
                                <button class="btn btn-error btn-tiny donation-cancel" data-id="{$row->getId()}" data-checkout="{$row->getCheckoutCode()}" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations/transaction">
                                    Cancelar
                                </button>
                            {else}
                                <input type="checkbox" name="DonationID[]" value="{$row->getId()}"/>
                            {/if}
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</form>
