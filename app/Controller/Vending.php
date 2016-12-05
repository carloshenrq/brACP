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
use \Format;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Vending extends Caller
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
     * Obtém os dados iniciais de mapa.
     *
     * @param array $get
     * @param array $post
     */
    private function index_GET($get, $post, $response)
    {
        $this->getApp()->display('vending.list');
        return $response;
    }

    /**
     * Obtém os dados iniciais de mapa.
     *
     * @param array $get
     * @param array $post
     */
    private function list_GET($get, $post, $response)
    {
        $merchants  = $this->getAllMerchants();
        $maps       = [];

        // Varre os dados informados para retorno.
        foreach($merchants as $merchant)
        {
            if(!in_array($merchant->map, array_keys($maps)))
                $maps[$merchant->map][] = $merchant;
        }

        // Retorna um vetor com todos os mercadores e mapas (para filtro).
        return $response->withJson([
            'merchants' => $merchants,
            'maps'      => $maps,
            'map_keys'  => array_keys($maps),
        ]);
    }

    /**
     * Obtém os mercadores do banco de dados.
     *
     * @return array
     */
    private function getAllMerchants()
    {
        // Retorna embranco caso as configurações não estejam
        // habilitados.
        if(!BRACP_ALLOW_VENDING)
            return [];
        
        // Dados de venda para retornar ao browse
        $vending_data = Cache::get('BRACP_CACHE_MERCHANT_SVR_' . $this->getApp()->getSession()->BRACP_SVR_SELECTED, function() {

            $app = \brACPApp::getInstance();
            $item = new Item($app);

            // Obtém vetor de todos os mercadores
            $merchants = $app->getSvrEm()
                            ->createQuery('
                                SELECT
                                    item, cart, char, guild, merc, merc_char
                                FROM
                                    Model\MerchantItem item
                                INNER JOIN
                                    item.cart cart
                                INNER JOIN
                                    cart.char char
                                LEFT JOIN
                                    char.guild guild
                                INNER JOIN
                                    item.merchant merc
                                INNER JOIN
                                    merc.char merc_char
                                ORDER BY
                                    merc_char.last_map ASC,
                                    merc_char.last_x ASC,
                                    merc_char.last_y ASC,
                                    merc_char.char_id ASC,
                                    item.itemkey ASC
                            ')
                            ->getResult();

            $i = 0;
            $_tmpChar = $merchants_data = [];

            // Varre os dados de mercadores.
            foreach($merchants as $merchant)
            {
                $char_id = $merchant->getMerchant()->getChar_id();   

                // Verifica se o personagem no vetor, caso não esteja
                // Cria entrada para exibição futura.
                if(!in_array($char_id, $_tmpChar))
                {
                    $_tmpChar[$i]           = $char_id;
                    $merchants_data[$i]     = (object)[
                        'title'     => $merchant->getMerchant()->getTitle(),
                        'name'      => $merchant->getMerchant()->getChar()->getName(),
                        'map'       => $merchant->getMerchant()->getChar()->getLast_map(),
                        'x'         => $merchant->getMerchant()->getChar()->getLast_x(),
                        'y'         => $merchant->getMerchant()->getChar()->getLast_y(),
                        'items'     => []
                    ];
                    $i++;
                }

                // Define os dados de index para inserir.
                $index = array_search($char_id, $_tmpChar);

                // Objeto temporario.
                $_tmp =  (object)[
                    'item'      => $item->getItem($merchant->getCart()->getNameid()),
                    'amount'    => Format::zeny($merchant->getAmount()),
                    'price'     => Format::zeny($merchant->getPrice()),
                    'refine'    => $merchant->getCart()->getRefine(),
                    'unique_id' => $merchant->getCart()->getUnique_id(),
                    'cards'     => []
                ];

                // Adiciona as cartas ao objeto informado.
                if(!empty($merchant->getCart()->getCard0())) $_tmp->cards[] = $item->getItem($merchant->getCart()->getCard0());
                if(!empty($merchant->getCart()->getCard1())) $_tmp->cards[] = $item->getItem($merchant->getCart()->getCard1());
                if(!empty($merchant->getCart()->getCard2())) $_tmp->cards[] = $item->getItem($merchant->getCart()->getCard2());
                if(!empty($merchant->getCart()->getCard3())) $_tmp->cards[] = $item->getItem($merchant->getCart()->getCard3());

                // Atualiza indices de mercadores.
                $merchants_data[$index]->items[]    = $_tmp;
            }

            return $merchants_data;
        });

        return $vending_data;
    }

}

