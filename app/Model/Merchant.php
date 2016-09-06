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
 * @Table(name="autotrade_merchants")
 */
class Merchant
{
    /**
     * @Id
     * @Column(name="account_id", type="integer")
     */
    protected $account_id;

    /**
     * @Id
     * @Column(name="char_id", type="integer")
     */
    protected $char_id;

    /**
     * @ManyToOne(targetEntity="Char")
     * @JoinColumn(name="char_id", referencedColumnName="char_id", nullable=false)
     */
    protected $char;

    /**
     * @Column(name="sex", type="integer")
     */
    protected $sex;

    /**
     * @Column(name="title", type="string", length=80)
     */
    protected $title;

    public function getAccount_id()
    {
        return $this->account_id;
    }
    
    public function setAccount_id($account_id)
    {
        return $this->account_id = $account_id;
    }

    public function getChar_id()
    {
        return $this->char_id;
    }
    
    public function setChar_id($char_id)
    {
        return $this->char_id = $char_id;
    }

    public function getChar()
    {
        return $this->char;
    }
    
    public function setChar($char)
    {
        return $this->char = $char;
    }

    public function getSex()
    {
        return $this->sex;
    }
    
    public function setSex($sex)
    {
        return $this->sex = $sex;
    }

    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        return $this->title = $title;
    }
}

