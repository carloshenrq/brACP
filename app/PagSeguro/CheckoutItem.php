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

/**
 * Classe construtura para o item de checkout do pagseguro.
 */
class CheckoutItem
{
    /**
     * Código do item que será utilizado para envio ao pagseguro.
     * @var string
     */
    private $id;

    /**
     * Descrição do item a ser enviado ao pagseguro.
     * @var string
     */
    private $description;

    /**
     * Valor unitário do produto.
     * @var float
     */
    private $amount;

    /**
     * Quantidade do produto.
     * @var float
     */
    private $quantity;

    /**
     * Custo de envio.
     * @var float
     */
    private $shippingCost;

    /**
     * Peso do item.
     * @var float
     */
    private $weight;

    /**
     * Construtor para o item.
     * @param string $id
     * @parma string $description
     * @parma float $amout
     * @parma float $quantity
     */
    public function __construct($id = null, $description = null, $amount = 0, $quantity = 0, $shippingCost = 0, $weight = 0)
    {
        // Define os dados para o item atual.
        $this->setId($id)
                ->setDescription($description)
                ->setAmount($amount)
                ->setQuantity($quantity)
                ->setShippingCost($shippingCost)
                ->setWeight($weight);
    }

    /**
     * Define o id do item a ser adicionado ao xml de checkout
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Define a descrição do item a ser adicionado ao xml de checkout
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Define o preço do item a ser adicionado ao xml de checkout
     * @param float $amout
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Define a quantidade do item a ser adicionado ao xml de checkout
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Define o custo de envio para o item.
     * @param float $shippingCost
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;
        return $this;
    }

    /**
     * Define o peso para o item.
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Obtém o id do item a ser adicionado ao xml.
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Obtém a descrição do item a ser adicionado ao xml.
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Obtém o valor do item a ser adicionado ao xml.
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Obtém a quantidade do item a ser adicionado ao xml.
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Obtém o custo de envio para o item.
     * @param float
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * Obtém o peso para o item.
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }
}


