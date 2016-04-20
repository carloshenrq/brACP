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

<h1>Minha Conta &raquo; Doações &raquo; PagSeguro</h1>

{if isset($donationId) eq true && isset($checkoutCode) eq true}
<script>
    var donationId = '{$donationId}',
        checkoutCode = '{$checkoutCode}';

    // Inicializa os parametros de doação.
    donation(donationId, checkoutCode, '{$smarty.const.BRACP_DIR_INSTALL_URL}account/pagseguro/transaction');
</script>
{/if}

<div class="bracp-message error mobile-only">
    <h1>Ops!</h1>
    Você não pode acessar esta tela usando dispositivos moveis!
</div>

<div class="no-mobile">
    {if isset($message) eq true}
        {if isset($message['success']) eq true}
            <p class='bracp-message success'>
                {$message['success']}
            </p>
        {else}
            <p class="bracp-message error">
                {$message['error']}
            </p>
        {/if}
    {/if}

    <p>
        Olá <strong>{$account->getUserid()}</strong>, você sabia que suas doações são importantes para o servidor?!<br>
        Quando você doa ao servidor você ajuda em:
    </p>

    <ul>
        <li>Desenvolvimento de novos sistemas e novidades para todos.</li>
        <li>Manter o servidor online para que todos possam jogar.</li>
        <li>Ajuda o servidor a crescer e cada vez a ter mais jogadores.</li>
    </ul>

    <p class="bracp-message info">
        Nós nunca lhe obrigaramos a realizar uma doação ao servidor.<br>
        <strong><i>Você doa por livre e espontânea vontade.</i></strong> 
    </p>

    {if is_null($promotion) eq false}
        <p class="bracp-message success">
            <strong>Promoção ativa!</strong> +{$promotion->getBonusMultiply()} em bônus eletrônico!<br>
            <br>
            <strong>Validade:</strong><br>
            <u><i>{Format::date($promotion->getStartDate(), 'd/m/Y')}</i></u>
                até <u><i>{Format::date($promotion->getEndDate(), 'd/m/Y')}</i></u> 
                termina em 
                <u><i>{Format::date_diff(date('Y-m-d'), $promotion->getEndDate(), 'Y-m-d')}</i></u>.<br>
            <br>
            <strong>Descrição:</strong><br>{$promotion->getDescription()}<br>
            <br>
            <i><strong>OBS.:</strong> Doações iniciadas antes da data de inicio da promoção não recompensadas com o bônus extra.</i>
        </p>
    {/if}

    <p>
        Como forma de agradecimento a sua doação, nós lhe presentearemos com
        <strong>{$smarty.const.DONATION_AMOUNT_MULTIPLY}{if is_null($promotion) eq false} (+{$promotion->getBonusMultiply()}){/if}</strong> em bônus eletrônico a cada <strong>R$ 1,00</strong> que você doar.
    </p>

    {if $smarty.const.DONATION_AMOUNT_USE_RATE eq true}
        <p class="bracp-message info">
            Nós utilizamos o <a href="https://pagseguro.uol.com.br/" target="_blank">PagSeguro</a> como motor de nossas doações.<br>
            Por conta disso, será adicionado uma taxa de <strong>R$ 0,40 + 3,99%</strong> sobre o valor doado.
            {if $smarty.const.DONATION_AMOUT_SHOW_RATE_CALC eq true}
                <br>
                <br>
                <strong>Valor doado</strong>: R$ 100,00<br>
                <strong>Taxa (%)</strong>: 100,00 * 3,99% = 3,99 + 0,40 = 4,39<br>
                <strong>Valor total</strong>: R$ 100,00 + R$ 4,39 = R$ 104,39
            {/if}
        </p>
    {/if}

    <form class="ajax-form" action="{$smarty.const.BRACP_DIR_INSTALL_URL}account/pagseguro" autocomplete="off" method="post" target=".bracp-body">
        <div class="bracp-form">
            <div style="width: 50%; margin: auto;">
                <div class="bracp-form-field">
                    <label>
                        Valor da Doação (R$):<br>
                        <input class="bracp-donation-calc number" data-target="#bonusValue" data-multiply="{$multiply}" data-rates="1" type="text" id="donationValue" name="donationValue" placeholder="Valor da Doação (R$)" size="30" maxlength="30" data-places="2" value="0.00" required/>
                    </label>
                </div>
                <div class="bracp-form-field">
                    <label>
                        Bônus Eletrônico:<br>
                        <input type="text" id="bonusValue" name="bonusValue" placeholder="Valor do bônus" class="number" data-places="0" value="0" size="30" maxlength="30" readonly/>
                    </label>
                </div>
                <div class="bracp-form-field">
                    <label>
                        Valor total (R$):<br>
                        <input type="text" id="cobrado" name="cobrado" placeholder="Valor cobrado (com taxas)" class="number" data-places="2" value="0.00" size="30" maxlength="30" readonly/>
                    </label>
                </div>
            </div>
            <div class="bracp-form-field">
                <label>
                    <input type="checkbox" id="donotreceivebonus" name="donotreceivebonus"/>
                    Eu não desejo receber os bônus eletrônicos para esta doação.
                </label>
                <br>
                <label>
                    <input type="checkbox" id="acceptdonation" name="acceptdonation" required/>
                    Eu entendo que isto <strong>é uma doação</strong> e <strong>não uma compra</strong>.
                </label>
            </div>

            {if $smarty.const.BRACP_RECAPTCHA_ENABLED eq true}
                <div class="g-recaptcha" data-sitekey="{$smarty.const.BRACP_RECAPTCHA_PUBLIC_KEY}"></div>
            {/if}

            <div class="bracp-form-submit">
                <input class="btn" type="submit" value="Doar"/>
                <input class="btn" type="reset" value="Limpar"/>
            </div>
        </div>
    </form>

    {if count($donations) eq 0}

    <p class="bracp-message warning">
        Você ainda não realizou doações ao servidor.
    </p>

    {else}
    <br>
    <div class="donation-table">
        {include 'account.donations.pagseguro.table.tpl'}
    </div>
    {/if}
    <br>

    {if $smarty.const.DONATION_SHOW_NEXT_PROMO eq true}
        {if count($promotions) eq 0}
            <div class="bracp-message warning">
                Nenhuma promoção disponível para os próximos <strong>{$smarty.const.DONATION_INTERVAL_DAYS}</strong> dia(s).
            </div>
        {else}
            <table border="1" align="center" class="bracp-table">
                <caption class="bracp-message info">Existe(m) <strong>{count($promotions)}</strong> doação(ões) para os próximos <strong>{$smarty.const.DONATION_INTERVAL_DAYS}</strong> dia(s).</caption>
                <thead>
                    <tr>
                        <th align="right">Cód.</th>
                        <th>Descrição</th>
                        <th align="right">Bônus extra</th>
                        <th align="center">Inicio</th>
                        <th align="center">Fim</th>
                        <th align="left">Duração</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$promotions item=row}
                        <tr>
                            <td align="right" width="50px">{$row->getId()}</td>
                            <td>{$row->getDescription()}</td>
                            <td align="right" width="100px">{$row->getBonusMultiply()}</td>
                            <td align="center" width="100px">{Format::date($row->getStartDate(), 'd/m/Y')}</td>
                            <td align="center" width="100px">{Format::date($row->getEndDate(), 'd/m/Y')}</td>
                            <td align="left">{Format::date_diff($row->getStartDate(), $row->getEndDate(), 'Y-m-d')}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {/if}
    {/if}
</div>
