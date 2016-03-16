/**
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
 */

var grecaptcha_timer = false;

+function($)
{
    // Ao clicar no botão para doar novamente.
    $(document).on('click', '.donation-checkout', function(e) {
        e.preventDefault();
        // Abre o pagseguro para realizar o checkout novamente.
        donation($(this).data('id'), $(this).data('checkout'), $(this).data('url'));
    });

    // Ao clicar no botão para cancelar a doação.
    $(document).on('click', '.donation-cancel', function(e) {
        e.preventDefault();
        // Cancela a doação clicada.
        donationAbort($(this).data('id'), $(this).data('checkout'), $(this).data('url'), true);
    });

    $(document).on('change', '.bracp-donation-calc', function() {

        var thisValue = parseFloat($(this).val());

        if(!isNaN(thisValue))
        {
            $($(this).data('target')).val(thisValue * parseFloat($(this).data('multiply'))).blur();

            // Verifica se está sendo taxado.
            if($(this).data('rates'))
                $('#cobrado').val((thisValue/(1 - .0399)) + .4).blur();
        }
        else
            $(this).val(0).blur().change();

    });

    $(document).on('change', '.bracp-bonus-calc', function() {

        var thisValue = parseFloat($(this).val());

        if(!isNaN(thisValue))
            $($(this).data('target')).val(thisValue / parseFloat($(this).data('multiply'))).blur().change();
        else
            $(this).val(0).blur().change();
    });

    $(document).on('keypress', 'input.number', function(e) {
        var keyCode = e.keyCode || e.which;

        return /^(46|8|9|27|13|190)$/.test(keyCode) || /^([0-9.-])$/.test(String.fromCharCode(keyCode));
    });

    // Ao sair de um campo decimal, define as casas decimais.
    $(document).on('blur', 'input[type="number"], input.number', function() {
        var decimalPlaces = $(this).data('places') == undefined ? 2:$(this).data('places'),
            decimalMultiply = Math.pow(10, decimalPlaces);

        var tmpValue = $(this).val();

        while(tmpValue.indexOf(',') >= 0)
            tmpValue = tmpValue.replace(',', '.')

        value = Math.floor(decimalMultiply * parseFloat(tmpValue));

        if(isNaN(value))
            value = 0;

        $(this).val((value/decimalMultiply).toFixed(decimalPlaces).toString());
    });

    $(document).ready(function() {
        grecaptcha_timer = setInterval(function() {
            // Se a biblioteca do google ainda não estiver pronta,
            //  mantém o timer rodando.
            if(grecaptcha == undefined)
                return;

            // Limpa o intervalo caso seja carregado.
            clearInterval(grecaptcha_timer);

            // Adicionado renderização para o código re-captcha na página atual.
            // Verificações serão adicionadas via servidor.
            if($('.bracp-g-recaptcha').length > 0)
            {
                $('.bracp-g-recaptcha').each(function(){
                    if($(this).html().length == 0)
                    {
                        grecaptcha.render(this, {
                            'sitekey' : $(this).data('sitekey')
                        });
                    }
                });
            }
        }, 100);
    });

    $.ajaxSetup({
        'beforeSend' : function(jqXHR, settings) {
            // Ajusta a tela de acordo com a largura.
            $('.bracp-ajax-loading').css({
                'width' : $(window).width(),
                'height' : $(window).height(),
            }).stop(true, true).fadeIn('fast');
        },
        'complete' : function (jqXHR, textStatus) {
            $('.bracp-ajax-loading').stop(true, true).fadeOut('fast');

            // Adicionado renderização para o código re-captcha na página atual.
            // Verificações serão adicionadas via servidor.
            if($('.bracp-g-recaptcha').length > 0)
            {
                $('.bracp-g-recaptcha').each(function(){
                    if($(this).html().length == 0)
                    {
                        grecaptcha.render(this, {
                            'sitekey' : $(this).data('sitekey')
                        });
                    }
                });
            }

            $('input[type="number"], input.number').each(function() {
                $(this).blur();
            });
        }
    });

} (window.jQuery);


function donation(donationId, checkoutCode, url)
{
    // Inicializa o lightbox do pagseguro.
    PagSeguroLightbox({
        'code' : checkoutCode
    }, {
        'success' : function(transactionCode)
        {
            // Atualiza a doação com o código de transação.
            donationTransactionCode(donationId,
                                    checkoutCode,
                                    transactionCode,
                                    url);
        },
        'abort' : function()
        {
            // Cancela a doação.
            donationAbort(donationId,
                            checkoutCode,
                            url);
        }
    });
}

/**
 * Atualiza a doação com um código de transção.
 *
 * @param integer donationId Código da doação.
 * @param string transactionCode Código de transação.
 */
function donationTransactionCode(donationId, checkoutCode, transactionCode, url)
{
    $.ajax({
        'url'       : url,
        'method'    : 'POST',
        'data'      : { 'DonationID' : donationId, 'transactionCode' : transactionCode, 'checkoutCode' : checkoutCode },
        'async'     : false,
        'success'   : function() {
            window.location.reload();
        }
    });
}

/**
 * Cancela uma doação já inicializada.
 *
 * @param integer donationId Código da doação.
 * @param string checkoutCode Código de checkout para a doação.
 */
function donationAbort(donationId, checkoutCode, url, async)
{
    if(async == undefined)
        async = false;

    $.ajax({
        'url'       : url,
        'method'    : 'POST',
        'data'      : { 'DonationID' : donationId, 'checkoutCode' : checkoutCode, 'cancel' : 1 },
        'async'     : async,
        'success'   : function() {
            window.location.reload();
        }
    });
}
