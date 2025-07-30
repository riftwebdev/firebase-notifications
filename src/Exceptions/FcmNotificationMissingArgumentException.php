<?php

namespace Riftweb\FirebaseNotifications\Exceptions;

use Exception;
use Throwable;

class FcmNotificationMissingArgumentException extends Exception
{
    public function __construct(
        string $message = "",
        int $code = 400,
        ?Throwable $previous = null
    )
    {
        $baseMessage = "FcmNotification is missing required arguments: ";
        parent::__construct($baseMessage . $message , $code, $previous);
    }
}
