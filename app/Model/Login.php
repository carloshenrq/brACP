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

    public function getVip_time()
    {
        return $this->vip_time;
    }
    
    public function setVip_time($vip_time)
    {
        return $this->vip_time = $vip_time;
    }

    public function getOld_group()
    {
        return $this->old_group;
    }
    
    public function setOld_group($old_group)
    {
        return $this->old_group = $old_group;
    }
}

