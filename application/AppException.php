<?php

/**
 * Classe abstrata para gerênciamento das exceptions da aplicação.
 */
class AppException extends Exception
{
    /**
     * @see IAppException::__construct()
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
