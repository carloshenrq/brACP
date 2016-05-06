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
 * @Table(name="bracp_compensations")
 */
class Compensate
{
    /**
     * @Id
     * @Column(name="CompensateID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Donation")
     * @JoinColumn(name="DonationID", referencedColumnName="DonationID", nullable=false)
     */
    protected $donation;

    /**
     * @ManyToOne(targetEntity="Login")
     * @JoinColumn(name="AccountID", referencedColumnName="account_id", nullable=false)
     */
    protected $account;

    /**
     * @Column(name="UserID", type="string", length=23)
     */
    protected $userid;

    /**
     * @Column(name="CompensatePending", type="boolean")
     */
    protected $pending;

    /**
     * @Column(name="CompensateDate", type="string", length=10, nullable=true)
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

    public function getDonation()
    {
        return $this->donation;
    }
    
    public function setDonation($donation)
    {
        return $this->donation = $donation;
    }

    public function getAccount()
    {
        return $this->account;
    }
    
    public function setAccount($account)
    {
        return $this->account = $account;
    }

    public function getUserid()
    {
        return $this->userid;
    }
    
    public function setUserid($userid)
    {
        return $this->userid = $userid;
    }

    public function getPending()
    {
        return $this->pending;
    }
    
    public function setPending($pending)
    {
        return $this->pending = $pending;
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

