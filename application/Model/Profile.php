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
 * Classe de entidade aos perfis que existem no bracp.
 *
 * @EntityListeners({"ProfileListener"})
 * @Entity(repositoryClass="Model\ProfileRepository")
 * @Table(name="bracp_profile", uniqueConstraints={@UniqueConstraint(name="bracp_profile_u01", columns={"Email"}), @UniqueConstraint(name="bracp_profile_u02", columns={"FacebookID"})})
 */
class Profile
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="ProfileID", type="integer", nullable=false)
     */
    public $id;

    /**
     * @Column(name="Name", type="string", length=256, nullable=false, options={"default":""})
     */
    public $name;

    /**
     * @Column(name="Gender", type="string", length=1, nullable=false, options={"default":"O"})
     */
    public $gender;

    /**
     * @Column(name="Birthdate", type="date", nullable=false)
     */
    public $birthdate;

    /**
     * @Column(name="Email", type="string", length=60, nullable=true, options={"default":null})
     */
    public $email;

    /**
     * @Column(name="Password", type="string", length=128, nullable=true, options={"default":null})
     */
    public $password;

    /**
     * @Column(name="AvatarURL", type="string", length=65535, nullable=true, options={"default":null})
     */
    public $avatarUrl;

    /**
     * @Column(name="AboutMe", type="string", length=65535, nullable=true, options={"default":null})
     */
    public $aboutMe;

    /**
     * @Column(name="CanCreateAccount", type="boolean", nullable=false, options={"default":true})
     */
    public $canCreateAccount;

    /**
     * @Column(name="CanReportProfiles", type="boolean", nullable=false, options={"default":true})
     */
    public $canReportProfiles;

    /**
     * @Column(name="Blocked", type="boolean", nullable=false, options={"default":false})
     */
    public $blocked;

    /**
     * @Column(name="BlockedReason", type="string", length=2048, nullable=true, options={"default":null})
     */
    public $blockedReason;

    /**
     * @Column(name="BlockedUntil", type="integer", nullable=true)
     */
    public $blockedUntil;

    /**
     * @Column(name="Verified", type="boolean", nullable=false, options={"default":false})
     */
    public $verified;

    /**
     * @Column(name="RegisterDate", type="datetime", nullable=false, options={"default" : false})
     */
    public $registerDate;

    /**
     * @Column(name="FacebookID", type="string", length=30, nullable=true, options={"default":null})
     */
    public $facebookId;

    /**
     * @Column(name="Visibility", type="string", length=1, nullable=false, options={"default":"M"})
     */
    public $visibility;

    /**
     * @Column(name="ShowBirthdate", type="string", length=1, nullable=false, options={"default":"M"})
     */
    public $showBirthdate;

    /**
     * @Column(name="ShowEmail", type="string", length=1, nullable=false, options={"default":"M"})
     */
    public $showEmail;

    /**
     * @Column(name="ShowFacebook", type="string", length=1, nullable=false, options={"default":"M"})
     */
    public $showFacebook;

    /**
     * @Column(name="AllowMessage", type="string", length=1, nullable=false, options={"default":"M"})
     */
    public $allowMessage;

    /**
     * @Column(name="Privileges", type="string", length=1, nullable=false, options={"default":"U"})
     */
    public $privileges;

    /**
     * @Column(name="GAAllowed", type="boolean", nullable=false, options={"default":false})
     */
    public $gaAllowed;

    /**
     * @Column(name="GASecret", type="string", length=16, nullable=true)
     */
    public $gaSecret;

}
