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
 * @Entity(repositoryClass="Model\AnnounceRepository")
 * @Table(name="bracp_announces")
 */
class Announce
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="AnnounceID", type="integer", nullable=false)
     */
    public $id;

    /**
     * @Column(name="AnnounceTitle", type="string", length=60, nullable=true, options={"default":null})
     */
    public $title;

    /**
     * @Column(name="AnnounceContent", type="string", length=65535, nullable=true, options={"default":null})
     */
    public $content;

    /**
     * @Column(name="AnnounceCreateDt", type="datetime", nullable=false, options={"default" : false})
     */
    public $createDt;

    /**
     * @Column(name="AnnounceShowDt", type="datetime", nullable=false)
     */
    public $showDt;
     
    /**
     * @Column(name="AnnounceEndDt", type="datetime", nullable=true, options={"default" : null})
     */
    public $endDt;

    /**
     * @Column(name="AnnounceType", type="string", length=1, nullable=false, options={"default":"I"})
     */
    public $type;
    
    /**
     * @Column(name="AnnounceShowType", type="string", length=1, nullable=false, options={"default":"N"})
     */
    public $showType;
}
