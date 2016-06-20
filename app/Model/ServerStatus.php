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
 * @Table(name="bracp_server_status")
 */
class ServerStatus
{
    /**
     * @Id
     * @Column(name="StatusID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="ServerIndex", type="integer")
     */
    protected $index;

    /**
     * @Column(name="ServerName", type="string", length=100)
     */
    protected $name;

    /**
     * @Column(name="MapStatus", type="boolean")
     */
    protected $map;

    /**
     * @Column(name="CharStatus", type="boolean")
     */
    protected $char;

    /**
     * @Column(name="LoginStatus", type="boolean")
     */
    protected $login;

    /**
     * @Column(name="StatusTime", type="string", length=19)
     */
    protected $time;

    /**
     * @Column(name="StatusExpire", type="string", length=19)
     */
    protected $expire;

    /**
     * @Column(name="PlayerCount", type="integer")
     */
    protected $playerCount;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getIndex()
    {
        return $this->index;
    }
    
    public function setIndex($index)
    {
        return $this->index = $index;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        return $this->name = $name;
    }

    public function getMap()
    {
        return $this->map;
    }
    
    public function setMap($map)
    {
        return $this->map = $map;
    }

    public function getChar()
    {
        return $this->char;
    }
    
    public function setChar($char)
    {
        return $this->char = $char;
    }

    public function getLogin()
    {
        return $this->login;
    }
    
    public function setLogin($login)
    {
        return $this->login = $login;
    }

    public function getTime()
    {
        return $this->time;
    }
    
    public function setTime($time)
    {
        return $this->time = $time;
    }

    public function getExpire()
    {
        return $this->expire;
    }
    
    public function setExpire($expire)
    {
        return $this->expire = $expire;
    }

    public function getPlayerCount()
    {
        return $this->playerCount;
    }
    
    public function setPlayerCount($playerCount)
    {
        return $this->playerCount = $playerCount;
    }
}

