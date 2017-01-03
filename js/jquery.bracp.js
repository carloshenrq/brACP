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

function closeAllModals(callBack)
{
    if(!callBack) callBack = function() {};

    +function($)
    {
        $(document).find('.modal-trigger-check:checked').each(function() {
            $(this).prop('checked', false);
        });

        callBack();

    } (window.jQuery);
}

function removeAllModalMessages()
{
    +function($)
    {
        setTimeout(function(){
            $(document).find('.modal-body').filter(function() {
                return ($(this).find('.message').length > 0);
            }).each(function() {
                $(this).find('.message').filter(function() {
                    return (!$(this).hasClass('message-timeout')
                    && ($(this).hasClass('error') || $(this).hasClass('success')));
                }).each(function() {
                    $(this)
                        .addClass('message-timeout')
                        .on('click', function() {
                            $(this).stop(true, true).fadeOut('fast', function() {
                                $(this).remove();
                            });
                        })
                        .delay(15000)
                        .fadeOut('fast', function() {
                            $(this).remove();
                        });
                });
            });
        }, 300);

    } (window.jQuery);
}

+function($)
{

    $(window).on('keydown', function(e) {
        // Tratamento para os modais que quando houver tecla esc pressionada,
        //  irá fechar os modais com checked.
        if(e.which == 27)
        {
            e.preventDefault();
            closeAllModals();
        }
    });

    $(document).on('click', '.url-link', function() {
        window.location.href = $(this).data('href');
    });

    $(document).on('click', '.modal-trigger-check', function() {
        var checked = $(this).prop('checked');

        $('.modal-trigger-check').each(function() {
            $(this).prop('checked', false);
        })

        $(this).prop('checked', checked);
    });

    $(document).on('ready', function() {

        $('canvas.char-stats').each(function() {

            var statsData = $(this).data('stats');

            var radar = new Chart(this, {
                type : 'radar',
                data : {
                    labels : ["Str", "Agi", "Vit", "Int", "Dex", "Luk"],
                    datasets : [{
                        fill: true,
                        data: statsData,
                        label : false,
                        backgroundColor : ['rgba(153, 204, 255, .4)'],
                        scaleStepWidth : '0'
                    }]
                },
                options : {
                    responsive: false,
                    maintainAspectRatio: false,
                    scale: {
                        reverse: false,
                        scaleLabel: {
                            display: false
                        },
                        ticks : {
                            beginAtZero : true,
                            max : Math.max.apply(null, statsData)
                        }
                    }
                }
            });
            radar.options.legend.display = false;
        });

    });

} (window.jQuery);
