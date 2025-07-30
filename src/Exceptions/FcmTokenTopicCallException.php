<?php

namespace Riftweb\FirebaseNotifications\Exceptions;

use Exception;
use Throwable;

class FcmTokenTopicCallException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 500,
        ?Throwable $previous = null
    )
    {
        $baseMessage = 'An error occurred while trying to call the FCM token topic API.';
        if (!empty($message)) {
            $baseMessage .= ' - ' . $message;
        }

        parent::__construct($baseMessage, $code, $previous);
    }
}
