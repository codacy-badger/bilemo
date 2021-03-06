<?php

namespace App\Domain\Common\Exception;

use Throwable;

final class AuthorizationException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
