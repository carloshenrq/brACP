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
     * Método de listagem de login.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function checkout(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Dados recebidos pelo post para resetar os dados.
        $data = $request->getParsedBody();

        // Dados de retorno para informações de erro.
        $return = ['error_state' => 0, 'success_state' => false];

        $return['error_state'] = self::donationSave($data['donationValue'], $data['userid']);

        $return['success_state']    = $return['error_state'] == 0;
        $response->withJson($return);
    }

    /**
     * Método para criar e salvar doações no banco de dados.
     */
    public static function donationSave($value, $userid = null)
    {
        // Retorna negativo caso o sistema de doaçõe esteja desativado.
        if(!BRACP_DONATION_ENABLED)
            return -1;

        // Doações negativas ou com valores, sejam inferiores ou superiores ao limite, são negadas.
        if($value <= 0 || $value < BRACP_DONATION_MIN_VALUE || $value > BRACP_DONATION_MAX_VALUE)
            return 1;

        // Se o nome de usuário for enviado, então testa a existência do mesmo.
        //  Caso não exista, retorna 2.
        if(!empty($userid))
        {
            $account = self::getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid]);

            if(is_null($account))
                return 2;

            unset($account);
        }

        // @Todo: Fazer salvar no banco de dados o retorno de doações.

        return 0;

    }

    /**
     * Método de listagem de login.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function promoList(ServerRequestInterface $request, ResponseInterface $response, $args)
    {

        $response->withJson(self::getPromoList());

    }

    /**
     * Obtém a lista de promoções para o servidor.
     *
     * @return array
     */
    private static function getPromoList()
    {
        return Cache::get('BRACP_PROMO_LIST', function() {
            return Donation::getCpEm()
                    ->createQuery('
                        SELECT
                            promo
                        FROM
                            Model\Promotion promo
                        ORDER BY
                            promo.startDate ASC,
                            promo.endDate DESC
                    ')
                    ->getResult();
        });
    }

}

