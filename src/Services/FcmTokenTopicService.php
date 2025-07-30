<?php
namespace Riftweb\FirebaseNotifications\Services;

use Illuminate\Support\Collection;
use Riftweb\FirebaseNotifications\Clients\FcmTokenTopicClient;
use Riftweb\FirebaseNotifications\Exceptions\FcmTokenTopicCallException;
use Riftweb\FirebaseNotifications\Exceptions\FcmTooManyTokensException;
use Riftweb\FirebaseNotifications\Helpers\Helper;
use Riftweb\FirebaseNotifications\Mappings\FcmTokenTopicMapping;
use Psr\Http\Message\StreamInterface;

class FcmTokenTopicService
{
    public function __construct(
        protected FcmTokenTopicClient  $fcmTokenTopicClient,
        protected FcmTokenTopicMapping $tokenMapping,
        protected Helper               $helper
    )
    {
    }

    /**
     * Subscribe FCM Tokens to topics
     *
     * @param string|array $tokens
     * @param string|array $topics
     *
     * @return Collection
     *
     * @throws FcmTooManyTokensException
     * @throws FcmTokenTopicCallException
     */
    public function subscribeToTopics(
        string|array $tokens,
        string|array $topics
    ): Collection
    {
        $tokens = $this->helper::mapItemsToArray($tokens);

        if (count($tokens) > 999) {
            throw new FcmTooManyTokensException();
        }

        $responses = collect();
        collect(
            $this->helper::mapItemsToArray($topics)
        )->each(function ($topic) use ($tokens, &$responses) {
            $responses->push($this->fcmTokenTopicClient->subscribeTokensToTopic($tokens, $topic));
        });

        return $responses->values();
    }

    /**
     * Subscribe FCM Tokens to a topic
     *
     * @param string|array $tokens
     * @param string       $topic
     *
     * @return StreamInterface
     *
     * @throws FcmTokenTopicCallException
     * @throws FcmTooManyTokensException
     */
    public function subscribeToTopic(string|array $tokens, string $topic): StreamInterface
    {
        $tokens = $this->helper::mapItemsToArray($tokens);

        if (count($tokens) > 999) {
            throw new FcmTooManyTokensException();
        }

        return $this->fcmTokenTopicClient->subscribeTokensToTopic($tokens, $topic);
    }

    /**
     * Unsubscribe FCM Tokens from topics
     *
     * @param string|array $tokens
     * @param string|array $topics
     *
     * @return Collection
     *
     * @throws FcmTokenTopicCallException
     * @throws FcmTooManyTokensException
     */
    public function unsubscribeToTopics(
        string|array $tokens,
        string|array $topics
    ): Collection
    {
        $tokens = $this->helper::mapItemsToArray($tokens);

        if (count($tokens) > 999) {
            throw new FcmTooManyTokensException();
        }

        $responses = collect();
        collect(
            $this->helper::mapItemsToArray($topics)
        )->each(function ($topic) use ($tokens, &$responses) {
            $responses->push($this->fcmTokenTopicClient->unsubscribeTokensFromTopic($tokens, $topic));
        });

        return $responses->values();
    }

    /**
     * Unsubscribe FCM Tokens from a topic
     *
     * @param string|array $tokens
     * @param string       $topic
     *
     * @return StreamInterface
     *
     * @throws FcmTokenTopicCallException
     * @throws FcmTooManyTokensException
     */
    public function unsubscribeToTopic(
        string|array $tokens,
        string $topic
    ): StreamInterface
    {
        $tokens = $this->helper::mapItemsToArray($tokens);

        if (count($tokens) > 999) {
            throw new FcmTooManyTokensException();
        }

        return $this->fcmTokenTopicClient->unsubscribeTokensFromTopic($tokens, $topic);
    }

    /**
     * Get an FCM Token subscribed topics
     *
     * @param string $token
     *
     * @return Collection
     *
     * @throws FcmTokenTopicCallException
     */
    public function getTopics(string $token): Collection
    {
        return collect(
            json_decode(
                $this->fcmTokenTopicClient->getTopics($token)->getContents(),
                true
            )
        )->mapWithKeys(function (array $data, $topic) {
            return [
                $topic => [$topic, $data['addDate'] ?? null]
            ];
        });
    }
}
