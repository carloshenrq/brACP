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
    // // Ao clicar no div do link envia para data('url').
    // $(document).on('click', '.bracp-link', function() {
    //     // Envia o usuário para o link clicado no div.
    //     window.location.href = $(this).data('url');
    // });

    // Ao clicar sobre o imagem do menu mobile, abrir o menu dos itens.
    $(document).on('click', '.bracp-menu-mobile-img', function() {
        var menuHtml = $($(this).data('menu')).html(),
            objToggle = $($(this).data('toggle'));

        if(objToggle.css('display') == 'none')
        {
            objToggle.html(menuHtml);
        }

        objToggle.stop(true, true).slideToggle('fast');

        // $($(this).data('toggle')).html(menuHtml).slideToggle('fast');
    });

    // Ao clicar em um item que possua sub-menu adiciona o menu seguinte como html atual.
    $(document).on('click', '.bracp-menu-mobile-items-show > ul li', function() {
        if($(this).find('ul').length > 0)
        {
            var subMenu = $(this).find('ul'),
                subMenuHtml = subMenu.html();

            subMenuHtml = "<li class='bracp-menu-anterior'>" + subMenu.data('back') + "</li>" + subMenuHtml; 

            $($(this).parent()).html(subMenuHtml);
        }
    });

    $(document).on('ready', function() {

    });
} (window.jQuery);

