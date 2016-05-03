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
use \Themes;
use \Cache;
use \Request;

/**
 * Controlador para dados de conta.
 *
 * @static
 */
class Admin
{
    use \TApplication;

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function update(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Obtém os dados de versão a serem atualizados.
        // Cria o cache para evitar que a requisição fique sendo realizada
        //  varias vezes pelo administrador.
        $req = Cache::get('BRACP_GIT_UPDATE', function() {
            return json_decode(Request::create('https://api.github.com/repos/carloshenrq/brACP/')
                                ->get('releases')->getBody()->getContents());
        });

        // Array de atualizações para o painel de controle.
        $updates = [];

        // Varre todos os dados retornados para exibição das versões que podem ser
        // atualizadas.
        foreach($req as $up)
        {
            $files = null;

            foreach($up->assets as $file)
            {
                $files = [
                    'name' => $file->name,
                    'type' => $file->content_type,
                    'size' => $file->size,
                    'link' => $file->browser_download_url,
                ];
                break;
            }

            $tmp = json_decode(json_encode([
                'version' => [
                    'name'          => $up->name,
                    'number'        => $up->tag_name,
                    'prerelease'    => $up->prerelease,
                    'published'     => $up->created_at,
                ],
                'files' => $files,
            ]));

            $updates[] = $tmp;

            unset($tmp, $files);
        }

        // Exibe o display para as versões que podem ser atualizadas.
        self::getApp()->display('admin.update', [
            'updates' => $updates
        ]);
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function cacheFlush(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Limpa o cache de memória para todo o painel de controle.
        Cache::flush();

        // Exibe o display para home.
        self::getApp()->display('home');
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function mods(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe o display para home.
        self::getApp()->display('admin.mods', [
        ]);
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function players(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe o display para home.
        self::getApp()->display('admin.players', [
        ]);
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function donation(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe o display para home.
        self::getApp()->display('admin.donation', [
        ]);
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function backup(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Exibe o display para home.
        self::getApp()->display('admin.backup', [
            'bkp_response' => self::getApp()->createBackup()
        ]);
    }

    /**
     * Método inicial para exibição dos templates na tela.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     */
    public static function theme(ServerRequestInterface $request, ResponseInterface $response, $args)
    {
        // Refaz o cache para os temas.
        Themes::cacheAll();

        // Exibe o display para home.
        self::getApp()->display('home');
    }

}

