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

<p>
    Olá <strong>{$account->getUserid()}</strong>, você sabia que suas doações são importantes para o servidor?!<br>
    Quando você doa ao servidor você ajuda em:
</p>

<ul>
    <li>Desenvolvimento de novos sistemas e novidades para todos.</li>
    <li>Manter o servidor online para que todos possam jogar.</li>
    <li>Ajuda o servidor a crescer e cada vez a ter mais jogadores.</li>
</ul>

<p class="bracp-message-info">
    Nós nunca lhe obrigaramos a realizar uma doação ao servidor.<br>
    <strong><i>Você doa por livre e espontânea vontade.</i></strong> 
</p>

{if is_null($promotion) eq false}
    <p class="bracp-message-success">
        <strong>Promoção ativa!</strong> +{$promotion->getBonusMultiply()} em bônus eletrônico!<br>
        <br>
        <strong>Validade:</strong><br>
        <u><i>{Format::date($promotion->getStartDate(), 'd/m/Y')}</i></u>
            até <u><i>{Format::date($promotion->getEndDate(), 'd/m/Y')}</i></u> 
            termina em 
            <u><i>{Format::date_diff($promotion->getStartDate(), $promotion->getEndDate(), 'Y-m-d')}</i></u>.<br>
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
    <p class="bracp-message-info">
        Nós utilizamos o <a href="https://pagseguro.uol.com.br/" target="_blank">PagSeguro</a> como motor de nossas doações.<br>
        Por conta disso, será cobrado uma taxa de <strong>R$ 0,40 + 0,3999%</strong> sobre o valor doado. 
    </p>
{/if}


