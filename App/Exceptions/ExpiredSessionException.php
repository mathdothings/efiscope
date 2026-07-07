<?php

namespace App\Exceptions;

class ExpiredSessionException extends \Exception
{
    public function __construct($message = "A sessão expirou, revalide as credenciais e tente novamente.")
    {
        parent::__construct($message);
    }
}
