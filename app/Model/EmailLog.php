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
 * @Table(name="bracp_change_mail_log")
 */
class EmailLog
{
    /**
     * @Id
     * @Column(name="EmailLogID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Login")
     * @JoinColumn(name="AccountID", referencedColumnName="account_id")
     */
    protected $account;

    /**
     * @Column(name="EmailFrom", type="string", length=39)
     */
    protected $from;

    /**
     * @Column(name="EmailTo", type="string", length=39)
     */
    protected $to;

    /**
     * @Column(name="EmailLogDate", type="string", length=19)
     */
    protected $date;

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

    public function getFrom()
    {
        return $this->from;
    }
    
    public function setFrom($from)
    {
        return $this->from = $from;
    }

    public function getTo()
    {
        return $this->to;
    }
    
    public function setTo($to)
    {
        return $this->to = $to;
    }

    public function getDate()
    {
        return $this->date;
    }
    
    public function setDate($date)
    {
        return $this->date = $date;
    }
}

