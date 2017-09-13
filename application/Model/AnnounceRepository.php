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
 * Classe de repositório de dados para profiles.
 */
class AnnounceRepository extends AppRepository
{
    /**
     * Obtém os anuncios globais ativos para exibição dos usuários.
     *
     * @return array
     */
    public function getAllActiveGlobal()
    {
        // Obtém todos os anuncios globais que devem ser exibidos em tela.
        return $this->_em->createQuery('
                            SELECT
                                announce
                            FROM
                                Model\Announce announce
                            WHERE
                                announce.showDt <= :CURDATE AND
                                (announce.endDt >= :CURDATE OR announce.endDt IS NULL) AND
                                announce.showType = :showType
                            ORDER BY
                                announce.endDt ASC
                        ')->setParameter('CURDATE', new \DateTime())
                        ->setParameter('showType', 'N')
                        ->getResult();
    }
}
