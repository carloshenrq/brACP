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
 * @Table(name="bracp_donations")
 */
class Donation
{
    /**
     * @Id
     * @Column(name="DonationID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Promotion")
     * @JoinColumn(name="PromotionID", referencedColumnName="PromotionID", nullable=true)
     */
    protected $promotion;

    /**
     * @Column(name="DonationDate", type="string", length=10)
     */
    protected $date;

    /**
     * @Column(name="DonationRefer", type="string", length=32)
     */
    protected $reference;

    /**
     * @Column(name="DonationDrive", type="string", length=50)
     */
    protected $drive = 'PAGSEGURO';

    /**
     * @Column(name="AccountID", type="integer")
     */
    protected $account_id;

    /**
     * @Column(name="DonationValue", type="decimal", precision=12, scale=2)
     */
    protected $value;

    /**
     * @Column(name="DonationBonus", type="integer")
     */
    protected $bonus;

    /**
     * @Column(name="DonationTotalValue", type="decimal", precision=12, scale=2)
     */
    protected $totalValue;

    /**
     * @Column(name="CheckoutCode", type="string", length=50, nullable=true)
     */
    protected $checkoutCode;

    /**
     * @Column(name="TransactionCode", type="string", length=50, nullable=true)
     */
    protected $transactionCode;

    /**
     * @Column(name="DonationReceiveBonus", type="boolean")
     */
    protected $receiveBonus = true;

    /**
     * @Column(name="DonationCompensate", type="boolean")
     */
    protected $compensate = false;

    /**
     * @Column(name="DonationStatus", type="string", length=50)
     */
    protected $status = 'INICIADA';

    /**
     * @Column(name="DonationPaymentDate", type="string", length=19, nullable=true)
     */
    protected $paymentDate = null;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getPromotion()
    {
        return $this->promotion;
    }
    
    public function setPromotion($promotion)
    {
        return $this->promotion = $promotion;
    }

    public function getDate()
    {
        return $this->date;
    }
    
    public function setDate($date)
    {
        return $this->date = $date;
    }

    public function getReference()
    {
        return $this->reference;
    }
    
    public function setReference($reference)
    {
        return $this->reference = $reference;
    }

    public function getDrive()
    {
        return $this->drive;
    }
    
    public function setDrive($drive)
    {
        return $this->drive = $drive;
    }

    public function getAccount_id()
    {
        return $this->account_id;
    }
    
    public function setAccount_id($account_id)
    {
        return $this->account_id = $account_id;
    }

    public function getValue()
    {
        return $this->value;
    }
    
    public function setValue($value)
    {
        return $this->value = $value;
    }

    public function getBonus()
    {
        return $this->bonus;
    }
    
    public function setBonus($bonus)
    {
        return $this->bonus = $bonus;
    }

    public function getTotalValue()
    {
        return $this->totalValue;
    }
    
    public function setTotalValue($totalValue)
    {
        return $this->totalValue = $totalValue;
    }

    public function getCheckoutCode()
    {
        return $this->checkoutCode;
    }
    
    public function setCheckoutCode($checkoutCode)
    {
        return $this->checkoutCode = $checkoutCode;
    }

    public function getTransactionCode()
    {
        return $this->transactionCode;
    }
    
    public function setTransactionCode($transactionCode)
    {
        return $this->transactionCode = $transactionCode;
    }

    public function getReceiveBonus()
    {
        return $this->receiveBonus;
    }
    
    public function setReceiveBonus($receiveBonus)
    {
        return $this->receiveBonus = $receiveBonus;
    }

    public function getCompensate()
    {
        return $this->compensate;
    }
    
    public function setCompensate($compensate)
    {
        return $this->compensate = $compensate;
    }

    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        return $this->status = $status;
    }

    public function getPaymentDate()
    {
        return $this->paymentDate;
    }
    
    public function setPaymentDate($paymentDate)
    {
        return $this->paymentDate = $paymentDate;
    }
}

