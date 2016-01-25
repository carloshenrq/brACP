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

{if $donationStart eq true}
<script>
    PagSeguroLightbox({
        'code' : '{$checkoutCode}'
    }, {
        'success' : function(transactionCode) {
            $.ajax({
                'url'       : '{$smarty.const.BRACP_DIR_INSTALL_URL}/account/donations/transactions',
                'method'    : 'post',
                'data'      : { 'donationId' : '{$donation->getId()}', 'transactionCode' : transactionCode },
                'async'     : false,
                'success'   : function() {
                    // Recarrega a página atual.
                    window.location.reload();
                }
            });
        }
    });
</script>
{/if}

<h1>Minha Conta &raquo; Doações</h1>

{if $donationStart eq true}
    <p class="bracp-message-success">
        Obrigado por iniciar o processo de doação! Ficamos muito felizes em receber sua ajuda!
    </p>
{/if}

<p>Você sabia que suas doações ajudam a manter o servidor no ar? Todos custos que temos de hospedagem,
     desenvolvimento e equipe são vocês que nos ajudam a pagar através de suas doações.
</p>

<p class="bracp-message-warning">
    Gostariamos de lembrar que <strong><i>todas as doações são por livre e expontânea vontade</i></strong>,
     o jogo é gratuito e você doa apenas se você quiser colaborar com o crescimento do servidor!
</p>

<p>
	Como forma de agradecimento a sua doação, iremos presentar você com uma quantia de <strong><i>Bônus Eletrônico</i></strong> caso seja sua vontade receber-los.
</p>

<p class="bracp-message-warning">
    Lembre-se: Para cada <strong>R$ 1,00</strong> que você doar, você é prêmiado com <strong>{$amountBonus} {if $amountPromo > 0}(+{$amountPromo}){/if}</strong> em Bônus Eletrônico.
</p>


{if is_null($promotion) eq false}
<p class="bracp-message-success">
    <strong>Promoção:</strong> <i>{$promotion->getDescription()}</i>.<br>
    <strong>Periodo:</strong> <i>{$promotion->getStartDate()}</i> até <i>{$promotion->getEndDate()}</i><br>
    <strong>Bônus extra a cada R$ 1,00:</strong> <i>{$amountPromo}</i>.<br>
    <br>
    <i>*Promoção válida <strong><u>SOMENTE</u></strong> para todas as doações <u>INICIADAS</u> dentro do periodo indicado.</i>
</p>
{/if}

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations" autocomplete="off" method="post" target=".bracp-body">
    <div class="bracp-form" style="width: 400px">
        <div class="bracp-form-field">
            <label>
                Valor da Doação R$:<br>
                <input type="text" id="donation" name="donation" class="number bracp-donation-calc" data-rates="{$smarty.const.DONATION_AMOUNT_USE_RATE}" data-multiply="{$amountBonus+$amountPromo}" data-target="#bonus" placeholder="Valor da doação" min="0" step="1.00" size="20" value="0.00"/>
            </label>
            <br>
            <label>
                Valor em Bônus Eletrônico:<br>
                <input type="text" id="bonus" name="bonus" class="number bracp-bonus-calc" data-rates="{$smarty.const.DONATION_AMOUNT_USE_RATE}" data-multiply="{$amountBonus+$amountPromo}" data-target="#donation" placeholder="Valor em bônus" min="0" size="20" value="0" data-places="0"/>
            </label>
        </div>
        {if $smarty.const.DONATION_AMOUNT_USE_RATE eq true}
	        <div class="bracp-form-field" style="display: {if $smarty.const.DONATION_AMOUT_SHOW_RATE_CALC eq true}block{else}none{/if};">
	            <label>
	                Valor cobrado:<br>
	                <input type="text" id="cobrado" name="cobrado" class="number bracp-cobrado-calc" value="0.00" data-places="2" title="Valor que será cobrado na fatura." readonly/>
	            </label>
	            {if $smarty.const.DONATION_AMOUT_SHOW_RATE_CALC eq true}
		            <p class="bracp-message-warning">
		            	<strong><i>Valor R$ = ((Doação + R$ 0,40) / (100%-3.99%))</i></strong>
		            </p>
		        {/if}
	        </div>
        {/if}
        <div class="bracp-form-field">
            <label>
                <input type="checkbox" id="nobonus" name="nobonus" value="1"/>
                Eu não desejo receber os bônus eletrônicos desta doação.
            </label>
            <label>
                <input type="checkbox" id="ciente" name="ciente" required/>
                Eu entendi que <strong>isso é uma doação</strong> e <strong>não uma compra</strong>.
            </label>

            <div class="bracp-form-submit">
                <input type="submit" value="Doar"/>
                <input type="reset" value="Resetar"/>
            </div>
        </div>
    </div>
    {if is_null($promotion) eq false}
        <input type="hidden" name="PromotionID" value="{$promotion->getId()}"/>
    {/if}
</form>

{if count($donations) eq 0}
    <p class="bracp-message-warning">
        Você não realizou nenhuma doação dentro dos ultimos 60 dias.
    </p>
{else}
    <br>
    <table border="1" align="center" class="bracp-table">
        <caption class="bracp-message-warning">Você possui <strong>{min(30, count($donations))}</strong> doações nos últimos 60 dias</caption>
        <thead>
            <tr class="tiny">
                <th rowspan="2" align="right">Cód.</th>
                <th rowspan="2" align="center">Data</th>
                <th rowspan="2" align="center">Estado</th>
                <th rowspan="2" align="right">Valor (R$)</th>
                <th rowspan="2" align="right">Bônus</th>
                <th rowspan="2" align="right">Cobrado (R$)</th>
                <th colspan="3" align="center">Promoção</th>
                <th rowspan="2" align="center">Compensado</th>
                <th rowspan="2" align="center">Ação</th>
            </tr>
            <tr class="tiny">
                <th>Descrição</th>
                <th align="center">Inicio</th>
                <th align="center">Fim</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$donations item=row}
            <tr>
                <td align="right">{$row->getId()}</td>
                <td align="center">{$row->getDate()}</td>
                <td align="center">{$row->getStatus()}</td>
                <td align="right">{Format::money($row->getValue())}</td>
                <td align="right">{$row->getBonus()}</td>
                <td align="right">{Format::money($row->getTotalValue())}</td>
                {if is_null($row->getPromotion())}
                    <td align="center" colspan="3">---</td>
                {else}
                    <td align="left">
                        {if strlen($row->getPromotion()->getDescription()) > 20}
                            <span title="{$row->getPromotion()->getDescription()}">
                                {substr($row->getPromotion()->getDescription(), 0, 20)}...
                            </span>
                        {else}
                            {$row->getPromotion()->getDescription()}
                        {/if}
                    </td>
                    <td align="center">{$row->getPromotion()->getStartDate()}</td>
                    <td align="center">{$row->getPromotion()->getEndDate()}</td>
                {/if}
                <td align="center">{if $row->getCompensate() eq true}Sim{else}Não{/if}</td>
                <td align="center">
                    {if empty($row->getTransactionCode()) eq false and !($row->getStatus() eq 'CANCELADO' or $row->getStatus() eq 'PAGO')}
                        <button class="ajax-url" data-url="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations" data-data="transactionCode={$row->getTransactionCode()}" data-method="GET" data-target=".bracp-body">Atualizar</button>
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
{/if}

{if $smarty.const.DONATION_SHOW_NEXT_PROMO eq true and $smarty.const.DONATION_INTERVAL_DAYS > 0}
    {if count($promos) eq 0}
        <p class="bracp-message-warning">Nenhuma promoção prevista para os próximos <strong>{$smarty.const.DONATION_INTERVAL_DAYS}</strong> dias.</p>
    {else}
        <br>
        <table border="1" align="center" class="bracp-table">
            <caption class="bracp-message-warning">Existem <strong>{count($promos)}</strong> promoções previstas para os próximos <strong>{$smarty.const.DONATION_INTERVAL_DAYS}</strong> dias.</caption>
            <thead>
                <tr class="tiny">
                    <th rowspan="2" align="right">Cód.</th>
                    <th rowspan="2" align="left">Descrição</th>
                    <th rowspan="2" align="right">Multiplicador</th>
                    <th colspan="2" align="center">Periodo</th>
                </tr>
                <tr class="tiny">
                    <th align="center">Inicio</th>
                    <th align="center">Fim</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$promos item=promo}
                <tr>
                    <td align="right">{$promo->getId()}</td>
                    <td align="left">{$promo->getDescription()}</td>
                    <td align="right">{$promo->getBonusMultiply()}</td>
                    <td align="center">{$promo->getStartDate()}</td>
                    <td align="center">{$promo->getEndDate()}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {/if}
{/if}
