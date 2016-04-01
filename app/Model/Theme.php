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
 * @Table(name="bracp_themes")
 */
class Theme
{
    /**
     * @Id
     * @Column(name="ThemeID", type="integer")
     */
    protected $id;

    /**
     * @Column(name="Name", type="string", length=20)
     */
    protected $name;

    /**
     * @Column(name="Version", type="string", length=10)
     */
    protected $version;

    /**
     * @Column(name="Folder", type="string", length=100)
     */
    protected $folder;

    /**
     * @Column(name="ImportTime", type="string", length=19)
     */
    protected $importTime;

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

    public function getVersion()
    {
        return $this->version;
    }
    
    public function setVersion($version)
    {
        return $this->version = $version;
    }

    public function getFolder()
    {
        return $this->folder;
    }
    
    public function setFolder($folder)
    {
        return $this->folder = $folder;
    }

    public function getImportTime()
    {
        return $this->importTime;
    }
    
    public function setImportTime($importTime)
    {
        return $this->importTime = $importTime;
    }
}

