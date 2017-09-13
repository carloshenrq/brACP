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

use \Doctrine\ORM\Mapping;

/**
 * Classe para verificação de dados de perfil.
 *
 * @EntityListeners({"ProfileVerifyListener"})
 * @Entity(repositoryClass="Model\ProfileVerifyRepository")
 * @Table(name="bracp_profile_verify", uniqueConstraints={@UniqueConstraint(name="bracp_profile_verify_u01", columns={"VerifyCode"})})
 */
class ProfileVerify
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="VerifyID", type="integer", nullable=false)
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="Profile")
     * @JoinColumn(name="ProfileID", referencedColumnName="ProfileID", nullable=false)
     */
    public $profile;

    /**
     * @Column(name="VerifyEmail", type="string", length=60, nullable=false)
     */
    public $email;

    /**
     * @Column(name="VerifyCode", type="string", length=32, nullable=false)
     */
    public $code;

    /**
     * @Column(name="VerifyProfile", type="boolean", nullable=false, options={"default":false})
     */
    public $verifyProfile;

    /**
     * @Column(name="VerifyUsed", type="boolean", nullable=false, options={"default":false})
     */
    public $used;

    /**
     * @Column(name="VerifyUsedDt", type="datetime", nullable=true, options={"default":null})
     */
    public $usedDate;

    /**
     * @Column(name="VerifyExpireDt", type="datetime", nullable=true, options={"default":null})
     */
    public $expireDate;
}
