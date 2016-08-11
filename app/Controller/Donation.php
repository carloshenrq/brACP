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
// use \Model\Donation;

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
     * Método de leitura dos dados para abortar a doação.
     */
    public static function abort(ServerRequestInterface $request, ResponseInterface $response, $args)
    {

    }

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
        $return = ['error_state' => 0, 'success_state' => false, 'checkout' => false, 'id' => null];

        $checkout = self::donationSave($data['donationValue'], $data['userid']);

        if(is_array($checkout))
        {
            $return['error_state'] = 0;
            $return['checkout'] = $checkout['checkout'];
            $return['id']       = $checkout['id'];
        }
        else
            $return['error_state'] = $checkout;


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

        $account = null;
        // Se o nome de usuário for enviado, então testa a existência do mesmo.
        //  Caso não exista, retorna 2.
        if(!empty($userid))
        {
            $account = self::getSvrDftEm()
                        ->getRepository('Model\Login')
                        ->findOneBy(['userid' => $userid]);

            if(is_null($account))
                return 2;
        }

        // Procura promoção no banco de dados para aplicar a doação que está sendo realizada.
        $promos =   self::getCpEm()
                    ->createQuery('
                        SELECT
                            promotion
                        FROM
                            Model\Promotion promotion
                        WHERE
                            :CURDATE BETWEEN promotion.startDate AND promotion.endDate
                                AND
                            promotion.canceled = false
                        ORDER BY
                            promotion.startDate ASC,
                            promotion.endDate ASC
                    ')
                    ->setParameter('CURDATE', date('Y-m-d'))
                    ->setMaxResults(1)
                    ->getResult();

        // Caso encontre a promoção.
        $promotion = null;

        $donation = new \Model\Donation;
        $donation->setPromotion($promotion);
        $donation->setReceiverId(BRACP_PAGSEGURO_TOKEN);
        $donation->setReceiverMail(BRACP_PAGSEGURO_EMAIL);
        $donation->setSandboxMode(BRACP_PAGSEGURO_SANDBOX_MODE);
        $donation->setTransactionDrive('PAGSEGURO');
        $donation->setTransactionCode('');
        $donation->setTransactionType('DONATION');
        $donation->setTransactionUserID($userid);
        $donation->setTransactionCheckoutCode(null);
        $donation->setPayerID(null);
        $donation->setPayerMail(null);
        $donation->setPayerStatus(null);
        $donation->setPayerName(null);
        $donation->setPayerCountry(null);
        $donation->setPayerState(null);
        $donation->setPayerCity(null);
        $donation->setPayerAddress(null);
        $donation->setPayerZipCode(null);
        $donation->setPayerAddressConfirmed(null);
        $donation->setDonationValue($value);
        $donation->setDonationPayment(null);
        $donation->setDonationStatus('INICIADA');
        $donation->setDonationType(null);
        $donation->setVerifySign(null);
        $donation->setCompensate(!is_null($userid));
        $donation->setAccount_id(((is_null($account)) ? null : $account->getAccount_id()));
        $donation->setDonationServer(constant('BRACP_SRV_' . self::getApp()->getSession()->BRACP_SVR_SELECTED . '_NAME'));
        $donation->setSqlHost(constant('BRACP_SRV_' . self::getApp()->getSession()->BRACP_SVR_SELECTED . '_SQL_HOST'));
        $donation->setSqlUser(constant('BRACP_SRV_' . self::getApp()->getSession()->BRACP_SVR_SELECTED . '_SQL_USER'));
        $donation->setSqlPass(constant('BRACP_SRV_' . self::getApp()->getSession()->BRACP_SVR_SELECTED . '_SQL_PASS'));
        $donation->setSqlDBName(constant('BRACP_SRV_' . self::getApp()->getSession()->BRACP_SVR_SELECTED . '_SQL_DBNAME'));
        $donation->setCompensateVar(BRACP_DONATION_VAR);

        self::getCpEm()->persist($donation);
        self::getCpEm()->flush();

        $txtBody = Request::create(BRACP_PAGSEGURO_WS_URL)
                    ->post('v2/checkout?email='.$donation->getReceiverMail().'&token='.$donation->getReceiverId(), [
                        'form_params' => [
                            'email'                 => $donation->getReceiverMail(),
                            'token'                 => $donation->getReceiverId(),
                            'currency'              => 'BRL',
                            'itemId1'               => 'SEURO_DONATION',
                            'itemDescription1'      => 'Doação para SeuRO - Servidor de Ragnarok Online',
                            'itemAmount1'           => sprintf('%.2f', $donation->getDonationValue()),
                            'itemQuantity1'         => 1,
                            'metadataItemKey1'      => 'GAME_NAME',
                            'metadataItemValue1'    => 'SeuRO',
                            'metadataItemKey2'      => 'PLAYER_ID',
                            'metadataItemValue2'    => ((empty($userid)) ? 'NO_USER':$userid),
                        ]
                    ])
                    ->getBody()->getContents();

        // Dados de checkout recebidos pela requisição realizada ao PagSeguro.
        $checkout = json_decode(json_encode(simplexml_load_string($txtBody)));
        unset($txtBody);

        // Salva o código de checkout no banco de dados.
        $donation->setTransactionCheckoutCode($checkout->code);
        self::getCpEm()->merge($donation);
        self::getCpEm()->flush();

        return [
            'id' => $donation->getId(),
            'checkout' => $donation->getTransactionCheckoutCode()
        ];

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

