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
 * Classe para escutar os eventos relacionados aos perfis. 
 * Alterações e etc...
 */
class ProfileReportListener
{
    /**
     * @PostPersist
     *
     * Método para notificar o informante que sua denuncia foi recebida.
     *
     * @param ProfileReport $report Dados de validação do perfil. 
     * @param object $args Dados de argumentos enviados pelo EntityManager
     */
    public function postPersist(ProfileReport $report, $args)
    {
        $app = \App::getInstance();
        // Envia o e-mail ao endereço de e-mail do perfil
        $app->getMailer()
            ->send('Sua denuncia foi recebida',
                    [$report->informer->email => $report->informer->name],
                    'mail.profile.report.tpl',
                    [
                        'report'    => $report
                    ]);
    }
}
