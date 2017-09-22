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
 * Classe entidade para o login do usuário.
 *
 * @Entity
 * @Table(name="login")
 */
class RAG_Login
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="account_id", type="integer", nullable=false)
     */
    public $account_id;

    /**
     * @Column(name="userid", type="string", length=23, nullable=false, options={"default":""})
     */
    public $userid;
    
    /**
     * @Column(name="user_pass", type="string", length=32, nullable=false, options={"default":""})
     */
    public $user_pass;
    
    /**
     * @Column(name="sex", type="string", length=1, nullable=false, options={"default":""})
     */
    public $sex;
    
    /**
     * @Column(name="email", type="string", length=39, nullable=false, options={"default":""})
     */
    public $email;

    /**
     * @Column(name="group_id", type="integer", nullable=false, options={"default":0})
     */
    public $group_id;
    
    /**
     * @Column(name="state", type="integer", nullable=false, options={"default":0})
     */
    public $state;
    
    /**
     * @Column(name="unban_time", type="integer", nullable=false, options={"default":0})
     */
    public $unban_time;
    
    /**
     * @Column(name="logincount", type="integer", nullable=false, options={"default":0})
     */
    public $logincount;

    /**
     * @Column(name="lastlogin", type="datetime", nullable=false)
     */
    public $lastlogin;

    /**
     * @Column(name="last_ip", type="string", length=100, nullable=false, options={"default":""})
     */
    public $last_ip;

    /**
     * @Column(name="mac_address", type="string", length=18, nullable=false, options={"default":""})
     */
    public $mac_address;

    /**
     * @Column(name="birthdate", type="date", nullable=false)
     */
    public $birthdate;
    
    /**
     * @Column(name="character_slots", type="integer", nullable=false, options={"default":0})
     */
    public $character_slots;

    /**
     * @Column(name="pincode", type="string", length=4, nullable=false, options={"default":""})
     */
    public $pincode;

    /**
     * @Column(name="pincode_change", type="integer", nullable=false, options={"default":0})
     */
    public $pincode_change;

    /**
     * @Column(name="last_password_change", type="integer", nullable=false, options={"default":0})
     */
    public $last_password_change;
}
