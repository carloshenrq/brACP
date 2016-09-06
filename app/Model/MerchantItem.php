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

namespace Model;

use Doctrine\ORM\Mapping;

/**
 * @Entity
 * @Table(name="autotrade_data")
 */
class MerchantItem
{
    /**
     * @Id
     * @Column(name="char_id", type="integer")
     */
    protected $char_id;

    /**
     * @Id
     * @Column(name="itemkey", type="integer")
     */
    protected $itemkey;

    /**
     * @OneToOne(targetEntity="Cart")
     * @JoinColumn(name="itemkey", referencedColumnName="id", nullable=false)
     */
    protected $cart;

    /**
     * @Column(name="amount", type="integer")
     */
    protected $amount;

    /**
     * @Column(name="price", type="integer")
     */
    protected $price;

    /**
     * @ManyToOne(targetEntity="Merchant")
     * @JoinColumn(name="char_id", referencedColumnName="char_id", nullable=false)
     */
    protected $merchant;

    public function getChar_id()
    {
        return $this->char_id;
    }
    
    public function setChar_id($char_id)
    {
        return $this->char_id = $char_id;
    }

    public function getItemkey()
    {
        return $this->itemkey;
    }
    
    public function setItemkey($itemkey)
    {
        return $this->itemkey = $itemkey;
    }

    public function getCart()
    {
        return $this->cart;
    }
    
    public function setCart($cart)
    {
        return $this->cart = $cart;
    }

    public function getAmount()
    {
        return $this->amount;
    }
    
    public function setAmount($amount)
    {
        return $this->amount = $amount;
    }

    public function getPrice()
    {
        return $this->price;
    }
    
    public function setPrice($price)
    {
        return $this->price = $price;
    }

    public function getMerchant()
    {
        return $this->merchant;
    }
    
    public function setMerchant($merchant)
    {
        return $this->merchant = $merchant;
    }
}

