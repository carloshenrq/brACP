<?php

/**
 * Interface para gerenciamento dos exceptions do framework.
 */
interface IAppException
{
    /**
     * Construtor para as exceptions.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct($message = null, $code = 0);

    /**
     * Obtém a mensagem da exception.
     * @return string
     */
    public function getMessage();

    /**
     * Obtém o código da exception.
     * @return int
     */
    public function getCode();

    /**
     * Obtém o arquivo que ocorreu a exception.
     * @return string
     */
    public function getFile();

    /**
     * Obtém a linha que ocorreu a linha da exception.
     * @return int
     */
    public function getLine();

    /**
     * Obtém os dados de trace completo.
     * @return array
     */
    public function getTrace();

    /**
     * Obtém os caminhos de erro completo em formato string.
     * @return string
     */
    public function getTraceAsString();
}
