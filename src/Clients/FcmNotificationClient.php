<?php

namespace Riftweb\FirebaseNotifications\Clients;

use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Riftweb\FirebaseNotifications\Exceptions\FcmNotificationClientException;
use Riftweb\FirebaseNotifications\Mappings\FcmNotificationMapping;
use Throwable;

class FcmNotificationClient
{
    protected string $url;
    protected string $oAuthToken;

    public function __construct(
        protected FcmNotificationMapping $fcmNotificationMapping,
        protected Client $client,
        protected FcmGoogleClient $fcmGoogleClient
    )
    {
        $this->url = config('fcm_notifications.fcm_api_url');
        $this->oAuthToken = $this->fcmGoogleClient->generateAuthToken();
    }

    /**
     * Send a notification to FCM
     *
     * @param array $data
     *
     * @return StreamInterface
     *
     * @throws FcmNotificationClientException
     */
    public function sendFcmNotification(array $data): StreamInterface
    {
        try {
            return $this->client->post(
                $this->url,
                [
                    'headers' => FcmNotificationMapping::mapHeader(
                        $this->fcmGoogleClient->generateAuthToken()
                    ),
                    'json' => $data,
                ]
            )->getBody();
        } catch (Throwable $e) {
            throw new FcmNotificationClientException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
