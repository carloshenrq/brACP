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
 * @Table(name="bracp_account_confirm")
 */
class Confirmation
{
    /**
     * @Id
     * @Column(name="ConfirmationID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="AccountID", type="integer")
     */
    protected $account_id;

    /**
     * @Column(name="ConfirmationCode", type="string", length=32)
     */
    protected $code;

    /**
     * @Column(name="ConfirmationDate", type="string", length=19)
     */
    protected $date;

    /**
     * @Column(name="ConfirmationExpire", type="string", length=19)
     */
    protected $expire;

    /**
     * @Column(name="ConfirmationUsed", type="boolean")
     */
    protected $used;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getAccount_id()
    {
        return $this->account_id;
    }
    
    public function setAccount_id($account_id)
    {
        return $this->account_id = $account_id;
    }

    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode($code)
    {
        return $this->code = $code;
    }

    public function getDate()
    {
        return $this->date;
    }
    
    public function setDate($date)
    {
        return $this->date = $date;
    }

    public function getExpire()
    {
        return $this->expire;
    }
    
    public function setExpire($expire)
    {
        return $this->expire = $expire;
    }

    public function getUsed()
    {
        return $this->used;
    }
    
    public function setUsed($used)
    {
        return $this->used = $used;
    }
}

