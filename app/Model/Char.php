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
 * @Table(name="`char`")
 */
class Char
{
    /**
     * @Id
     * @Column(name="char_id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $char_id;

    /**
     * @Column(name="account_id", type="integer")
     */
    protected $account_id;

    /**
     * @Column(name="char_num", type="integer")
     */
    protected $char_num;

    /**
     * @Column(name="name", type="string", length=30)
     */
    protected $name;

    /**
     * @Column(name="class", type="integer")
     */
    protected $class;

    /**
     * @Column(name="base_level", type="integer")
     */
    protected $base_level;

    /**
     * @Column(name="job_level", type="integer")
     */
    protected $job_level;

    /**
     * @Column(name="base_exp", type="integer")
     */
    protected $base_exp;

    /**
     * @Column(name="job_exp", type="integer")
     */
    protected $job_exp;

    /**
     * @Column(name="zeny", type="integer")
     */
    protected $zeny;

    /**
     * @Column(name="str", type="integer")
     */
    protected $str;

    /**
     * @Column(name="agi", type="integer")
     */
    protected $agi;

    /**
     * @Column(name="vit", type="integer")
     */
    protected $vit;

    /**
     * @Column(name="int", type="integer")
     */
    protected $int;

    /**
     * @Column(name="dex", type="integer")
     */
    protected $dex;

    /**
     * @Column(name="luk", type="integer")
     */
    protected $luk;

    /**
     * @Column(name="max_hp", type="integer")
     */
    protected $max_hp;

    /**
     * @Column(name="hp", type="integer")
     */
    protected $hp;

    /**
     * @Column(name="max_sp", type="integer")
     */
    protected $max_sp;

    /**
     * @Column(name="sp", type="integer")
     */
    protected $sp;

    /**
     * @Column(name="skill_point", type="integer")
     */
    protected $skill_point;

    /**
     * @Column(name="option", type="integer")
     */
    protected $option;

    /**
     * @Column(name="karma", type="integer")
     */
    protected $karma;

    /**
     * @Column(name="manner", type="integer")
     */
    protected $manner;

    /**
     * @Column(name="party_id", type="integer")
     */
    protected $party_id;

    /**
     * @OneToOne(targetEntity="Guild")
     * @JoinColumn(name="guild_id", referencedColumnName="guild_id")
     */
    protected $guild;

    /**
     * @Column(name="pet_id", type="integer")
     */
    protected $pet_id;

    /**
     * @Column(name="homun_id", type="integer")
     */
    protected $homun_id;

    /**
     * @Column(name="elemental_id", type="integer")
     */
    protected $elemental_id;

    /**
     * @Column(name="hair", type="integer")
     */
    protected $hair;

    /**
     * @Column(name="hair_color", type="integer")
     */
    protected $hair_color;

    /**
     * @Column(name="clothes_color", type="integer")
     */
    protected $clothes_color;

    /**
     * @Column(name="weapon", type="integer")
     */
    protected $weapon;

    /**
     * @Column(name="shield", type="integer")
     */
    protected $shield;

    /**
     * @Column(name="head_top", type="integer")
     */
    protected $head_top;

    /**
     * @Column(name="head_mid", type="integer")
     */
    protected $head_mid;

    /**
     * @Column(name="head_bottom", type="integer")
     */
    protected $head_bottom;

    /**
     * @Column(name="robe", type="integer")
     */
    protected $robe;

    /**
     * @Column(name="last_map", type="string", length=11)
     */
    protected $last_map;

    /**
     * @Column(name="last_x", type="integer")
     */
    protected $last_x;

    /**
     * @Column(name="last_y", type="integer")
     */
    protected $last_y;

    /**
     * @Column(name="save_map", type="string", length=11)
     */
    protected $save_map;

    /**
     * @Column(name="save_x", type="integer")
     */
    protected $save_x;

    /**
     * @Column(name="save_y", type="integer")
     */
    protected $save_y;

    /**
     * @Column(name="partner_id", type="integer")
     */
    protected $partner_id;

    /**
     * @Column(name="online", type="integer")
     */
    protected $online;

    /**
     * @Column(name="father", type="integer")
     */
    protected $father;

    /**
     * @Column(name="mother", type="integer")
     */
    protected $mother;

    /**
     * @Column(name="child", type="integer")
     */
    protected $child;

    /**
     * @Column(name="fame", type="integer")
     */
    protected $fame;

    /**
     * @Column(name="rename", type="integer")
     */
    protected $rename;

    /**
     * @Column(name="delete_date", type="integer")
     */
    protected $delete_date;

    /**
     * @Column(name="moves", type="integer")
     */
    protected $moves;

    /**
     * @Column(name="unban_time", type="integer")
     */
    protected $unban_time;

    /**
     * @Column(name="font", type="integer")
     */
    protected $font;

    /**
     * @Column(name="uniqueitem_counter", type="integer")
     */
    protected $uniqueitem_counter;

    /**
     * @Column(name="sex", type="integer")
     */
    protected $sex;

    /**
     * @Column(name="hotkey_rowshift", type="integer")
     */
    protected $hotkey_rowshift;

    /**
     * @OneToMany(targetEntity="Inventory", mappedBy="char")
     */
    protected $inventory;

    public function getChar_id()
    {
        return $this->char_id;
    }
    
    public function setChar_id($char_id)
    {
        return $this->char_id = $char_id;
    }

    public function getAccount_id()
    {
        return $this->account_id;
    }
    
    public function setAccount_id($account_id)
    {
        return $this->account_id = $account_id;
    }

    public function getChar_num()
    {
        return $this->char_num;
    }
    
    public function setChar_num($char_num)
    {
        return $this->char_num = $char_num;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        return $this->name = $name;
    }

    public function getClass()
    {
        return $this->class;
    }
    
    public function setClass($class)
    {
        return $this->class = $class;
    }

    public function getBase_level()
    {
        return $this->base_level;
    }
    
    public function setBase_level($base_level)
    {
        return $this->base_level = $base_level;
    }

    public function getJob_level()
    {
        return $this->job_level;
    }
    
    public function setJob_level($job_level)
    {
        return $this->job_level = $job_level;
    }

    public function getBase_exp()
    {
        return $this->base_exp;
    }
    
    public function setBase_exp($base_exp)
    {
        return $this->base_exp = $base_exp;
    }

    public function getJob_exp()
    {
        return $this->job_exp;
    }
    
    public function setJob_exp($job_exp)
    {
        return $this->job_exp = $job_exp;
    }

    public function getZeny()
    {
        return $this->zeny;
    }
    
    public function setZeny($zeny)
    {
        return $this->zeny = $zeny;
    }

    public function getStr()
    {
        return $this->str;
    }
    
    public function setStr($str)
    {
        return $this->str = $str;
    }

    public function getAgi()
    {
        return $this->agi;
    }
    
    public function setAgi($agi)
    {
        return $this->agi = $agi;
    }

    public function getVit()
    {
        return $this->vit;
    }
    
    public function setVit($vit)
    {
        return $this->vit = $vit;
    }

    public function getInt()
    {
        return $this->int;
    }
    
    public function setInt($int)
    {
        return $this->int = $int;
    }

    public function getDex()
    {
        return $this->dex;
    }
    
    public function setDex($dex)
    {
        return $this->dex = $dex;
    }

    public function getLuk()
    {
        return $this->luk;
    }
    
    public function setLuk($luk)
    {
        return $this->luk = $luk;
    }

    public function getMax_hp()
    {
        return $this->max_hp;
    }
    
    public function setMax_hp($max_hp)
    {
        return $this->max_hp = $max_hp;
    }

    public function getHp()
    {
        return $this->hp;
    }
    
    public function setHp($hp)
    {
        return $this->hp = $hp;
    }

    public function getMax_sp()
    {
        return $this->max_sp;
    }
    
    public function setMax_sp($max_sp)
    {
        return $this->max_sp = $max_sp;
    }

    public function getSp()
    {
        return $this->sp;
    }
    
    public function setSp($sp)
    {
        return $this->sp = $sp;
    }

    public function getStatus_point()
    {
        return $this->status_point;
    }
    
    public function setStatus_point($status_point)
    {
        return $this->status_point = $status_point;
    }

    public function getSkill_point()
    {
        return $this->skill_point;
    }
    
    public function setSkill_point($skill_point)
    {
        return $this->skill_point = $skill_point;
    }

    public function getOption()
    {
        return $this->option;
    }
    
    public function setOption($option)
    {
        return $this->option = $option;
    }

    public function getKarma()
    {
        return $this->karma;
    }
    
    public function setKarma($karma)
    {
        return $this->karma = $karma;
    }

    public function getManner()
    {
        return $this->manner;
    }
    
    public function setManner($manner)
    {
        return $this->manner = $manner;
    }

    public function getParty_id()
    {
        return $this->party_id;
    }
    
    public function setParty_id($party_id)
    {
        return $this->party_id = $party_id;
    }

    public function getGuild()
    {
        return $this->guild;
    }
    
    public function setGuild($guild)
    {
        return $this->guild = $guild;
    }

    public function getPet_id()
    {
        return $this->pet_id;
    }
    
    public function setPet_id($pet_id)
    {
        return $this->pet_id = $pet_id;
    }

    public function getHomun_id()
    {
        return $this->homun_id;
    }
    
    public function setHomun_id($homun_id)
    {
        return $this->homun_id = $homun_id;
    }

    public function getElemental_id()
    {
        return $this->elemental_id;
    }
    
    public function setElemental_id($elemental_id)
    {
        return $this->elemental_id = $elemental_id;
    }

    public function getHair()
    {
        return $this->hair;
    }
    
    public function setHair($hair)
    {
        return $this->hair = $hair;
    }

    public function getHair_color()
    {
        return $this->hair_color;
    }
    
    public function setHair_color($hair_color)
    {
        return $this->hair_color = $hair_color;
    }

    public function getClothes_color()
    {
        return $this->clothes_color;
    }
    
    public function setClothes_color($clothes_color)
    {
        return $this->clothes_color = $clothes_color;
    }

    public function getWeapon()
    {
        return $this->weapon;
    }
    
    public function setWeapon($weapon)
    {
        return $this->weapon = $weapon;
    }

    public function getShield()
    {
        return $this->shield;
    }
    
    public function setShield($shield)
    {
        return $this->shield = $shield;
    }

    public function getHead_top()
    {
        return $this->head_top;
    }
    
    public function setHead_top($head_top)
    {
        return $this->head_top = $head_top;
    }

    public function getHead_mid()
    {
        return $this->head_mid;
    }
    
    public function setHead_mid($head_mid)
    {
        return $this->head_mid = $head_mid;
    }

    public function getHead_bottom()
    {
        return $this->head_bottom;
    }
    
    public function setHead_bottom($head_bottom)
    {
        return $this->head_bottom = $head_bottom;
    }

    public function getRobe()
    {
        return $this->robe;
    }
    
    public function setRobe($robe)
    {
        return $this->robe = $robe;
    }

    public function getLast_map()
    {
        return $this->last_map;
    }
    
    public function setLast_map($last_map)
    {
        return $this->last_map = $last_map;
    }

    public function getLast_x()
    {
        return $this->last_x;
    }
    
    public function setLast_x($last_x)
    {
        return $this->last_x = $last_x;
    }

    public function getLast_y()
    {
        return $this->last_y;
    }
    
    public function setLast_y($last_y)
    {
        return $this->last_y = $last_y;
    }

    public function getSave_map()
    {
        return $this->save_map;
    }
    
    public function setSave_map($save_map)
    {
        return $this->save_map = $save_map;
    }

    public function getSave_x()
    {
        return $this->save_x;
    }
    
    public function setSave_x($save_x)
    {
        return $this->save_x = $save_x;
    }

    public function getSave_y()
    {
        return $this->save_y;
    }
    
    public function setSave_y($save_y)
    {
        return $this->save_y = $save_y;
    }

    public function getPartner_id()
    {
        return $this->partner_id;
    }
    
    public function setPartner_id($partner_id)
    {
        return $this->partner_id = $partner_id;
    }

    public function getOnline()
    {
        return $this->online;
    }
    
    public function setOnline($online)
    {
        return $this->online = $online;
    }

    public function getFather()
    {
        return $this->father;
    }
    
    public function setFather($father)
    {
        return $this->father = $father;
    }

    public function getMother()
    {
        return $this->mother;
    }
    
    public function setMother($mother)
    {
        return $this->mother = $mother;
    }

    public function getChild()
    {
        return $this->child;
    }
    
    public function setChild($child)
    {
        return $this->child = $child;
    }

    public function getFame()
    {
        return $this->fame;
    }
    
    public function setFame($fame)
    {
        return $this->fame = $fame;
    }

    public function getRename()
    {
        return $this->rename;
    }
    
    public function setRename($rename)
    {
        return $this->rename = $rename;
    }

    public function getDelete_date()
    {
        return $this->delete_date;
    }
    
    public function setDelete_date($delete_date)
    {
        return $this->delete_date = $delete_date;
    }

    public function getMoves()
    {
        return $this->moves;
    }
    
    public function setMoves($moves)
    {
        return $this->moves = $moves;
    }

    public function getUnban_time()
    {
        return $this->unban_time;
    }
    
    public function setUnban_time($unban_time)
    {
        return $this->unban_time = $unban_time;
    }

    public function getFont()
    {
        return $this->font;
    }
    
    public function setFont($font)
    {
        return $this->font = $font;
    }

    public function getUniqueitem_counter()
    {
        return $this->uniqueitem_counter;
    }
    
    public function setUniqueitem_counter($uniqueitem_counter)
    {
        return $this->uniqueitem_counter = $uniqueitem_counter;
    }

    public function getSex()
    {
        return $this->sex;
    }
    
    public function setSex($sex)
    {
        return $this->sex = $sex;
    }

    public function getHotkey_rowshift()
    {
        return $this->hotkey_rowshift;
    }
    
    public function setHotkey_rowshift($hotkey_rowshift)
    {
        return $this->hotkey_rowshift = $hotkey_rowshift;
    }

    public function getInventory()
    {
        return $this->inventory;
    }
    
    public function setInventory($inventory)
    {
        return $this->inventory = $inventory;
    }
}

