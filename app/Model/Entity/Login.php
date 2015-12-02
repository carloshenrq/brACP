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

namespace Model\Entity;

use Model\Entity;
use Doctrine\ORM\Mapping;

/**
 * @Entity
 * @Table(name="login")
 */
class Login
{
    /**
     * @Id
     * @Column(name="account_id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $account_id;

    /**
     * @Column(name="userid", type="string", length=23)
     */
    protected $userid;

    /**
     * @Column(name="user_pass", type="string", length=32)
     */
    protected $user_pass;

    /**
     * @Column(name="sex", type="string", columnDefinition="ENUM('M','F','S'), options={"default":"M"}")
     */
    protected $sex;

    /**
     * @Column(name="email", type="string", length=39)
     */
    protected $email;

    /**
     * @Column(name="group_id", type="integer")
     */
    protected $group_id;

    /**
     * @Column(name="state", type="integer")
     */
    protected $state;

    /**
     * @Column(name="unban_time", type="integer")
     */
    protected $unban_time;

    /**
     * @Column(name="expiration_time", type="integer")
     */
    protected $expiration_time;

    /**
     * @Column(name="logincount", type="integer")
     */
    protected $logincount;

    /**
     * @Column(name="lastlogin", type="datetime")
     */
    protected $lastlogin;

    /**
     * @Column(name="last_ip", type="string", length=100)
     */
    protected $last_ip;

    /**
     * @Column(name="birthdate", type="string", length=10)
     */
    protected $birthdate;

    /**
     * @Column(name="birthdate", type="string", length=10, options={"default":"0000-00-00"}")
     */
    protected $birthdate;

    /**
     * @Column(name="character_slots", type="integer")
     */
    protected $character_slots;

    /**
     * @Column(name="pincode", type="string", length=4)
     */
    protected $pincode;

    /**
     * @Column(name="pincode_change", type="integer")
     */
    protected $pincode_change;

    /**
     * @Column(name="vip_time", type="integer")
     */
    protected $vip_time;

    /**
     * @Column(name="old_group", type="integer")
     */
    protected $old_group;
}

