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
 * @Table(name="bracp_recover")
 */
class Recover
{
    /**
     * @Id
     * @Column(name="RecoverID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="AccountID", type="integer")
     */
    protected $account_id;

    /**
     * @Column(name="RecoverCode", type="string", length=32)
     */
    protected $code;

    /**
     * @Column(name="RecoverDate", type="string", length=19)
     */
    protected $date;

    /**
     * @Column(name="RecoverExpire", type="string", length=19)
     */
    protected $expire;

    /**
     * @Column(name="RecoverUsed", type="boolean")
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

    public function getAccount()
    {
        return $this->account;
    }
    
    public function setAccount($account)
    {
        return $this->account = $account;
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

