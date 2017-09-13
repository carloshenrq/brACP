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
 * @Entity
 * @Table(name="bracp_servers_status")
 */
class ServerStatus
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="StatusID", type="integer", nullable=false)
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="Server")
     * @JoinColumn(name="ServerID", referencedColumnName="ServerID", nullable=false)
     */
    public $server;

    /**
     * @Column(name="LoginServer", type="boolean", nullable=false, options={"default" : false})
     */
    public $loginServer;

    /**
     * @Column(name="LoginPing", type="decimal", precision=5, scale=3, nullable=false, options={"default" : 0})
     */
    public $loginPing;

    /**
     * @Column(name="CharServer", type="boolean", nullable=false, options={"default" : false})
     */
    public $charServer;

    /**
     * @Column(name="CharPing", type="decimal", precision=5, scale=3, nullable=false, options={"default" : 0})
     */
    public $charPing;

    /**
     * @Column(name="MapServer", type="boolean", nullable=false, options={"default" : false})
     */
    public $mapServer;

    /**
     * @Column(name="MapPing", type="decimal", precision=5, scale=3, nullable=false, options={"default" : 0})
     */
    public $mapPing;

    /**
     * @Column(name="AveragePing", type="decimal", precision=5, scale=3, nullable=false, options={"default" : 0})
     */
    public $averagePing;

    /**
     * @Column(name="StatusDate", type="datetime", nullable=false)
     */
    public $statusDate;

    /**
     * @Column(name="StatusExpire", type="datetime", nullable=false)
     */
    public $statusExpire;
}
