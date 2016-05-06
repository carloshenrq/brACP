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

        // Se a notificação do paypal for para esta conta e se mostrar válida
        //  mediante verificação do PayPal, então segue para verificações internas.
        if(hash('md5', PAYPAL_ACCOUNT) == hash('md5', $data['business']) && CheckNotify::isValid($data))
        {
            // Obtém os dados de doação do paypal para os dados informados.
            $donation = self::parsePaypalData($data);

            // @Todo: Tratamento para pagamentos confirmados/estornados/etc...
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

    /**
     * Trata os dados de doação do paypal para o banco de dados.
     *
     * @param array $data
     *
     * @return \Model\Donation
     */
    private static function parsePaypalData($data)
    {
        // Verifica se a transação está cadastrada no banco de dados antes de continuar,
        //  a transação pode ter sido cancelada.
        $donation = self::getApp()->getEm()
                            ->createQuery('
                                SELECT
                                    donation, promotion
                                FROM
                                    Model\Donation donation
                                LEFT JOIN
                                    donation.promotion promotion
                                WHERE
                                    donation.transactionDrive   = :transactionDrive AND
                                    donation.transactionCode    = :transactionCode
                            ')
                            ->setParameter('transactionDrive',  'PAYPAL')
                            ->setParameter('transactionCode',   $data['txn_id'])
                            ->getOneOrNullResult();

        // Verifica se a doação para a transação informada é existente no banco
        //  de dados.
        if(is_null($donation))
        {
            // Cria a doação no banco de dados.
            $donation = new \Model\Donation;
            $donation->setReceiverID($data['receiver_id']);
            $donation->setReceiverMail($data['receiver_email']);
            $donation->setSandboxMode($data['test_ipn'] == '1');
            $donation->setTransactionDrive('PAYPAL');
            $donation->setTransactionCode($data['txn_id']);
            $donation->setTransactionType($data['txn_type']);
            $donation->setTransactionUserID($data['custom']);
            $donation->setPayerID($data['payer_id']);
            $donation->setPayerMail($data['payer_email']);
            $donation->setPayerStatus($data['payer_status']);
            $donation->setPayerName($data['first_name'] . ' ' . $data['last_name']);
            $donation->setPayerCountry($data['address_country']);
            $donation->setPayerState($data['address_state']);
            $donation->setPayerCity($data['address_city']);
            $donation->setPayerAddress($data['address_street']);
            $donation->setPayerZipCode($data['address_zip']);
            $donation->setPayerAddressConfirmed($data['address_status'] == 'confirmed');
            $donation->setDonationValue($data['mc_gross']);
            $donation->setDonationPayment(date_create_from_format('G:i:s M m, Y T',
                                            $data['payment_date'])->format('Y-m-d H:i:s'));
            $donation->setDonationStatus($data['payment_status']);
            $donation->setDonationType($data['payment_type']);
            $donation->setVerifySign($data['verify_sign']);

            self::getApp()->getEm()->persist($donation);
            self::getApp()->getEm()->flush();
        }

        return $donation;
    }
}

