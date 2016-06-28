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
     * @Column(name="ReceiverID", type="string", length=50)
     */
    protected $receiverId;

    /**
     * @Column(name="ReceiverMail", type="string", length=100)
     */
    protected $receiverMail;

    /**
     * @Column(name="SandboxMode", type="boolean")
     */
    protected $sandboxMode;

    /**
     * @Column(name="TransactionDrive", type="string", length=20)
     */
    protected $transactionDrive;

    /**
     * @Column(name="TransactionCode", type="string", length=100)
     */
    protected $transactionCode;

    /**
     * @Column(name="TransactionType", type="string", length=50)
     */
    protected $transactionType;

    /**
     * @Column(name="TransactionUserID", type="string", length=23)
     */
    protected $transactionUserID;

    /**
     * @Column(name="PayerID", type="string", length=50)
     */
    protected $payerID;

    /**
     * @Column(name="PayerMail", type="string", length=100)
     */
    protected $payerMail;

    /**
     * @Column(name="PayerStatus", type="string", length=30)
     */
    protected $payerStatus;

    /**
     * @Column(name="PayerName", type="string", length=100)
     */
    protected $payerName;

    /**
     * @Column(name="PayerCountry", type="string", length=50)
     */
    protected $payerCountry;

    /**
     * @Column(name="PayerState", type="string", length=50)
     */
    protected $payerState;

    /**
     * @Column(name="PayerCity", type="string", length=50)
     */
    protected $payerCity;

    /**
     * @Column(name="PayerAddress", type="string", length=200)
     */
    protected $payerAddress;

    /**
     * @Column(name="PayerZipCode", type="string", length=30)
     */
    protected $payerZipCode;

    /**
     * @Column(name="PayerAddressConfirmed", type="boolean")
     */
    protected $payerAddressConfirmed;

    /**
     * @Column(name="DonationValue", type="decimal", precision=12, scale=2)
     */
    protected $donationValue;

    /**
     * @Column(name="DonationPayment", type="string", length=20)
     */
    protected $donationPayment;

    /**
     * @Column(name="DonationStatus", type="string", length=30)
     */
    protected $donationStatus;

    /**
     * @Column(name="DonationType", type="string", length=30)
     */
    protected $donationType;

    /**
     * @Column(name="VerifySign", type="text")
     */
    protected $verifySign;

    /**
     * @Column(name="DonationCompensate", type="boolean")
     */
    protected $compensate;

    /*
     * @Column(name="DonationAccountID", type="integer", nullable=true)
     */
    protected $account_id;

    /**
     * @Column(name="DonationServerName", type="string", length=30, nullable=true)
     */
    protected $donationServer;

    /**
     * @Column(name="DonationSQLHost", type="string", length=50, nullable=true)
     */
    protected $sqlHost;

    /**
     * @Column(name="DonationSQLUser", type="string", length=50, nullable=true)
     */
    protected $sqlUser;

    /**
     * @Column(name="DonationSQLPass", type="string", length=50, nullable=true)
     */
    protected $sqlPass;

    /**
     * @Column(name="DonationSQLDBName", type="string", length=50, nullable=true)
     */
    protected $sqlDBName;

    /**
     * @Column(name="DonationCompensateVar", type="string", length=50, nullable=true)
     */
    protected $compensateVar;

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

    public function getReceiverId()
    {
        return $this->receiverId;
    }
    
    public function setReceiverId($receiverId)
    {
        return $this->receiverId = $receiverId;
    }

    public function getReceiverMail()
    {
        return $this->receiverMail;
    }
    
    public function setReceiverMail($receiverMail)
    {
        return $this->receiverMail = $receiverMail;
    }

    public function getSandboxMode()
    {
        return $this->sandboxMode;
    }
    
    public function setSandboxMode($sandboxMode)
    {
        return $this->sandboxMode = $sandboxMode;
    }

    public function getTransactionDrive()
    {
        return $this->transactionDrive;
    }
    
    public function setTransactionDrive($transactionDrive)
    {
        return $this->transactionDrive = $transactionDrive;
    }

    public function getTransactionCode()
    {
        return $this->transactionCode;
    }
    
    public function setTransactionCode($transactionCode)
    {
        return $this->transactionCode = $transactionCode;
    }

    public function getTransactionType()
    {
        return $this->transactionType;
    }
    
    public function setTransactionType($transactionType)
    {
        return $this->transactionType = $transactionType;
    }

    public function getTransactionUserID()
    {
        return $this->transactionUserID;
    }
    
    public function setTransactionUserID($transactionUserID)
    {
        return $this->transactionUserID = $transactionUserID;
    }

    public function getPayerID()
    {
        return $this->payerID;
    }
    
    public function setPayerID($payerID)
    {
        return $this->payerID = $payerID;
    }

    public function getPayerMail()
    {
        return $this->payerMail;
    }
    
    public function setPayerMail($payerMail)
    {
        return $this->payerMail = $payerMail;
    }

    public function getPayerStatus()
    {
        return $this->payerStatus;
    }
    
    public function setPayerStatus($payerStatus)
    {
        return $this->payerStatus = $payerStatus;
    }

    public function getPayerName()
    {
        return $this->payerName;
    }
    
    public function setPayerName($payerName)
    {
        return $this->payerName = $payerName;
    }

    public function getPayerCountry()
    {
        return $this->payerCountry;
    }
    
    public function setPayerCountry($payerCountry)
    {
        return $this->payerCountry = $payerCountry;
    }

    public function getPayerState()
    {
        return $this->payerState;
    }
    
    public function setPayerState($payerState)
    {
        return $this->payerState = $payerState;
    }

    public function getPayerCity()
    {
        return $this->payerCity;
    }
    
    public function setPayerCity($payerCity)
    {
        return $this->payerCity = $payerCity;
    }

    public function getPayerAddress()
    {
        return $this->payerAddress;
    }
    
    public function setPayerAddress($payerAddress)
    {
        return $this->payerAddress = $payerAddress;
    }

    public function getPayerZipCode()
    {
        return $this->payerZipCode;
    }
    
    public function setPayerZipCode($payerZipCode)
    {
        return $this->payerZipCode = $payerZipCode;
    }

    public function getPayerAddressConfirmed()
    {
        return $this->payerAddressConfirmed;
    }
    
    public function setPayerAddressConfirmed($payerAddressConfirmed)
    {
        return $this->payerAddressConfirmed = $payerAddressConfirmed;
    }

    public function getDonationValue()
    {
        return $this->donationValue;
    }
    
    public function setDonationValue($donationValue)
    {
        return $this->donationValue = $donationValue;
    }

    public function getDonationPayment()
    {
        return $this->donationPayment;
    }
    
    public function setDonationPayment($donationPayment)
    {
        return $this->donationPayment = $donationPayment;
    }

    public function getDonationStatus()
    {
        return $this->donationStatus;
    }
    
    public function setDonationStatus($donationStatus)
    {
        return $this->donationStatus = $donationStatus;
    }

    public function getDonationType()
    {
        return $this->donationType;
    }
    
    public function setDonationType($donationType)
    {
        return $this->donationType = $donationType;
    }

    public function getVerifySign()
    {
        return $this->verifySign;
    }
    
    public function setVerifySign($verifySign)
    {
        return $this->verifySign = $verifySign;
    }

    public function getCompensate()
    {
        return $this->compensate;
    }
    
    public function setCompensate($compensate)
    {
        return $this->compensate = $compensate;
    }

    public function getAccount_id()
    {
        return $this->account_id;
    }
    
    public function setAccount_id($account_id)
    {
        return $this->account_id = $account_id;
    }

    public function getDonationServer()
    {
        return $this->donationServer;
    }
    
    public function setDonationServer($donationServer)
    {
        return $this->donationServer = $donationServer;
    }

    public function getSqlHost()
    {
        return $this->sqlHost;
    }
    
    public function setSqlHost($sqlHost)
    {
        return $this->sqlHost = $sqlHost;
    }

    public function getSqlUser()
    {
        return $this->sqlUser;
    }
    
    public function setSqlUser($sqlUser)
    {
        return $this->sqlUser = $sqlUser;
    }

    public function getSqlPass()
    {
        return $this->sqlPass;
    }
    
    public function setSqlPass($sqlPass)
    {
        return $this->sqlPass = $sqlPass;
    }

    public function getSqlDBName()
    {
        return $this->sqlDBName;
    }
    
    public function setSqlDBName($sqlDBName)
    {
        return $this->sqlDBName = $sqlDBName;
    }
}
