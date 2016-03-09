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

    // Caso realize o submit de um form tipo ajax.
    $(document).on('submit', 'form.ajax-form', function() {

        // Se estiver definido para bloquear o url, então não atualiza o url no navegador.
        if($(this).data('block') == undefined)
        {
            // Define o url da página.
            window.history.pushState("", "", $(this).attr('action'));
        }

        // Realiza a requisição ajax no contexto atual.
        $.ajax({
            'url'       : $(this).attr('action'),
            'context'   : $($(this).attr('target')) || this,
            'method'    : $(this).attr('method') || "GET",
            'data'      : $(this).serializeObject() || {},
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

        return false;
    });

    // Caso realize o click em um elemento com ajax-url
    $(document).on('click', '.ajax-url', function() {

        // Se estiver definido para bloquear o url, então não atualiza o url no navegador.
        if($(this).data('block') == undefined)
        {
            // Define o url da página.
            window.history.pushState("", "", $(this).attr('action'));
        }

        // Realiza a requisição ajax no contexto atual.
        $.ajax({
            'url'       : $(this).data('url'),
            'context'   : $($(this).data('target')) || this,
            'method'    : $(this).data('method') || "GET",
            'data'      : $(this).data('data') || {},
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
                this.html(jqXHR.responseText);
            },
        });
    });

    $.fn.serializeObject = function()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
} (window.jQuery);

