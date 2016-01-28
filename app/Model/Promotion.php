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
 * @Table(name="bracp_donations_promo")
 */
class Promotion
{
    /**
     * @Id
     * @Column(name="PromotionID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="PromotionDescription", type="string", length=1024)
     */
    protected $description;

    /**
     * @Column(name="BonusMultiply", type="integer")
     */
    protected $bonusMultiply;

    /**
     * @Column(name="PromotionStartDate", type="string", length=10)
     */
    protected $startDate;

    /**
     * @Column(name="PromotionEndDate", type="string", length=10)
     */
    protected $endDate;

    /**
     * @Column(name="PromotionCanceled", type="boolean")
     */
    protected $canceled = false;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        return $this->description = $description;
    }

    public function getBonusMultiply()
    {
        return $this->bonusMultiply;
    }
    
    public function setBonusMultiply($bonusMultiply)
    {
        return $this->bonusMultiply = $bonusMultiply;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }
    
    public function setStartDate($startDate)
    {
        return $this->startDate = $startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }
    
    public function setEndDate($endDate)
    {
        return $this->endDate = $endDate;
    }

    public function getCanceled()
    {
        return $this->canceled;
    }
    
    public function setCanceled($canceled)
    {
        return $this->canceled = $canceled;
    }
}

