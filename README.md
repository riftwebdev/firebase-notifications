# Firebase Notifications 📩

[![Latest Version](https://img.shields.io/packagist/v/riftweb/firebase-notifications?style=flat-square)](https://packagist.org/packages/riftweb/firebase-notifications)
[![Total Downloads](https://img.shields.io/packagist/dt/riftweb/firebase-notifications?style=flat-square)](https://packagist.org/packages/riftweb/firebase-notifications)
[![License](https://img.shields.io/github/license/riftwebdev/firebase-notifications?style=flat-square)](https://github.com/riftwebdev/firebase-notifications/blob/main/LICENSE.md)
[![Website](https://img.shields.io/badge/Website-RIFT%20%7C%20Web%20Development-black?style=flat-square)](https://riftweb.com)

A [Laravel](https://laravel.com/) package that lets you use the new FCM Http V1 API and send push notifications with ease.

## Summary
1. [Install](https://github.com/riftwebdev/firebase-notifications#install)
    - [Laravel](https://github.com/riftwebdev/firebase-notifications#laravel)
2. [Usage](https://github.com/riftwebdev/firebase-notifications#usage)
    - [Topics](https://github.com/riftwebdev/firebase-notifications#firebase)
        - [Subscribe](https://github.com/riftwebdev/firebase-notifications#subscribe)
        - [Unsubscribe](https://github.com/riftwebdev/firebase-notifications#unsubscribe)
        - [List Token Subscribed Topics](https://github.com/riftwebdev/firebase-notifications#list-token--subscribed-topics)
    - [Notification](https://github.com/riftwebdev/firebase-notifications#notification)

## Install
If your Firebase project is already configured, skip this part and go to the [Usage section](https://github.com/riftwebdev/firebase-notifications#usage) section.

### Firebase
TBD

### Laravel
1. Install via Composer
```bash
composer require riftwebdev/firebase-notifications
```

2. Publish the config file
```bash
php artisan vendor:publish --tag=firebase-notifications --ansi --force
```

2. Place your Firebase JSON on your project.

   <img width="396" height="639" alt="image" src="https://github.com/user-attachments/assets/fedc0c71-a5df-4f4c-bc94-5436428dd190" />

3. Retrieve the .env variables from your Firebase Console -> Project Settings -> General and watch firebaseConfig.

   <img width="954" height="864" alt="5" src="https://github.com/user-attachments/assets/20919b2f-ff11-46c7-a3c2-89ab5c22fe56" />

4. Assign values to the .env variables

```env
FCM_API_KEY="<Firebase apiKey>"
FCM_AUTH_DOMAIN="<Firebase authDomain>"
FCM_PROJECT_ID="<Firebase projectId>"
FCM_STORAGE_BUCKET="<Firebase storageBucket>"
FCM_MESSAGING_SENDER_ID="<Firebase messagingSenderId>"
FCM_APP_ID="<Firebase appId>"
FCM_JSON="<path of the JSON file from Firebase>"
```

## Usage

### Topics

Topics are used to make groups of device tokens. They will allow you to send notification directly to the topic where users are registered in.

#### Subscribe

##### Single Token to Single Topic

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$token = "example token";
$topic = "exampleTopic";

FcmTokenTopic::subscribeToTopic($token, $topic);
```

##### Multiple Tokens to Single Topic

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$tokens = ["example token 1", "example token 2"];

$topic = "exampleTopic";

FcmTokenTopic::subscribeToTopic($tokens, $topic);
```

##### Single Token to Multiple Topics

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$token = "example token";
$topics = ["exampleTopic1", "exampleTopic2"];

FcmTokenTopic::subscribeToTopics($token, $topics);
```

##### Multiple Tokens to Multiple Topics

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$tokens = ["example token 1", "example token 2"];
$topics = ["exampleTopic1", "exampleTopic2"];

FcmTokenTopic::subscribeToTopics($tokens, $topics);
```

#### Unsubscribe

##### Single Token to Single Topic

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$token = "example token";
$topic = "exampleTopic";

FcmTokenTopic::unsubscribeToTopic($token, $topic);
```

##### Multiple Tokens to Single Topic

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$tokens = ["example token 1", "example token 2"];

$topic = "exampleTopic";

FcmTokenTopic::unsubscribeToTopic($tokens, $topic);
```

##### Single Token to Multiple Topics

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$token = "example token";
$topics = ["exampleTopic1", "exampleTopic2"];

FcmTokenTopic::unsubscribeToTopics($token, $topics);
```

##### Multiple Tokens to Multiple Topics

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$tokens = ["example token 1", "example token 2"];
$topics = ["exampleTopic1", "exampleTopic2"];

FcmTokenTopic::unsubscribeToTopics($tokens, $topics);
```

#### List Token Subscribed Topics

```php
use Riftweb\FirebaseNotifications\Facades\FcmTokenTopic;

$token = "example token 1";

FcmTokenTopic::getTopics($token);

```

## Notification

You can send notification to specific tokens or to topics, and both at same time.

**Beware, if you set topics and tokens at same time, both will receive (whoever is subscribed to the topics + the extra tokens**

### 
```php
use Riftweb\FirebaseNotifications\Facades\FcmNotification;

FcmNotification::create()
  ->setTitle('Example Notification')
  ->setBody('Lorem ipsum dolores')
  ->setImage(asset('images/example.png'))
  ->setCategory('develop') // Compatible with iOS
  ->setSound('blingbling') // Compatible with iOS
  ->setFlutterClickAction() // Only use this if you're using flutter - Will set click_action as FLUTTER_NOTIFICATION_CLICK, otherwise use setClickAction()
  ->setLink('https://example.com') // Used for webpush notifications
  ->setData([
    'user_id' => 1,
    'foo' => 'bar'
  ]) // Extra Data
  ->setTopics(['topic 1', 'topic 2']) // Set this notification to be sent for certain topics
  ->setTokens(['token 1', 'token 2']) // Specify which tokens will receive this notification
  ->send();

```

### Notification Mapping
```php
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
```

## Contributing 🤝

Contributions are welcome! Please follow:
1. Fork the repository
2. Create your feature branch
3. Commit changes
4. Push to the branch
5. Open a PR

## License 📄

MIT License - See [LICENSE](LICENSE.md) for details.