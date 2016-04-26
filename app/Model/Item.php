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
 * @Table(name="item_db")
 */
class Item
{
    /**
     * @Id
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="name_english", type="string", length=50)
     */
    protected $name_english;

    /**
     * @Column(name="name_japanese", type="string", length=50)
     */
    protected $name_japanese;

    /**
     * @Column(name="type", type="integer")
     */
    protected $type;

    /**
     * @Column(name="price_buy", type="integer")
     */
    protected $price_buy;

    /**
     * @Column(name="price_sell", type="integer")
     */
    protected $price_sell;

    /**
     * @Column(name="weight", type="integer")
     */
    protected $weight;

    /**
     * @Column(name="atk", type="integer")
     */
    protected $atk;

    /**
     * @Column(name="matk", type="integer")
     */
    protected $matk;

    /**
     * @Column(name="defence", type="integer")
     */
    protected $defence;

    /**
     * @Column(name="range", type="integer")
     */
    protected $range;

    /**
     * @Column(name="slots", type="integer")
     */
    protected $slots;

    /**
     * @Column(name="equip_jobs", type="integer")
     */
    protected $equip_jobs;

    /**
     * @Column(name="equip_upper", type="integer")
     */
    protected $equip_upper;

    /**
     * @Column(name="equip_genders", type="integer")
     */
    protected $equip_genders;

    /**
     * @Column(name="equip_locations", type="integer")
     */
    protected $equip_locations;

    /**
     * @Column(name="weapon_level", type="integer")
     */
    protected $weapon_level;

    /**
     * @Column(name="equip_level_min", type="integer")
     */
    protected $equip_level_min;

    /**
     * @Column(name="equip_level_max", type="integer")
     */
    protected $equip_level_max;

    /**
     * @Column(name="refineable", type="integer")
     */
    protected $refineable;

    /**
     * @Column(name="view", type="integer")
     */
    protected $view;

    /**
     * @Column(name="bindonequip", type="integer")
     */
    protected $bindonequip;

    /**
     * @Column(name="forceserial", type="integer")
     */
    protected $forceserial;

    /**
     * @Column(name="buyingstore", type="integer")
     */
    protected $buyingstore;

    /**
     * @Column(name="delay", type="integer")
     */
    protected $delay;

    /**
     * @Column(name="trade_flag", type="integer")
     */
    protected $trade_flag;

    /**
     * @Column(name="trade_group", type="integer")
     */
    protected $trade_group;

    /**
     * @Column(name="nouse_flag", type="integer")
     */
    protected $nouse_flag;

    /**
     * @Column(name="nouse_group", type="integer")
     */
    protected $nouse_group;

    /**
     * @Column(name="stack_amount", type="integer")
     */
    protected $stack_amount;

    /**
     * @Column(name="stack_flag", type="integer")
     */
    protected $stack_flag;

    /**
     * @Column(name="sprite", type="integer")
     */
    protected $sprite;

    /**
     * @Column(name="script", type="string")
     */
    protected $script;

    /**
     * @Column(name="equip_script", type="string")
     */
    protected $equip_script;

    /**
     * @Column(name="unequip_script", type="string")
     */
    protected $unequip_script;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getName_english()
    {
        return $this->name_english;
    }
    
    public function setName_english($name_english)
    {
        return $this->name_english = $name_english;
    }

    public function getName_japanese()
    {
        return $this->name_japanese;
    }
    
    public function setName_japanese($name_japanese)
    {
        return $this->name_japanese = $name_japanese;
    }

    public function getType()
    {
        return $this->type;
    }
    
    public function setType($type)
    {
        return $this->type = $type;
    }

    public function getPrice_buy()
    {
        return $this->price_buy;
    }
    
    public function setPrice_buy($price_buy)
    {
        return $this->price_buy = $price_buy;
    }

    public function getPrice_sell()
    {
        return $this->price_sell;
    }
    
    public function setPrice_sell($price_sell)
    {
        return $this->price_sell = $price_sell;
    }

    public function getWeight()
    {
        return $this->weight;
    }
    
    public function setWeight($weight)
    {
        return $this->weight = $weight;
    }

    public function getAtk()
    {
        return $this->atk;
    }
    
    public function setAtk($atk)
    {
        return $this->atk = $atk;
    }

    public function getMatk()
    {
        return $this->matk;
    }
    
    public function setMatk($matk)
    {
        return $this->matk = $matk;
    }

    public function getDefence()
    {
        return $this->defence;
    }
    
    public function setDefence($defence)
    {
        return $this->defence = $defence;
    }

    public function getRange()
    {
        return $this->range;
    }
    
    public function setRange($range)
    {
        return $this->range = $range;
    }

    public function getSlots()
    {
        return $this->slots;
    }
    
    public function setSlots($slots)
    {
        return $this->slots = $slots;
    }

    public function getEquip_jobs()
    {
        return $this->equip_jobs;
    }
    
    public function setEquip_jobs($equip_jobs)
    {
        return $this->equip_jobs = $equip_jobs;
    }

    public function getEquip_upper()
    {
        return $this->equip_upper;
    }
    
    public function setEquip_upper($equip_upper)
    {
        return $this->equip_upper = $equip_upper;
    }

    public function getEquip_genders()
    {
        return $this->equip_genders;
    }
    
    public function setEquip_genders($equip_genders)
    {
        return $this->equip_genders = $equip_genders;
    }

    public function getEquip_locations()
    {
        return $this->equip_locations;
    }
    
    public function setEquip_locations($equip_locations)
    {
        return $this->equip_locations = $equip_locations;
    }

    public function getWeapon_level()
    {
        return $this->weapon_level;
    }
    
    public function setWeapon_level($weapon_level)
    {
        return $this->weapon_level = $weapon_level;
    }

    public function getEquip_level_min()
    {
        return $this->equip_level_min;
    }
    
    public function setEquip_level_min($equip_level_min)
    {
        return $this->equip_level_min = $equip_level_min;
    }

    public function getEquip_level_max()
    {
        return $this->equip_level_max;
    }
    
    public function setEquip_level_max($equip_level_max)
    {
        return $this->equip_level_max = $equip_level_max;
    }

    public function getRefineable()
    {
        return $this->refineable;
    }
    
    public function setRefineable($refineable)
    {
        return $this->refineable = $refineable;
    }

    public function getView()
    {
        return $this->view;
    }
    
    public function setView($view)
    {
        return $this->view = $view;
    }

    public function getBindonequip()
    {
        return $this->bindonequip;
    }
    
    public function setBindonequip($bindonequip)
    {
        return $this->bindonequip = $bindonequip;
    }

    public function getForceserial()
    {
        return $this->forceserial;
    }
    
    public function setForceserial($forceserial)
    {
        return $this->forceserial = $forceserial;
    }

    public function getBuyingstore()
    {
        return $this->buyingstore;
    }
    
    public function setBuyingstore($buyingstore)
    {
        return $this->buyingstore = $buyingstore;
    }

    public function getDelay()
    {
        return $this->delay;
    }
    
    public function setDelay($delay)
    {
        return $this->delay = $delay;
    }

    public function getTrade_flag()
    {
        return $this->trade_flag;
    }
    
    public function setTrade_flag($trade_flag)
    {
        return $this->trade_flag = $trade_flag;
    }

    public function getTrade_group()
    {
        return $this->trade_group;
    }
    
    public function setTrade_group($trade_group)
    {
        return $this->trade_group = $trade_group;
    }

    public function getNouse_flag()
    {
        return $this->nouse_flag;
    }
    
    public function setNouse_flag($nouse_flag)
    {
        return $this->nouse_flag = $nouse_flag;
    }

    public function getNouse_group()
    {
        return $this->nouse_group;
    }
    
    public function setNouse_group($nouse_group)
    {
        return $this->nouse_group = $nouse_group;
    }

    public function getStack_amount()
    {
        return $this->stack_amount;
    }
    
    public function setStack_amount($stack_amount)
    {
        return $this->stack_amount = $stack_amount;
    }

    public function getStack_flag()
    {
        return $this->stack_flag;
    }
    
    public function setStack_flag($stack_flag)
    {
        return $this->stack_flag = $stack_flag;
    }

    public function getSprite()
    {
        return $this->sprite;
    }
    
    public function setSprite($sprite)
    {
        return $this->sprite = $sprite;
    }

    public function getScript()
    {
        return $this->script;
    }
    
    public function setScript($script)
    {
        return $this->script = $script;
    }

    public function getEquip_script()
    {
        return $this->equip_script;
    }
    
    public function setEquip_script($equip_script)
    {
        return $this->equip_script = $equip_script;
    }

    public function getUnequip_script()
    {
        return $this->unequip_script;
    }
    
    public function setUnequip_script($unequip_script)
    {
        return $this->unequip_script = $unequip_script;
    }
}
