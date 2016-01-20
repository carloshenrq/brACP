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

<h1>Minha Conta &raquo; Doações</h1>

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
	Lembre-se: Para cada <strong>R$ 1,00</strong> que você doar, você é prêmiado com <strong>{$smarty.const.DONATION_AMOUNT_MULTIPLY}</strong> em Bônus Eletrônico.
</p>

<form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/donations" autocomplete="off" method="post" target=".bracp-body">
    <div class="bracp-form" style="width: 400px">
        <div class="bracp-form-field">
            <label>
                Valor da Doação R$:<br>
                <input type="text" id="donation" name="donation" class="number bracp-donation-calc" data-rates="{$smarty.const.DONATION_AMOUNT_USE_RATE}" data-multiply="{$smarty.const.DONATION_AMOUNT_MULTIPLY}" data-target="#bonus" placeholder="Valor da doação" min="0" step="1.00" size="20" value="0.00"/>
            </label>
            <br>
            <label>
                Valor em Bônus Eletrônico:<br>
                <input type="text" id="bonus" name="bonus" class="number bracp-bonus-calc" data-rates="{$smarty.const.DONATION_AMOUNT_USE_RATE}" data-multiply="{$smarty.const.DONATION_AMOUNT_MULTIPLY}" data-target="#donation" placeholder="Valor em bônus" min="0" step="{$smarty.const.DONATION_AMOUNT_MULTIPLY}" size="20" value="0" data-places="0"/>
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
                <input type="checkbox" id="nobonus" name="nobonus"/>
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
</form>
