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

{extends file="mail.default.tpl"}

{block name="mail_body"}

{if $donation->getStatus() eq 'INICIADA'}

Sua doação foi criada e está aguardando o pagamento para ser confirmada.<br>
Se você já realizou o pagamento, você receberá um e-mail informativo que foi pago.<br>

{else if $donation->getStatus() eq 'PAGO'}

Agradecemos o pagamento de sua doação!<br>
Obrigado por ajudar o servidor a crescer!

{else if $donation->getStatus() eq 'CANCELADO'}

Sua doação foi cancelada.<br>
Agradecemos sua a intenção de ajudar.

{else}

Sua doação foi devolvida.

{/if}
<br>
<hr>
<strong>Código:</strong> {$donation->getId()}<br>
<br>
<strong>Status:</strong> {$donation->getStatus()}<br>
<strong>Data da Doação:</strong> {Format::date($donation->getDate(), 'd/m/Y')}<br>
<strong>Valor da Doação:</strong> R$ {Format::money($donation->getValue())}<br>
<strong>Quantidade de Bônus:</strong> {$donation->getBonus()}<br>
<strong>Valor Total:</strong> R$ {Format::money($donation->getTotalValue())}<br>
<strong>Receber bônus:</strong> {if $donation->getReceiveBonus() eq true}Sim{else}Não{/if}
{/block}
