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
use \DOMDocument;
use \ArrayObject;

/**
 * Classe construtura para as transações do PagSeguro.
 */
class Checkout
{
    /**
     * Quem está recebendo o checkout.
     * @var string
     */
    private $receiver;

    /**
     * Moeda para a criação do checkout.
     * @var string
     */
    private $currency = 'BRL';

    /**
     * Items para o checkout.
     * @var ArrayObject
     */
    private $items = null;

    /**
     * Código de referência ao checkout.
     * @var string
     */
    private $reference;

    /**
     * Quem está fazendo o checkout.
     * @var CheckoutSender
     */
    private $sender;

    /**
     * Valor de acrescimo ou desconto.
     * @var float
     */
    private $extraAmount;

    /**
     * Url de retorno.
     * @var string
     */
    private $redirectURL;

    /**
     * Url de notificação.
     * @var string
     */
    private $notificationURL;

    /**
     * Número máximo de usos para esta consulta.
     * @var int
     */
    private $maxUses = 999;

    /**
     * Vida máxima para o pagamento.
     * @var int
     */
    private $maxAge = 999999999;

    /**
     * Meta dados para a o checkout.
     * @var ArrayObject
     */
    private $metaData = null;

    /**
     * Construtor para o checkout dos itens.
     */
    public function __construct()
    {
        $this->items = new ArrayObject();
        $this->metaData = new ArrayObject();
    }

    /**
     * @see ICheckout::sendRequest()
     */
    public function sendRequest()
    {
        // Cria o documento xml para enviar o request do checkout.
        $xml = new DOMDocument('1.0', 'UTF-8');
        $checkout = $xml->createElement('checkout');

        // Cria a tag de receiver.
        if(!empty($this->getReceiver()))
        {
            $receiver = $xml->createElement('receiver');
            $receiver->appendChild($xml->createElement('email', $this->getReceiver()));
            $checkout->appendChild($receiver);
        }

        // Cria a tag currency
        $checkout->appendChild($xml->createElement('currency', $this->getCurrency()));

        // Varre os itens.
        $items = $xml->createElement('items');
        foreach($this->items as $item)
        {
            $i = $xml->createElement('item');
            $i->appendChild($xml->createElement('id', $item->getId()));
            $i->appendChild($xml->createElement('description', $item->getDescription()));
            $i->appendChild($xml->createElement('amount', $item->getAmount()));
            $i->appendChild($xml->createElement('quantity', $item->getQuantity()));

            if($item->getShippingCost() > 0)
                $i->appendChild($xml->createElement('shippingCost', $item->getShippingCost()));

            if($item->getWeight() > 0)
                $i->appendChild($xml->createElement('weight', $item->getWeight()));

            $items->appendChild($i);
        }
        $checkout->appendChild($items);

        // Cria a tag de referência caso esteja definida.
        if(!empty($this->getReference()))
        {
            $checkout->appendChild($xml->createElement('reference', $this->getReference()));
        }

        // Verifica se o cliente foi definido para criar as
        //  tags para o memso.
        if($this->getSender() != null)
        {
            $sender = $xml->createElement('sender');

            if(!empty($this->getSender()->getEmail()))
                $sender->appendChild($xml->createElement('email', $this->getSender()->getEmail()));

            if(!empty($this->getSender()->getName()))
                $sender->appendChild($xml->createElement('name', $this->getSender()->getName()));

            // Caso o comprador possua telefone
            if(!empty($this->getSender()->getPhoneAreaCode()) && !empty($this->getSender()->getPhoneNumber()))
            {
                $phone = $xml->createElement('phone');
                $phone->appendChild($xml->createElement('areaCode', $sender->getPhoneAreaCode()));
                $phone->appendChild($xml->createElement('number', $sender->getPhoneNumber()));
                $sender->appendChild($phone);
            }

            // Cria o tag para os documentos.
            if(!empty($this->getSender()->getDocumentType()) && !empty($this->getSender()->getDocumentValue()))
            {
                $documents = $xml->createElement('documents');
                $document = $xml->createElement('document');

                $document->appendChild($xml->createElement('type', $this->getSender()->getDocumentType()));
                $document->appendChild($xml->createElement('value', $this->getSender()->getDocumentType()));

                $documents->appendChild($document);
                $sender->appendChild($documents);
            }

            // Cria a tag para data de nascimento.
            if(!empty($this->getSender()->getBornDate()))
            {
                $sender->appendChild($xml->createElement('bornDate', $this->getSender()->getBornDate()));
            }

            // Cria a tag do comprador.
            $checkout->appendChild($sender);
        }

        // Se houver valor extra.
        if(!empty($this->getExtraAmount()))
            $checkout->appendChild($xml->createElement('extraAmount', $this->getExtraAmount()));

        // Se houver url de redirecionamento.
        if(!empty($this->getRedirectURL()))
            $checkout->appendChild($xml->createElement('redirectURL', $this->getRedirectURL()));

        // Se houver url de notificação.
        if(!empty($this->getNotificationURL()))
            $checkout->appendChild($xml->createElement('notificationURL', $this->getNotificationURL()));

        // Cria a tag para usos máximos.
        if($this->getMaxUses() != 999 && $this->getMaxUses() > 0)
            $checkout->appendChild($xml->createElement('maxUses', $this->getMaxUses()));

        // Cria a tag para tempo máximo de consulta.
        if($this->getMaxAge() != 999999999 && $this->getMaxAge() >= 30)
            $checkout->appendChild($xml->createElement('maxAge', $this->getMaxAge()));

        // Verifica a quantidade de metadados para serem adicionados
        //  caso não exista, pula a tag.
        if($this->metaData->count() > 0)
        {
            // Cria a tag para armazenar os metadados.
            // e cria todas as subtags para os metadados.
            $metaData = $xml->createElement('metadata');
            foreach($this->metaData as $key => $value)
            {
                $i = $xml->createElement('item');
                $i->appendChild($xml->createElement('key', $key));
                $i->appendChild($xml->createElement('value', $value));
                $metaData->appendChild($i);
            }
            $checkout->appendChild($metaData);
        }
        $xml->appendChild($checkout);

        // Obtém todo output para o xml de checkout.
        $checkoutString = $xml->saveXML();

        // Query para envio dos dados.
        $checkoutQuery = 'v2/checkout?email=' . PAG_EMAIL . '&token=' . PAG_TOKEN;

        // Retorna os dados para o checkout criado.
        $checkoutResponse = simplexml_load_string(Request::create(PAG_WS_URL, [
            'headers' => [
                'Content-Type' => 'application/xml; charset=UTF-8'
            ]
        ])->post($checkoutQuery, [
            'body' => $checkoutString
        ])->getBody()->getContents());

        // Retorna os dados de resposta do pagseguro para as informações de checkout.
        return json_decode(json_encode($checkoutResponse));
    }

    /**
     * Adiciona um novo item ao checkout.
     * @param CheckoutItem $item
     */
    public function addItem(CheckoutItem $item)
    {
        $this->items->append($item);
        return $this;
    }

    /**
     * Adiciona um novo meta key para o xml de checkout.
     * @param string $key
     * @param string $value
     */
    public function addMetaKey($key, $value)
    {
        $this->metaData->offsetSet($key, $value);
        return $this;
    }

    /**
     * Define o recebedor do pagamento.
     * @param string $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * Obtém o recebedor do pagamento.
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Define a moeda do pagamento.
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Obtém a moeda do pagamento.
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Define o código de referência.
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Obtém o código de referência
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Define o comprador.
     * @param CheckoutSender $sender
     */
    public function setSender(CheckoutSender $sender)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * Obtém o código de referência
     * @return CheckoutSender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Define o valor de desconto/acrescimo.
     * @param float $extraAmount
     */
    public function setExtraAmount($extraAmount)
    {
        $this->extraAmount = $extraAmount;
        return $this;
    }

    /**
     * Obtém o valor de desconto/acrescimo.
     * @return float
     */
    public function getExtraAmount()
    {
        return $this->extraAmount;
    }

    /**
     * Define o url de redirecionamento.
     * @param string $redirectURL
     */
    public function setRedirectURL($redirectURL)
    {
        $this->redirectURL = $redirectURL;
        return $this;
    }

    /**
     * Obtém o url de redirecionamento.
     * @return string
     */
    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    /**
     * Define o url de notificação.
     * @param string $notificationURL
     */
    public function setNotificationURL($notificationURL)
    {
        $this->notificationURL = $notificationURL;
        return $this;
    }

    /**
     * Obtém o url de notificação.
     * @return string
     */
    public function getNotificationURL()
    {
        return $this->notificationURL;
    }

    /**
     * Quantidade máxima de usos.
     * @param int $maxUses
     */
    public function setMaxUses($maxUses)
    {
        $this->maxUses = $maxUses;
        return $this;
    }

    /**
     * Quantidade máxima de usos.
     * @return int
     */
    public function getMaxUses()
    {
        return $this->maxUses;
    }

    /**
     * Idade máxima para o xml.
     * @param int $maxAge
     */
    public function setMaxAge($maxAge)
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * Obtém a idade máxima para o xml.
     * @return int
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }
}

