<?php

namespace Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;

/**
 * Classe de repositório padrão para as entidades.
 *
 * @abstract
 */
abstract class AppRepository extends EntityRepository
{
    /**
     * Verifica dados de contra expressão regulares.
     *
     * @param string $text Dados de texto
     * @param string $regexp Dados de expressão regular. 
     *
     * @return boolean Verdadeiro se for válida.
     */
    public function verify($text, $regexp)
    {
        return (preg_match('/'.$regexp.'/', $text) == 1);
    }

    /**
     * Verifica se 2 textos são iguais.
     *
     * @param string $text
     * @param string $compare
     *
     * @return boolean Verdadeiro se forem.
     */
    public function isEquals($text, $compare)
    {
        return hash('md5', $text) === hash('md5', $compare);
    }

    /**
     * Método para salvar a entity no banco de dados.
     *
     * @final
     * @param object $entity
     */
    public final function save($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

    /**
     * Método para atualizar a entity no banco de dados.
     *
     * @final
     * @param object $entity
     */
    public final function update($entity)
    {
        $this->_em->merge($entity);
        $this->_em->flush();
    }

    /**
     * Método utilizado para remover os dados de entity no banco de dados.
     *
     * @final
     * @param object $entity
     */
    public final function remove($entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }

    /**
     * Método utilizado para atualizar os dados de uma entity.
     * 
     * @final
     * @param object $entity
     */
    public final function refresh($entity)
    {
        $this->_em->refresh($entity);
    }

    /**
     * Obtém a aplicação que está em execução global para o repositório.
     *
     * @final
     * @return \App
     */
    public final function getApp()
    {
        return \App::getInstance();
    }
}

