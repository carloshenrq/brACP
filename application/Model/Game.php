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
 * Classe de entidade aos perfis que existem no bracp.
 *
 * @EntityListeners({"GameListener"})
 * @Entity(repositoryClass="Model\GameRepository")
 * @Table(name="bracp_profile_accounts")
 */
class Game
{
    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="ProfileAccID", type="integer", nullable=false)
     */
    public $id;

    /**
     * @ManyToOne(targetEntity="Profile")
     * @JoinColumn(name="ProfileID", referencedColumnName="ProfileID", nullable=false)
     */
    public $profile;

    /**
     * @Column(name="AccountID", type="integer", nullable=false)
     */
    public $account_id;
    
    /**
     * @Column(name="AccountUserID", type="string", length=50, nullable=false)
     */
    public $userid;
    
    /**
     * @Column(name="AccountSex", type="string", length=1, nullable=false)
     */
    public $sex;

    /**
     * @Column(name="AccountVerifyDt", type="datetime", nullable=false)
     */
    public $verifyDt;
}
