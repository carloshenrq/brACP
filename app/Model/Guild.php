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
 * @Table(name="guild")
 */
class Guild
{
    /**
     * @Id
     * @Column(name="guild_id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="name", type="string", length=24)
     */
    protected $name;

    /**
     * @OneToOne(targetEntity="Char")
     * @JoinColumn(name="char_id", referencedColumnName="char_id", nullable=false)
     */
    protected $character;

    /**
     * @Column(name="master", type="string", length=24)
     */
    protected $master;

    /**
     * @Column(name="guild_lv", type="integer")
     */
    protected $guild_lv;

    /**
     * @Column(name="connect_member", type="integer")
     */
    protected $connect_member;

    /**
     * @Column(name="max_member", type="integer")
     */
    protected $max_member;

    /**
     * @Column(name="average_lv", type="integer")
     */
    protected $average_lv;

    /**
     * @Column(name="exp", type="integer")
     */
    protected $exp;

    /**
     * @Column(name="next_exp", type="integer")
     */
    protected $next_exp;

    /**
     * @Column(name="skill_point", type="integer")
     */
    protected $skill_point;

    /**
     * @Column(name="mes1", type="string", length=60)
     */
    protected $mes1;

    /**
     * @Column(name="mes2", type="string", length=120)
     */
    protected $mes2;

    /**
     * @Column(name="emblem_len", type="integer")
     */
    protected $emblem_len;

    /**
     * @Column(name="emblem_id", type="integer")
     */
    protected $emblem_id;

    /**
     * @Column(name="emblem_data", type="blob")
     */
    protected $emblem_data;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        return $this->name = $name;
    }

    public function getCharacter()
    {
        return $this->character;
    }
    
    public function setCharacter($character)
    {
        return $this->character = $character;
    }

    public function getMaster()
    {
        return $this->master;
    }
    
    public function setMaster($master)
    {
        return $this->master = $master;
    }

    public function getGuild_lv()
    {
        return $this->guild_lv;
    }
    
    public function setGuild_lv($guild_lv)
    {
        return $this->guild_lv = $guild_lv;
    }

    public function getConnect_member()
    {
        return $this->connect_member;
    }
    
    public function setConnect_member($connect_member)
    {
        return $this->connect_member = $connect_member;
    }

    public function getMax_member()
    {
        return $this->max_member;
    }
    
    public function setMax_member($max_member)
    {
        return $this->max_member = $max_member;
    }

    public function getAverage_lv()
    {
        return $this->average_lv;
    }
    
    public function setAverage_lv($average_lv)
    {
        return $this->average_lv = $average_lv;
    }

    public function getExp()
    {
        return $this->exp;
    }
    
    public function setExp($exp)
    {
        return $this->exp = $exp;
    }

    public function getNext_exp()
    {
        return $this->next_exp;
    }
    
    public function setNext_exp($next_exp)
    {
        return $this->next_exp = $next_exp;
    }

    public function getSkill_point()
    {
        return $this->skill_point;
    }
    
    public function setSkill_point($skill_point)
    {
        return $this->skill_point = $skill_point;
    }

    public function getMes1()
    {
        return $this->mes1;
    }
    
    public function setMes1($mes1)
    {
        return $this->mes1 = $mes1;
    }

    public function getMes2()
    {
        return $this->mes2;
    }
    
    public function setMes2($mes2)
    {
        return $this->mes2 = $mes2;
    }

    public function getEmblem_len()
    {
        return $this->emblem_len;
    }
    
    public function setEmblem_len($emblem_len)
    {
        return $this->emblem_len = $emblem_len;
    }

    public function getEmblem_id()
    {
        return $this->emblem_id;
    }
    
    public function setEmblem_id($emblem_id)
    {
        return $this->emblem_id = $emblem_id;
    }

    public function getEmblem_data()
    {
        return $this->emblem_data;
    }
    
    public function setEmblem_data($emblem_data)
    {
        return $this->emblem_data = $emblem_data;
    }
}
