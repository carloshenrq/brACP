<?php
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

namespace PayPal;

use \Request;

class CheckNotify
{
    /**
     * Verifica se é real a notificação que o paypal enviou para o painel de controle.
     *
     * @param array $data dados de POST enviados pelo PayPal.
     *
     * @return boolean
     */
    public static function isValid($data)
    {
        // Url para fazer a requisição e saber se a notificação é válida.
        $paypalUrl =  ((BRACP_DEVELOP_MODE) ?
                            'https://www.sandbox.paypal.com/cgi-bin/webscr':
                            'https://www.paypal.com/cgi-bin/webscr');

        // Obtém a resposta do servidor com informações dos dadoes de requisição.
        return (trim(Request::create('')->request('POST', $paypalUrl, [
            'query' => http_build_query(array_merge($data, [
                'cmd' => '_notify-validate',
            ]))
        ])->getBody()->getContents()) === 'VERIFIED');
    }
}
