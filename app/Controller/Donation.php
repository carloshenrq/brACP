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

namespace Controller;

use \Cache;
use \Request;
use \PayPal\CheckNotify;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Donation
{
    use \TApplication;

    /**
     * Recebe notificações do PayPal para dar os bonus ao jogador.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function paypalNotify(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Obtém os dados de post para verificação do paypal.
        $data = $request->getParsedBody();

        // Se a notificação do paypal for válida...
        if(CheckNotify::isValid($data))
        {
            // @Todo: Fazer os tramits para lançamento no banco de dados.
        }
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function paypal(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe o display para home.
        self::getApp()->display('donation.paypal', [
        ]);
    }
}

