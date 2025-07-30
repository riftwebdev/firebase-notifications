<?php
namespace Riftweb\FirebaseNotifications\Helpers;

class Helper
{
    public static function mapItemsToArray(string|array $items): array
    {
        if (is_array($items)) {
            return $items;
        }

        return [$items];
    }
}
