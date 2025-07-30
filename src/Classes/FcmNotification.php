<?php

namespace Riftweb\FirebaseNotifications\Classes;

use Illuminate\Support\Collection;
use Riftweb\FirebaseNotifications\Clients\FcmNotificationClient;
use Riftweb\FirebaseNotifications\Exceptions\FcmNotificationMissingArgumentException;
use Riftweb\FirebaseNotifications\Mappings\FcmNotificationMapping;
use stdClass;
use Throwable;

class FcmNotification
{
    public const string FLUTTER_CLICK_ACTION = "FLUTTER_NOTIFICATION_CLICK";

    protected Collection $tokens;
    protected Collection $topics;

    public string $title;
    public string $body;
    public ?string $image = null;
    public ?string $click_action = null;
    public array $data = [];
    public ?string $link = null;
    protected ?string $category = 'default';
    protected string $sound = 'default';

    /**
     * FcmNotification constructor.
     * @return self
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * Title of the notification.
     *
     * @return FcmNotification
     *
     * @param string $title
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Body of the notification.
     *
     * @return FcmNotification
     *
     * @param string $body
     */
    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Image of the notification.
     *
     * @param string $image
     *
     * @return FcmNotification
     */
    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Data body of the notification.
     * This is used to send additional data with the notification.
     *
     * @param array $data
     *
     * @return FcmNotification
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the FCM Tokens to send the notification to.
     *
     * @param array $tokens
     *
     * @return FcmNotification
     * @throws FcmNotificationMissingArgumentException
     */
    public function setTokens(Collection|array $tokens): static
    {
        if (is_array($tokens)) {
            $tokens = collect($tokens);
        }

        $tokens->each(function ($token) {
            if (!FcmNotificationMapping::isValidToken($token)) {
                throw new FcmNotificationMissingArgumentException("Invalid token: $token. Tokens are not nullable and must be a valid Firebase Cloud Messaging token.");
            }
        });

        $this->tokens = $tokens;
        return $this;
    }

    /**
     * Set the FCM Topics to send the notification to.
     *
     * @param Collection|array $topics
     *
     * @return FcmNotification
     * @throws FcmNotificationMissingArgumentException
     */
    public function setTopics(Collection|array $topics): static
    {
        if (is_array($topics)) {
            $topics = collect($topics);
        }

        $topics->each(function ($topic) {
            if (!FcmNotificationMapping::isValidTopic($topic)) {
                throw new FcmNotificationMissingArgumentException("Invalid topic name: {$topic}. Topic names are not nullable and must contain only alphanumeric characters, underscores, and hyphens.");
            }
        });

        $this->topics = $topics;
        return $this;
    }

    /**
     * Set the click action for the notification.
     * This is the URL that the notification will open when clicked.
     *
     * @param string $clickAction
     *
     * @return FcmNotification
     */
    public function setClickAction(string $clickAction): static
    {
        $this->click_action = $clickAction;
        return $this;
    }

    public function setFlutterClickAction(): static
    {
        $this->click_action = self::FLUTTER_CLICK_ACTION;
        $this->data = array_merge($this->data, ["click_action" => self::FLUTTER_CLICK_ACTION]);
        return $this;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Set the category for the notification.
     * This is used for iOS notifications to group notifications.
     *
     * @param string $sound
     *
     * @return FcmNotification
     */
    public function setSound(string $sound): static
    {
        $this->sound = $sound;
        return $this;
    }

    /**
     * Set the category for the notification.
     * This is used for iOS notifications to group notifications.
     *
     * @param string $category
     *
     * @return FcmNotification
     */
    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    private function map(): array
    {
        $image = !is_null($this->image) ? asset($this->image) : null;

        return [
            "notification" => [
                "title" => $this->title,
                "body" => $this->body,
                'image' => $image, // Used by Android/iOS
            ],
            "data" => $this->data,
            "android" => [
                "notification" => [
                    "click_action" => $this->click_action
                ]
            ],
            "apns" => [
                "payload" => [
                    "aps" => [
                        "category" => $this->category,
                        "sound" => $this->sound
                    ]
                ],
                "fcm_options" => [
                    "image" => $image,
                ]
            ],
            "webpush" => [
                "notification" => [
                    "title" => $this->title,
                    "body" => $this->body,
                    "icon" => $image,
                ],
                "fcm_options" => [
                    "link" => $this->link
                ]
            ]
        ];
    }

    /**
     * Send the notification to the specified tokens and topics.
     *
     * @throws FcmNotificationMissingArgumentException
     */
    public function send(): stdClass
    {
        if (count($this->tokens) === 0 && count($this->topics) === 0) {
            throw new FcmNotificationMissingArgumentException("You must set at least one token or topic to send the notification.");
        }

        if (empty($this->title)) {
            throw new FcmNotificationMissingArgumentException("title");
        }

        if (empty($this->body)) {
            throw new FcmNotificationMissingArgumentException("body");
        }

        return $this->handleSend();
    }

    /**
     * Handle the sending of the notification.
     *
     * @return stdClass
     */
    private function handleSend(): stdClass
    {
        $fcmNotificationClient = app(FcmNotificationClient::class);

        $message = $this->map();

        $sentMessages = new stdClass();

        $sentMessages->tokens = $this->tokens
            ->mapWithKeys(function ($token) use ($message, &$fcmNotificationClient) {
                try {
                    $fcmNotificationClient->sendFcmNotification(
                        array_merge($message, ["token" => $token])
                    );

                    $result = true;
                } catch (Throwable $e) {
                    $result = false;
                    report($e);
                }

                return [$token => $result];
            });

        $sentMessages['topics'] = $this->topics
            ->mapWithKeys(function ($topic) use ($message, &$fcmNotificationClient) {
                try {
                    $fcmNotificationClient->sendFcmNotification(
                        array_merge($message, ["topic" => $topic])
                    );

                    $result = true;
                } catch (Throwable $e) {
                    report($e);
                    $result = false;
                }

                return [$topic => $result];
            });

        return $sentMessages;
    }
}
