<?php
namespace Riftweb\FirebaseNotifications\Mappings;

class FcmTokenTopicMapping
{
    public const string TOKEN_API_URL = 'https://iid.googleapis.com/iid/v1';
    public const string TOKEN_INFO_API_URL = 'https://iid.googleapis.com/iid/info';
    public const string SUBSCRIBE_TOKENS_TO_TOPICS_ENDPOINT = 'batchAdd';
    public const string UNSUBSCRIBE_TOKENS_FROM_TOPICS_ENDPOINT = 'batchRemove';

    public static function mapItemsToArray(string|array $tokens): array
    {
        if (is_array($tokens)) {
            return $tokens;
        }

        return [$tokens];
    }

    public static function mapHeader(string $fcmToken): array
    {
        return [
            'Authorization' => "Bearer $fcmToken",
            'Content-Type' =>  'application/json',
            'access_token_auth' => 'true'
        ];
    }
}
