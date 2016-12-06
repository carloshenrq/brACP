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
     * @Column(name="sex", type="string", options={"default":"M"})
     */
    protected $sex = 'M';

    /**
     * @Column(name="email", type="string", length=39)
     */
    protected $email;

    /**
     * @Column(name="group_id", type="integer", options={"default":0})
     */
    protected $group_id = 0;

    /**
     * @Column(name="state", type="integer", options={"default":0})
     */
    protected $state = 0;

    /**
     * @Column(name="unban_time", type="integer", options={"default":0})
     */
    protected $unban_time = 0;

    /**
     * @Column(name="expiration_time", type="integer", options={"default":0})
     */
    protected $expiration_time = 0;

    /**
     * @Column(name="logincount", type="integer", options={"default":0})
     */
    protected $logincount = 0;

    /**
     * @Column(name="lastlogin", type="string", length=18, options={"default":"1111-11-11 00:00:00"})
     */
    protected $lastlogin = '1111-11-11 00:00:00';

    /**
     * @Column(name="last_ip", type="string", length=100, options={"default":"127.0.0.1"})
     */
    protected $last_ip = '127.0.0.1';

    /**
     * @Column(name="birthdate", type="string", length=10, options={"default":"1111-11-11"})
     */
    protected $birthdate = '1111-11-11';

    /**
     * @Column(name="character_slots", type="integer", options={"default":0})
     */
    protected $character_slots = 0;

    /**
     * @Column(name="pincode", type="string", length=4, options={"default":""})
     */
    protected $pincode = '';

    /**
     * @Column(name="pincode_change", type="integer", options={"default":0})
     */
    protected $pincode_change = 0;

    /**
     * @Column(name="last_password_change", type="integer", options={"default":0})
     */
    protected $last_password_change = 0;

    public function getAccount_id()
    {
        return $this->account_id;
    }
    
    public function setAccount_id($account_id)
    {
        return $this->account_id = $account_id;
    }

    public function getUserid()
    {
        return $this->userid;
    }
    
    public function setUserid($userid)
    {
        return $this->userid = $userid;
    }

    public function getUser_pass()
    {
        return $this->user_pass;
    }
    
    public function setUser_pass($user_pass)
    {
        return $this->user_pass = $user_pass;
    }

    public function getSex()
    {
        return $this->sex;
    }
    
    public function setSex($sex)
    {
        return $this->sex = $sex;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        return $this->email = $email;
    }

    public function getGroup_id()
    {
        return $this->group_id;
    }
    
    public function setGroup_id($group_id)
    {
        return $this->group_id = $group_id;
    }

    public function getState()
    {
        return $this->state;
    }
    
    public function setState($state)
    {
        return $this->state = $state;
    }

    public function getUnban_time()
    {
        return $this->unban_time;
    }
    
    public function setUnban_time($unban_time)
    {
        return $this->unban_time = $unban_time;
    }

    public function getExpiration_time()
    {
        return $this->expiration_time;
    }
    
    public function setExpiration_time($expiration_time)
    {
        return $this->expiration_time = $expiration_time;
    }

    public function getLogincount()
    {
        return $this->logincount;
    }
    
    public function setLogincount($logincount)
    {
        return $this->logincount = $logincount;
    }

    public function getLastlogin()
    {
        return $this->lastlogin;
    }
    
    public function setLastlogin($lastlogin)
    {
        return $this->lastlogin = $lastlogin;
    }

    public function getLast_ip()
    {
        return $this->last_ip;
    }
    
    public function setLast_ip($last_ip)
    {
        return $this->last_ip = $last_ip;
    }

    public function getBirthdate()
    {
        return $this->birthdate;
    }
    
    public function setBirthdate($birthdate)
    {
        return $this->birthdate = $birthdate;
    }

    public function getCharacter_slots()
    {
        return $this->character_slots;
    }
    
    public function setCharacter_slots($character_slots)
    {
        return $this->character_slots = $character_slots;
    }

    public function getPincode()
    {
        return $this->pincode;
    }
    
    public function setPincode($pincode)
    {
        return $this->pincode = $pincode;
    }

    public function getPincode_change()
    {
        return $this->pincode_change;
    }
    
    public function setPincode_change($pincode_change)
    {
        return $this->pincode_change = $pincode_change;
    }

    public function getLast_password_change()
    {
        return $this->last_password_change;
    }
    
    public function setLast_password_change($last_password_change)
    {
        return $this->last_password_change = $last_password_change;
    }
}

