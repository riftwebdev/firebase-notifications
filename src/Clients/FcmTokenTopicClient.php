<?php
namespace Riftweb\FirebaseNotifications\Clients;

use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Riftweb\FirebaseNotifications\Exceptions\FcmTokenTopicCallException;
use Riftweb\FirebaseNotifications\Mappings\FcmTokenTopicMapping;

class FcmTokenTopicClient
{
    protected string $url;

    public function __construct(
        protected Client $client,
        protected FcmGoogleClient $fcmGoogleClient
    )
    {
        $this->url = FcmTokenTopicMapping::TOKEN_API_URL;
    }

    /**
     * Make a call to the FCM Token API
     *
     * @param string $endpoint
     * @param string $method
     * @param array $body
     *
     * @return StreamInterface
     *
     * @throws FcmTokenTopicCallException
     */
    private function call(
        string $endpoint,
        string $method = 'POST',
        array $body = [],
    ): StreamInterface
    {
        try {
            return $this->client->request(
                $method,
                $endpoint,
                [
                    'headers' => FcmTokenTopicMapping::mapHeader(
                        $this->fcmGoogleClient->generateAuthToken()
                    ),
                    'json' => $body,
                ]
            )->getBody();
        } catch (Throwable $e) {
            throw new FcmTokenTopicCallException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Subscribe FCM Tokens to a topic
     *
     * @param array $tokens
     * @param string $topic
     *
     * @return StreamInterface
     *
     * @throws FcmTokenTopicCallException
     */
    public function subscribeTokensToTopic(array $tokens, string $topic): StreamInterface
    {
        return $this->call(
            FcmTokenTopicMapping::TOKEN_API_URL . ':' . FcmTokenTopicMapping::SUBSCRIBE_TOKENS_TO_TOPICS_ENDPOINT,
            'POST',
            [
                'to' => "/topics/" . $topic,
                'registration_tokens' => $tokens,
            ]
        );
    }

    /**
     * Unsubscribe FCM Tokens from a topic
     *
     * @param array $tokens
     * @param string $topic
     *
     * @return StreamInterface
     *
     * @throws FcmTokenTopicCallException
     */
    public function unsubscribeTokensFromTopic(array $tokens, string $topic): StreamInterface
    {
        return $this->call(
            FcmTokenTopicMapping::TOKEN_API_URL . ':' . FcmTokenTopicMapping::UNSUBSCRIBE_TOKENS_FROM_TOPICS_ENDPOINT,
            'POST',
            [
                'to' => "/topics/" . $topic,
                'registration_tokens' => $tokens,
            ]
        );
    }

    /**
     * Get topics for a specific FCM token
     *
     * @param string $token
     *
     * @return StreamInterface
     *
     * @throws FcmTokenTopicCallException
     */
    public function getTopics(string $token): StreamInterface
    {
        return $this->call(
            FcmTokenTopicMapping::TOKEN_INFO_API_URL . "/$token?details=true",
            'GET'
        );
    }
}
