<?php
namespace Riftweb\FirebaseNotifications\Exceptions;

use Exception;
use Throwable;

class FcmTooManyTokensException extends Exception
{
    public function __construct(
        string $message = 'Too many tokens provided. Maximum allowed is 999.',
        int $code = 400,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
