<?php
namespace Riftweb\FirebaseNotifications\Facades;

use Illuminate\Support\Facades\Facade;
use Riftweb\FirebaseNotifications\Classes\FcmNotification as FcmNotificationAlias;

class FcmNotification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FcmNotificationAlias::class;
    }
}
