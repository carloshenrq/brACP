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

/**
 * Classe para tratamento de informações relacionadas ao firewall.
 */
class Firewall extends brACPMiddleware
{
    /**
     * String que armazena o useragent.
     *
     * @return string
     */
    private $userAgent = null;

    /**
     * String que armazena o endereço ip do jogador.
     *
     * @return string
     */
    private $ipAddress = null;

    /**
     * Conexão com o sqlite local. Não será usado doctrine, aqui precisamos de speed.
     *
     * @var \PDO
     */
    private $sqlite = null;

    /**
     * Inicializa o middleware de firewall
     */
    protected function init()
    {
        // Define o objeto de firewall da aplicação.
        $this->getApp()->setFirewall($this);

        // Caso a extensão esteja carregada, então inicializa a conexão com a base local
        // Para realizar os testes necessários.
        if(extension_loaded('pdo_sqlite'))
        {
            $needImport = !file_exists('firewall.db'); // Verifica se é necessário importar as tabelas.

            // Inicializa a conexão com o banco de dados local do sqlite.
            $this->sqlite = new \PDO('sqlite:firewall.db');

            // Verifica se é necessarío importar os dados.
            if($needImport)
            {
                // Inicializa as transações.
                $this->sqlite->beginTransaction();

                // Dados de firewall
                $sqlite_db = file_get_contents(__DIR__ . '/../sql-files/bracp-firewall.sql');
                $sqlite_queries = explode(';', $sqlite_db);

                // Varre as querys e cria o banco de dados necessário.
                foreach($sqlite_queries as $query)
                {
                    $this->sqlite->query($query);
                }

                // Salva as alterações no banco de dados.
                $this->sqlite->commit();
            }
        }

        // Salva os detalhamentos do endereço ip no banco de dados.
        $this->logIpDetails();

        // @Todo: Verificações de blacklist
    }

    /**
     * Obtém o useragent utilizado para as requisições.
     *
     * @return string
     */
    public function getUserAgent()
    {
        if(!is_null($this->userAgent)) // Se estiver prenchido, não é necessário ler novamente...
            return $this->userAgent;
        
        return ($this->userAgent = $this->getApp()->getContainer()->get('request')->getHeader('user-agent')[0]);
    }

    /**
     * Obtém o endereço ip do jogador.
     *
     * @return string
     */
    public function getIpAddress()
    {
        if(!is_null($this->ipAddress)) // Se estiver prenchido, não é necessário ler novamente...
            return $this->ipAddress;

        // Inicializa o endereço de ip do jogador como
        // Desconhecido.
        $ipAddress = '?.?.?.?';

        // Possiveis variaveis para se obter o endereço ip do cliente.
        // issue #10: HTTP_CF_CONNECTING_IP-> Usuário usando proteção do cloudfire.
        $_vars = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED',
                  'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];

        // Varre as opções para retornar os dados ao painel de controle.
        foreach($_vars as $ip)
        {
            if(getenv($ip) !== false)
            {
                $ipAddress = getenv($ip);
                break;
            }
        }

        return ($this->ipAddress = $ipAddress);
    }

    /**
     * Método para criar os logs necessários para informações do IP como:
     * -> Cidade, pais, etc... (Os dados podem não ser completamente precisos, é mais para estatistica de regiões e etc...)
     */
    public function logIpDetails()
    {
        // Configuração desativada, não é necessário finalizar informações de log.
        if(!BRACP_LOG_IP_DETAILS || is_null($this->sqlite))
            return;

        // Obtém o endereço ip do jogador.
        $ipAddress = $this->getIpAddress();
        $userAgent = $this->getUserAgent();

        // @Todo: Salvar informações do endereço ip para o jogador.

        return;
    }

    /**
     * @see brACPMiddleware::__invoke()
     */
    public function __invoke($request, $response, $next)
    {
        return parent::__invoke($request, $response, $next);
    }
}



