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
 * @EntityListeners({"ServerListener"})
 * @Entity(repositoryClass="Model\ServerRepository")
 * @Table(name="bracp_servers")
 */
class Server
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="ServerID", type="integer", nullable=false)
     */
    public $id;

    /**
     * @Column(name="Server", type="string", length=20, nullable=false)
     */
    public $name;

    /**
     * @Column(name="ServerEnabled", type="boolean", nullable=false, options={"default" : true})
     */
    public $enabled;

    /**
     * @Column(name="SQLHost", type="string", length=256, nullable=false, options={"default" : "127.0.0.1"})
     */
    public $sqlHost;

    /**
     * @Column(name="SQLPort", type="integer", nullable=false, options={"default" : 3306})
     */
    public $sqlPort;

    /**
     * @Column(name="SQLUser", type="string", length=30, nullable=false, options={"default" : "ragnarok"})
     */
    public $sqlUser;

    /**
     * @Column(name="SQLPass", type="string", length=256, nullable=false, options={"default" : "ragnarok"})
     */
    public $sqlPass;

    /**
     * @Column(name="SQLDatabase", type="string", length=30, nullable=false, options={"default" : "ragnarok"})
     */
    public $sqlData;

    /**
     * @Column(name="SQLType", type="string", length=1, nullable=false, options={"default" : "S"})
     */
    public $sqlType;

    /**
     * @Column(name="LoginServer", type="boolean", nullable=false, options={"default" : true})
     */
    public $loginServer;

    /**
     * @Column(name="LoginIP", type="string", length=50, nullable=false, options={"default" : "127.0.0.1"})
     */
    public $loginIp;

    /**
     * @Column(name="LoginPort", type="integer", nullable=false, options={"default" : 6900})
     */
    public $loginPort;

    /**
     * @Column(name="CharIP", type="string", length=50, nullable=false, options={"default" : "127.0.0.1"})
     */
    public $charIp;

    /**
     * @Column(name="CharPort", type="integer", nullable=false, options={"default" : 6121})
     */
    public $charPort;

    /**
     * @Column(name="MapIP", type="string", length=50, nullable=false, options={"default" : "127.0.0.1"})
     */
    public $mapIp;

    /**
     * @Column(name="MapPort", type="integer", nullable=false, options={"default" : 5121})
     */
    public $mapPort;
}
