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
 * Classe de entidade para os logs de perfil.
 *
 * @EntityListeners({"ProfileReportListener"})
 * @Entity
 * @Table(name="bracp_profile_report")
 */
class ProfileReport
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="ReportID", type="integer", nullable=false)
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="Profile")
     * @JoinColumn(name="ProfileID", referencedColumnName="ProfileID", nullable=false)
     */
    public $profile;

    /**
     * @ManyToOne(targetEntity="Profile")
     * @JoinColumn(name="InformerID", referencedColumnName="ProfileID", nullable=false)
     */
    public $informer;

    /**
     * @Column(name="ReportDate", type="datetime", nullable=false)
     */
    public $date;

    /**
     * @Column(name="ReportType", type="string", length=1, nullable=false, options={"default":"O"})
     */
    public $type;

    /**
     * @Column(name="ReportText", type="string", length=65355, nullable=false, options={"default":""})
     */
    public $text;

    /**
     * @ManyToOne(targetEntity="Profile")
     * @JoinColumn(name="StaffID", referencedColumnName="ProfileID", nullable=true)
     */
    public $staff;

    /**
     * @Column(name="StaffReply", type="string", length=65355, nullable=true)
     */
    public $staffReply;

    /**
     * @Column(name="StaffReplyDate", type="datetime", nullable=true)
     */
    public $staffReplyDate;

    /**
     * @Column(name="StaffStatus", type="string", length=1, nullable=true)
     */
    public $staffStatus;
}
