<?php
namespace Riftweb\FirebaseNotifications\Clients;

use Google\Client as GoogleClient;
use Google\Exception;
use Google\Service\FirebaseCloudMessaging;

class FcmGoogleClient
{
    private GoogleClient $client;
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new GoogleClient();

        $this->client->setAuthConfig(
            config('fcm_notifications.fcm_json_path')
        );

        $this->client->addScope(FirebaseCloudMessaging::CLOUD_PLATFORM);
    }

    public function generateAuthToken(): string
    {
        $accessTokenResponse = $this->generateAccessToken();

        $this->client->setAccessToken($accessTokenResponse);

        return $accessTokenResponse["access_token"];
    }

    private function generateAccessToken(): array
    {
        $this->client->fetchAccessTokenWithAssertion();
        return $this->client->getAccessToken();
    }
}
