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

+function($)
{

    $(document).on('change', '.bracp-donation-calc', function() {

        var thisValue = parseFloat($(this).val());

        if(!isNaN(thisValue))
        {
            $($(this).data('target')).val(thisValue * parseFloat($(this).data('multiply'))).blur();

            // Verifica se está sendo taxado.
            if($(this).data('rates'))
                $('#cobrado').val((thisValue+ .4) / (1 - .0399)).blur();
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
        $(this).val((value/decimalMultiply).toFixed(decimalPlaces).toString());
    });

} (window.jQuery);

