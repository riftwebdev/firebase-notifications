<?php

namespace Riftweb\FirebaseNotifications\Mappings;


use Riftweb\FirebaseNotifications\Classes\FcmNotification;

class FcmNotificationMapping
{
    public const string FLUTTER_CLICK_ACTION = "FLUTTER_NOTIFICATION_CLICK";

    public static function isValidToken(mixed $token): bool
    {
        return is_string($token) && !empty($token);
    }

    public static function isValidTopic(mixed $topic): bool
    {
        if (!is_string($topic)) {
            return false;
        }

        $topic = str($topic)->trim();

        if (!$topic->isMatch('/^[a-zA-Z0-9_\/-]+$/')) {
            return false;
        }

        return $topic->isNotEmpty();
    }

    public static function mapHeader(string $generateAuthToken): array
    {
        return [
            'Authorization' => "Bearer $generateAuthToken",
            'Content-Type' => 'application/json',
        ];
    }
}
