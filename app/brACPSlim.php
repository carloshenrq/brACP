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

use Doctrine\ORM\EntityManager;
use Model\Login;

/**
 *
 */
class brACPSlim extends Slim\Slim
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct($userSettings = [])
    {
        // Initialize session for this app.
        session_cache_limiter(false);
        session_start();

        // Loads the default settings for this app.
        parent::__construct($userSettings);

        // Add the template folder to smarty.
        $this->view()->setTemplatesDirectory(BRACP_TEMPLATE_DIR);

        // Add the new middleware to run.
        $this->add(new \Slim\Middleware\ContentTypes());
        $this->add(new \brAMiddlewareDoctrine());
        $this->add(new \brAMiddlewareRoutes());
    }


    /**
     * Cria a conta indicada para o usuário.
     *
     * @param Login $acc Objeto de acc para criação da conta.
     *
     * @return boolean
     */
    public function createAccount(Login $acc)
    {
        // Verifica quantos usuários possuem 
        $users = count($this->getEntityManager()->getRepository('Model\Login')->findBy([
            'userid' => $acc->getUserid()
        ]));

        // Nome de usuário já em uso!
        if($users > 0)
            return false;

        // Grava a conta no banco de dados.
        $this->getEntityManager()->persist($acc);
        $this->getEntityManager()->flush();

        // @TODO: Disparar eventos de e-mail e validação de código.

        // Retorna verdadeiro que a conta foi criada.
        return true;
    }

    /**
     * @param $em Doctrine\ORM\EntityManager
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }
}

