<?php

namespace Riftweb\FirebaseNotifications\Exceptions;

use Exception;
use Throwable;

class FcmNotificationClientException extends Exception
{
    public function __construct(
        string $message = "",
        int $code = 500,
        ?Throwable $previous = null
    )
    {
        $baseMessage = "FcmNotification failed: ";
        parent::__construct($baseMessage . $message , $code, $previous);
    }
}
