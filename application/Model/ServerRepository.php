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
 *
 */
class ServerRepository extends AppRepository
{
    /**
     * Retorna todos os servidores habilitados.
     *
     * @return array
     */
    public function getAllEnabled()
    {
        return $this->findBy([
            'enabled' => true
        ]);
    }

    /**
     * Método para pingar em todos os servidores ativos e retornar os status
     * de ping respectivamente.
     */
    public function pingAll()
    {
        // Encontra todos os servidores que estão ativos.
        $servers = $this->getAllEnabled();

        // Caso não existam servidores configurados.
        if(!count($servers))
            return [];

        // Inicializa a variavel de retorno. 
        $_servers = [];

        // Varre todos os servers configurados e procura pelo ping 
        // dele no banco para não dar problema de ddos no servidor.
        foreach($servers as $server)
        {
            // Inicializa a variavél para retornar sobre o retorno do servidor.
            $_server = (object)[
                'id'    => $server->id,
                'name'  => $server->name,
                'status' => (object)[
                    'login' => false,
                    'char'  => false,
                    'map'   => false,
                    'ping'  => 0
                ]
            ];

            // Obtém do banco de dados o status de conexão com o ultimo ping realizado
            // Ao servidor.
            $status = $this->_em->createQuery('
                SELECT
                    status, server
                FROM 
                    Model\ServerStatus status 
                INNER JOIN 
                    status.server server
                WHERE 
                    server.id = :id AND 
                    status.statusExpire >= :statusExpire
            ')
            ->setParameter('id', $server->id)
            ->setParameter('statusExpire', new \Datetime())
            ->getOneOrNullResult();

            // Não encontrou o status do servidor.
            if(is_null($status))
            {
                // Inicializa os dados de ping.
                $status = new \Model\ServerStatus;
                $status->server = $server;
                $status->statusDate = new \Datetime();
                $status->statusExpire = new \Datetime(date('Y-m-d H:i:s', time() + BRACP_SERVER_PING));

                try
                {
                    // Realiza o ping no servidor de login para saber se o mesmo está online.
                    $login_t0 = microtime(true);
                    $fp = @fsockopen($server->loginIp, $server->loginPort);
                    $login_t1 = microtime(true) - $login_t0;
                    if(($status->loginServer = is_resource($fp)))
                    {
                        fclose($fp);
                        $status->loginPing = $login_t1;
                    }
                }
                catch(\Exception $ex)
                {
                    $login_t1 = 99;
                    $status->loginServer = false;
                    $status->loginPing = $login_t1;
                }

                try
                {
                    // Realiza o ping no servidor de char para saber se o mesmo está online.
                    $char_t0 = microtime(true);
                    $fp = @fsockopen($server->charIp, $server->charPort);
                    $char_t1 = microtime(true) - $char_t0;
                    if(($status->charServer = is_resource($fp)))
                    {
                        fclose($fp);
                        $status->charPing = $char_t1;
                    }
                }
                catch(\Exception $ex)
                {
                    $char_t1 = 99;
                    $status->charServer = false;
                    $status->charPing = $login_t1;
                }

                try
                {
                    // Realiza o ping no servidor de mapas para saber se o mesmo está online.
                    $map_t0 = microtime(true);
                    $fp = @fsockopen($server->mapIp, $server->mapPort);
                    $map_t1 = microtime(true) - $map_t0;
                    if(($status->mapServer = is_resource($fp)))
                    {
                        fclose($fp);
                        $status->mapPing = $map_t1;
                    }
                }
                catch(\Exception $ex)
                {
                    $map_t1 = 99;
                    $status->mapServer = false;
                    $status->mapPing = $map_t1;
                }

                // Calcula o ping médio entre os servidores apartir da hospedagem atual
                $status->averagePing = (($map_t1 + $char_t1 + $login_t1) / 3);

                $this->save($status);
                $this->_em->refresh($status);
            }

            // Adiciona os status ao informativo de retorno.
            $_server->status->ping = $status->averagePing;
            $_server->status->login = $status->loginServer;
            $_server->status->char = $status->charServer;
            $_server->status->map = $status->mapServer;

            // Adiciona ao array de retorno.
            $_servers[] = $_server;
        }

        // Retorna a lista de informações com os dados de servidores.
        return $_servers;
    }
}

