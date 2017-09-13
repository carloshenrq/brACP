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
 * Classe para entity de anuncios e avisos no servidor.
 *
 * @Entity
 * @Table(name="bracp_announces_profiles")
 */
class AnnounceProfile
{
    /**
     * @ManyToOne(targetEntity="Announce")
     * @JoinColumn(name="AnnounceID", referencedColumnName="AnnounceID", nullable=false)
     */
    public $announce;
     
    /**
     * @ManyToOne(targetEntity="Profile")
     * @JoinColumn(name="ProfileID", referencedColumnName="ProfileID", nullable=false)
     */
    public $profile;
              
    /**
     * @Column(name="ResponseType", type="string", length=1, nullable=false, options={"default":"O"})
     */
    public $type;
    
    /**
     * @Column(name="ResponseDt", type="datetime", nullable=false, options={"default" : false})
     */
    public $dt;
}
