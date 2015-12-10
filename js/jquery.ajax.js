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

    // Caso realize o click em um elemento com ajax-url
    $(document).on('click', '.ajax-url', function() {
        // Define o url da página.
        window.history.pushState("", "", $(this).data('url'));

        // Realiza a requisição ajax no contexto atual.
        $.ajax({
            'url'       : $(this).data('url'),
            'context'   : $($(this).data('target')) || this,
            'method'    : $(this).data('method') || "GET",
            'data'      : $(this).data('ajaxData') || {},
            'cache'     : false,
            'global'    : false,
            'async'     : true,
            'dataType'  : 'text',
            'success'   : function(data, textStatus, jqXHR) {
                // Define o retorno para a requisição como conteudo do contexto atual.
                this.html(data);
            },
            'error'     : function(jqXHR, textStatus, errorThrown ) {
                // Caso aconteça algum erro durante a requisição
                this.html($('<div class="ajax-error"/>').html(errorThrown));
            },
        });
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
        }
    });

} (window.jQuery);

