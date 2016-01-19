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
namespace PagSeguro;

/**
 * Classe construtura para o item de checkout do pagseguro.
 */
class CheckoutSender
{
    /**
     * Endereço de email do comprador.
     * @var string
     */
    private $email;

    /**
     * Nome do comprador.
     * @var string
     */
    private $name;

    /**
     * Código de área do comprador.
     * @var int
     */
    private $phoneAreaCode;

    /**
     * Número do telefone do comprador.
     * @var int
     */
    private $phoneNumber;

    /**
     * Tipo de documento do comprador
     * @var string
     */
    private $documentType;

    /**
     * Conteudo do documento para o comprador.
     * @var string
     */
    private $documentValue;

    /**
     * Data de nascimento para o comprador.
     * @var string
     */
    private $bornDate;

    public function __construct($email = null, $name = null, $phoneAreaCode = null,
                                $phoneNumber = null, $documentType = null, $documentValue = null,
                                $bornDate = null)
    {
        // Define os dados do comprador.
        $this->setEmail($email)
                ->setName($name)
                ->setPhoneAreaCode($phoneAreaCode)
                ->setPhoneNumber($phoneNumber)
                ->setDocumentType($documentType)
                ->setDocumentValue($documentValue)
                ->setBornDate($bornDate);
    }

    /**
     * Define o email do comprador.
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Obtém o email do comprador
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Define o nome do comprador.
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Obtém o nome do comprador
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Define o número do código de área do comprador.
     * @param string $phoneAreaCode
     */
    public function setPhoneAreaCode($phoneAreaCode)
    {
        $this->phoneAreaCode = $phoneAreaCode;
        return $this;
    }

    /**
     * Obtém o número do código de área do comprador
     * @return string
     */
    public function getPhoneAreaCode()
    {
        return $this->phoneAreaCode;
    }

    /**
     * Define o número de telefone do comprador
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Obtém o número de telefone do comprador
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Define o tipo de documento para o comprador
     * @param string $documentType
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
        return $this;
    }

    /**
     * Obtém o tipo de documento do comprador
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * Define o valor de documento para o comprador
     * @param string $documentValue
     */
    public function setDocumentValue($documentValue)
    {
        $this->documentValue = $documentValue;
        return $this;
    }

    /**
     * Obtém o valor de documento do comprador
     * @return string
     */
    public function getDocumentValue()
    {
        return $this->documentValue;
    }

    /**
     * Define a data de nascimento do comprador
     * @param string $bornDate
     */
    public function setBornDate($bornDate)
    {
        $this->bornDate = $bornDate;
        return $this;
    }

    /**
     * Obtém a data de nascimento do comprador
     * @return string
     */
    public function getBornDate()
    {
        return $this->bornDate;
    }
}


