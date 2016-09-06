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
 * @Table(name="cart_inventory")
 */
class Cart
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Char")
     * @JoinColumn(name="char_id", referencedColumnName="char_id", nullable=false)
     */
    protected $char;

    /**
     * @Column(name="nameid", type="integer")
     */
    protected $nameid;

    /**
     * @Column(name="amount", type="integer")
     */
    protected $amount;

    /**
     * @Column(name="equip", type="integer")
     */
    protected $equip;

    /**
     * @Column(name="identify", type="integer")
     */
    protected $identify;

    /**
     * @Column(name="refine", type="integer")
     */
    protected $refine;

    /**
     * @Column(name="attribute", type="integer")
     */
    protected $attribute;

    /**
     * @Column(name="card0", type="integer")
     */
    protected $card0;

    /**
     * @Column(name="card1", type="integer")
     */
    protected $card1;

    /**
     * @Column(name="card2", type="integer")
     */
    protected $card2;

    /**
     * @Column(name="card3", type="integer")
     */
    protected $card3;

    /**
     * @Column(name="expire_time", type="integer")
     */
    protected $expire_time;

    /**
     * @Column(name="bound", type="integer")
     */
    protected $bound;

    /**
     * @Column(name="unique_id", type="integer")
     */
    protected $unique_id;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getChar()
    {
        return $this->char;
    }
    
    public function setChar($char)
    {
        return $this->char = $char;
    }

    public function getNameid()
    {
        return $this->nameid;
    }
    
    public function setNameid($nameid)
    {
        return $this->nameid = $nameid;
    }

    public function getAmount()
    {
        return $this->amount;
    }
    
    public function setAmount($amount)
    {
        return $this->amount = $amount;
    }

    public function getEquip()
    {
        return $this->equip;
    }
    
    public function setEquip($equip)
    {
        return $this->equip = $equip;
    }

    public function getIdentify()
    {
        return $this->identify;
    }
    
    public function setIdentify($identify)
    {
        return $this->identify = $identify;
    }

    public function getRefine()
    {
        return $this->refine;
    }
    
    public function setRefine($refine)
    {
        return $this->refine = $refine;
    }

    public function getAttribute()
    {
        return $this->attribute;
    }
    
    public function setAttribute($attribute)
    {
        return $this->attribute = $attribute;
    }

    public function getCard0()
    {
        return $this->card0;
    }
    
    public function setCard0($card0)
    {
        return $this->card0 = $card0;
    }

    public function getCard1()
    {
        return $this->card1;
    }
    
    public function setCard1($card1)
    {
        return $this->card1 = $card1;
    }

    public function getCard2()
    {
        return $this->card2;
    }
    
    public function setCard2($card2)
    {
        return $this->card2 = $card2;
    }

    public function getCard3()
    {
        return $this->card3;
    }
    
    public function setCard3($card3)
    {
        return $this->card3 = $card3;
    }

    public function getExpire_time()
    {
        return $this->expire_time;
    }
    
    public function setExpire_time($expire_time)
    {
        return $this->expire_time = $expire_time;
    }

    public function getBound()
    {
        return $this->bound;
    }
    
    public function setBound($bound)
    {
        return $this->bound = $bound;
    }

    public function getUnique_id()
    {
        return $this->unique_id;
    }
    
    public function setUnique_id($unique_id)
    {
        return $this->unique_id = $unique_id;
    }
}

