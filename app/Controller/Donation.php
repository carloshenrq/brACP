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

        // 1. Se a notificação do paypal for para esta conta e se mostrar válida
        //  mediante verificação do PayPal, então segue para verificações internas.
        // 2. Somente é aceito transações na moeda configurada pelo painel de controle.
        // 3. Doações em moedas diferentes, não são aceitas.
        if(CheckNotify::isValid($data)
            && hash('md5', PAYPAL_ACCOUNT) == hash('md5', $data['business'])
            && $data['mc_currency'] == PAYPAL_CURRENCY)
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
        // ------------------
        // Somente para esclarecer o motivo de guardar todas essas informações.
        // Na dúvida, se é necessário ou não, é melhor guardar essas informações.
        // 
        // LINK: http://www.planalto.gov.br/ccivil_03/_ato2011-2014/2014/lei/l12965.htm
        //
        // Lei Nº 12965/2014 - Marco Civil da Internet no Brasil
        // Art. 16.  Na provisão de aplicações de internet, onerosa ou gratuita, é vedada a guarda:
        //      I - dos registros de acesso a outras aplicações de internet sem que o titular dos dados tenha consentido previamente, respeitado o disposto no art. 7o; ou
        //      II - de dados pessoais que sejam excessivos em relação à finalidade para a qual foi dado consentimento pelo seu titular.
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
            $donation->setTransactionUserID(((isset($data['custom'])) ? $data['custom']:''));
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

        // Se a doação está confirmada então...
        if($donation->getDonationStatus() == 'Completed')
        {
            // Obtém o registro de compensação para a doação.
            $compensate = self::getApp()->getEm()
                                        ->createQuery('
                                            SELECT
                                                compensate, donation
                                            FROM
                                                Model\Compensate compensate
                                            INNER JOIN
                                                compensate.donation donation
                                            WHERE
                                                donation.id = :id
                                        ')
                                        ->setParameter('id', $donation->getId())
                                        ->getOneOrNullResult();

            // Se não possui compensação, então cria o registro de compensação
            //  caso o jogador possua um login para ser utilizado.
            // -> Mesmo que não seja um login válido, ele irá criar a compensação porque pode ter sido digitado
            //    de forma incorreta.
            if(is_null($compensate) && !empty($donation->getTransactionUserID()))
            {
                // Gera a compensação no banco de dados.
                $compensate = new \Model\Compensate;
                $compensate->setDonation($donation);
                $compensate->setAccount(Account::getAccountUserID($donation->getTransactionUserID()));
                $compensate->setUserid($donation->getTransactionUserID());
                $compensate->setPending(true);
                $compensate->setDate(null);

                self::getApp()->getEm()->persist($compensate);
                self::getApp()->getEm()->flush();
            }
        }

        return $donation;
    }
}

