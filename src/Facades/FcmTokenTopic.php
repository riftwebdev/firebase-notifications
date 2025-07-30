<?php
namespace Riftweb\FirebaseNotifications\Facades;

use Illuminate\Support\Facades\Facade;
use Riftweb\FirebaseNotifications\Services\FcmTokenTopicService;

class FcmTokenTopic extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FcmTokenTopicService::class;
    }
}
