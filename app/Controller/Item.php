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

namespace Controller;

use \ServerPing;
use \Cache;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Item extends Caller
{
    /**
     * Construtor para o 
     */
    public function __construct(\brACPApp $app)
    {
        // Controller sem restrições de chamada
        parent::__construct($app, []);
    }

    /**
     * Obtém informações do item.
     *
     * @param array $get
     * @param array $post
     * @param object $response
     *
     * @return object
     */
    private function info_GET($get, $post, $response)
    {
        $nameid = $get['id'];

        // Obtém os dados do item em cache.
        $item_data = $this->getItem($nameid);

        // Retorna os dados em formato json.
        return $response->withJson($item_data);
    }

    /**
     * Obtém o Item
     *
     * @param string $nameid
     *
     * @return object Alteração de dados para item.
     */
    public function getItem($nameid)
    {
        return Cache::get('BRACP_ITEM_' . $nameid, function() use ($nameid) {
            // Obtém os caminhos para os arquivos de imagem do item.
            $icon = BRACP_DIR_INSTALL_URL . 'asset/icon/?id=' . $nameid;
            $images = BRACP_DIR_INSTALL_URL . 'asset/images/?id=' . $nameid;

            // Obtém o objeto do item.
            $item = \brACPApp::getInstance()->getDbEm()
                                        ->getRepository('Model\Item')
                                        ->findOneBy(['id' => $nameid]);

            // Verifica se o item existe, se não existe, então
            // retorna null.
            if(is_null($item))
                return null;

            // Retorna o objeto do item em forma de json.
            return [
                'id'        => $nameid,
                'icon'      => $icon,
                'image'     => $images,
                'name'      => $item->getName_japanese(),
                'weight'    => $item->getWeight(),
                'type'      => $item->getType(),
                'price'     => [
                    'buy'   => $item->getPrice_buy(),
                    'sell'  => $item->getPrice_sell(),
                ],
                'battle'    => [
                    'atk'           => $item->getAtk(),
                    'matk'          => $item->getMatk(),
                    'defence'       => $item->getDefence(),
                    'range'         => $item->getRange(),
                    'jobs'          => $item->getEquip_jobs(),
                    'upper'         => $item->getEquip_upper(),
                    'genders'       => $item->getEquip_genders(),
                    'locations'     => $item->getEquip_locations(),
                    'weapon_level'  => $item->getWeapon_level(),
                ],
                'refineable'    => $item->getRefineable(),
                'view'          => $item->getView(),
                'slots'         => $item->getSlots(),
            ];
        });
    }
}

