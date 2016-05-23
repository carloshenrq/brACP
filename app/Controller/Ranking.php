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

use \Cache;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Ranking
{
    use \TApplication;

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function chars(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe o display para home.
        self::getApp()->display('rankings.chars');
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function charJson(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Define que irá carregar o cache de personagens a serem exibidos
        //  ou criar o cache.
        $app = self::getApp();
        $chars = Cache::get('BRACP_RANKING_CHARS', function() use ($app) {
            $_cacheData = [];
            $data = $app->getEm()
                        ->createQuery('
                            SELECT
                                chars
                            FROM
                                Model\Char chars
                            ORDER BY
                                chars.base_level DESC,
                                chars.job_level DESC,
                                chars.base_exp DESC,
                                chars.job_exp DESC
                        ')
                        ->setMaxResults(20)
                        ->getResult();

            foreach($data as $i => $char)
            {
                $_cacheData[] = [
                    'pos' => 1 + $i,
                    'name' => $char->getName(),
                    'class' => '@@JOBS('.$char->getClass().')',
                    'baseLevel' => $char->getBase_Level(),
                    'jobLevel' => $char->getJob_Level(),
                    'online' => $char->getOnline(),
                    'status' => '@@STATUS('.$char->getOnline().')',
                ];
            }

            return json_decode(\Language::parse(json_encode($_cacheData)));
        });

        $response->withJson($chars);
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function economy(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Define que irá carregar o cache de personagens a serem exibidos
        //  ou criar o cache.
        $app = self::getApp();
        $chars = Cache::get('BRACP_RANKING_ECONOMY', function() use ($app) {
            return $app->getEm()
                        ->createQuery('
                            SELECT
                                chars
                            FROM
                                Model\Char chars
                            ORDER BY
                                chars.zeny DESC
                        ')
                        ->setMaxResults(100)
                        ->getResult();
        });

        // Exibe o display para home.
        self::getApp()->display('rankings.chars.economy', ['chars' => $chars]);
    }
}

