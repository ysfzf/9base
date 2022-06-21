<?php
namespace App\Exceptions;

use Throwable;

class TokenException extends \Exception
{
    public function __construct($message = "Unauthenticated", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
