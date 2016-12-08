<?php
/**
 *  brACP - brAthena Control Panel for Ragnarok Emulators
 *  Copyright (C) 2016  brAthena, CHLFZ
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
 * @Table(name="bracp_ip_data")
 */
class IpAddress
{
    /**
     * @Id
     * @Column(name="LogID", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="IpAddress", type="string", length=15)
     */
    protected $ipAddress;

    /**
     * @Column(name="UserAgent", type="string", length=300)
     */
    protected $userAgent;

    /**
     * @Column(name="Hostname", type="string", length=200)
     */
    protected $hostname;

    /**
     * @Column(name="City", type="string", length=100)
     */
    protected $city;

    /**
     * @Column(name="Region", type="string", length=100)
     */
    protected $region;

    /**
     * @Column(name="Country", type="string", length=10)
     */
    protected $country;

    /**
     * @Column(name="Location", type="string", length=100)
     */
    protected $location;

    /**
     * @Column(name="Origin", type="string", length=200)
     */
    protected $origin;

    /**
     * @Column(name="DtLog", type="string", length=19)
     */
    protected $dtLog;

    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        return $this->id = $id;
    }

    public function getIpAddress()
    {
        return $this->ipAddress;
    }
    
    public function setIpAddress($ipAddress)
    {
        return $this->ipAddress = $ipAddress;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }
    
    public function setUserAgent($userAgent)
    {
        return $this->userAgent = $userAgent;
    }

    public function getHostname()
    {
        return $this->hostname;
    }
    
    public function setHostname($hostname)
    {
        return $this->hostname = $hostname;
    }

    public function getCity()
    {
        return $this->city;
    }
    
    public function setCity($city)
    {
        return $this->city = $city;
    }

    public function getRegion()
    {
        return $this->region;
    }
    
    public function setRegion($region)
    {
        return $this->region = $region;
    }

    public function getCountry()
    {
        return $this->country;
    }
    
    public function setCountry($country)
    {
        return $this->country = $country;
    }

    public function getLocation()
    {
        return $this->location;
    }
    
    public function setLocation($location)
    {
        return $this->location = $location;
    }

    public function getOrigin()
    {
        return $this->origin;
    }
    
    public function setOrigin($origin)
    {
        return $this->origin = $origin;
    }

    public function getDtLog()
    {
        return $this->dtLog;
    }
    
    public function setDtLog($dtLog)
    {
        return $this->dtLog = $dtLog;
    }

}
