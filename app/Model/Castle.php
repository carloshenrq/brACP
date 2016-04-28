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
 * @Table(name="castle_db")
 */
class Castle
{
    /**
     * @Id
     * @Column(name="CastleID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="MapName", type="string", length=20)
     */
    protected $map;

    /**
     * @Column(name="CastleName", type="string", length=30)
     */
    protected $name;

    /**
     * @Column(name="OnGuildBreakEventName", type="string", length=30)
     */
    protected $breakEvent;

    /**
     * @Column(name="Flag", type="integer")
     */
    protected $flag;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getMap()
    {
        return $this->map;
    }
    
    public function setMap($map)
    {
        return $this->map = $map;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        return $this->name = $name;
    }

    public function getBreakEvent()
    {
        return $this->breakEvent;
    }
    
    public function setBreakEvent($breakEvent)
    {
        return $this->breakEvent = $breakEvent;
    }

    public function getFlag()
    {
        return $this->flag;
    }
    
    public function setFlag($flag)
    {
        return $this->flag = $flag;
    }
}

