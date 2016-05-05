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

<h1>@@DONATIONS(TITLE) &raquo; @@DONATIONS,PAYPAL(TITLE)</h1>

<div class="bracp-message error mobile-only">
    @@ERRORS(NO_MOBILE)
</div>

<div class="no-mobile">

{if $smarty.const.BRACP_DEVELOP_MODE eq true}
    <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
{else}
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
{/if}
    <!-- Identify your business so that you can collect the payments. -->
    <input type="hidden" name="business"
        value="{$smarty.const.PAYPAL_ACCOUNT}">

    <!-- Specify a Donate button. -->
    <input type="hidden" name="cmd" value="_donations">

    <!-- Specify details about the contribution -->
    <input type="hidden" name="item_name" value="@@DONATIONS,PAYPAL(ITEM)">
    <input type="hidden" name="item_number" value="@@DONATIONS,PAYPAL(NUMBER)">
    <input type="text" name="amount" value="0.00">
    <input type="text" name="custom" value="">
    <input type="hidden" name="currency_code" value="{$smarty.const.PAYPAL_CURRENCY}">

    <!-- Url de notificação para a doação do paypal -->
    <input type="hidden" name="notify_url" value="{$smarty.const.BRACP_URL}donations/paypal/notify"/>

    <!-- Display the payment button. -->
    <input type="image" name="submit"
    src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif"
    alt="PayPal - The safer, easier way to pay online">
    <img alt="" width="1" height="1"
    src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" >
</form>

</div>
