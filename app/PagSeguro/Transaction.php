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
namespace PagSeguro;

use \Request;

/**
 * Classe para verificação de transações e suas notificações.
 *
 * @static
 */
class Transaction
{
    /**
     * Realiza a requisição para o servidor do pagseguro para obter os dados
     *  para o código de transação enviado a aplicação.
     *
     * @static
     *
     * @param string $transactionCode Código de transação enviado.
     *
     * @return object
     */
    public static function checkTransaction($transactionCode)
    {
        // Endereço de leitura da notificação.
        $transactionUrl = 'v3/transactions/' . $transactionCode
                            .'?email=' . PAG_EMAIL . '&token=' . PAG_TOKEN;

        // Obtém os dados da resposta da requisição ao serviço do pagseguro.
        $transactionResponse = simplexml_load_string(Request::create(PAG_WS_URL)->get($transactionUrl)->getBody()->getContents());

        // Retorna o objeto da requisição para a chamada.
        return json_decode(json_encode($transactionResponse));
    }

    /**
     * Realiza a requisição para o servidor do pagseguro para obter os dados
     *  para o código de notificação enviado a aplicação.
     *
     * @static
     *
     * @param string $notificationCode Código de notificação de transações.
     *
     * @return object
     */
    public static function checkNotification($notificationCode)
    {
        // Endereço de leitura da notificação.
        $notificationUrl = 'v3/transactions/notifications/' . $notificationCode
                            .'?email=' . PAG_EMAIL . '&token=' . PAG_TOKEN;

        // Obtém os dados da resposta da requisição ao serviço do pagseguro.
        $notificationResponse = simplexml_load_string(Request::create(PAG_WS_URL)->get($notificationUrl)->getBody()->getContents());

        // Retorna o objeto da requisição para a chamada.
        return json_decode(json_encode($notificationResponse));
    }
}
