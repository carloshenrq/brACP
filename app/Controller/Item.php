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

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \ServerPing;
use \Cache;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Item
{
    use \TApplication;

    private static $itemsCache = [];
    private static $loaded = false;

    /**
     * Método para incializar os itens em cache.
     */
    public static function cacheLoad()
    {
        if(self::$loaded)
            return;

        self::$itemsCache = Cache::get('BRACP_ITEMDB', function() {
            return [];
        }, false, -1);

        self::$loaded = true;
    }

    /**
     * Obtém dados do item por Id
     *
     * @param integer $id
     *
     * @return object
     */
    public static function get($id)
    {
        return self::getFromCacheId($id);
    }

    /**
     * Obtém o item do cache.
     *
     * @param integer $id
     */
    public static function getFromCacheId($id)
    {
        self::cacheLoad();

        $items = self::$itemsCache;
        self::$itemsCache = null;

        if(!isset($items[$id]))
        {
            $item = self::getFromId($id);
            $item->cache_expire = time() + BRACP_CACHE_EXPIRE;
            $items[$id] = $item;

            Cache::delete('BRACP_ITEMDB');

           self::$itemsCache = Cache::get('BRACP_ITEMDB', function() use ($items) {
                return $items;
            }, false, -1);
        }

        self::$itemsCache = $items;
        $item = self::$itemsCache[$id];

        if($item->cache_expire <= time())
        {
            unset(self::$itemsCache[$id]);
            return self::getFromCacheId($id);
        }

        return self::$itemsCache[$id];
    }

    /**
     * Obtém os dados do item por id, direto na conexão com o banco
     *
     * @param integer $id
     *
     * @return object
     */
    private static function getFromId($id)
    {
        self::cacheLoad();

        $item = self::getDbEm()
                    ->createQuery('
                        SELECT
                            item
                        FROM
                            Model\Item item
                        WHERE
                            item.id = :id
                    ')
                    ->setParameter('id', $id)
                    ->getOneOrNullResult();

        if(is_null($item))
        {
            $item = new \Model\Item;
            $item->setId($id);
            $item->setName_japanese('Unknow');
            $item->setType(0);
            $item->setSlots(0);
        }

        $obj = (object)[
            // Informações do item (Direto do banco)
            'id'            => $item->getId(),
            'name'          => utf8_encode($item->getName_japanese()),
            'type'          => $item->getType(),
            'slots'         => $item->getSlots(),

            // Dados para exibição do item
            'icon'          => BRACP_DIR_INSTALL_URL . 'data/items/icons/' . $item->getId() . '.png',
            'image'         => BRACP_DIR_INSTALL_URL . 'data/items/images/' . $item->getId() . '.png',

            // Informações de cache.
            'cache'         => time(),
            'cache_expire'  => time()
        ];

        return $obj;
    }
}

