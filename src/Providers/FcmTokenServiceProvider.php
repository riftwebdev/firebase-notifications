<?php
namespace Riftweb\FirebaseNotifications\Providers;

use Illuminate\Support\ServiceProvider;
use Riftweb\FirebaseNotifications\Classes\FcmNotification;
use Riftweb\FirebaseNotifications\Clients\FcmTokenTopicClient;
use Riftweb\FirebaseNotifications\Helpers\Helper;
use Riftweb\FirebaseNotifications\Mappings\FcmTokenTopicMapping;
use Riftweb\FirebaseNotifications\Services\FcmTokenTopicService;

class FcmTokenServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../Config/firebase_notifications.php', 'firebase_notifications'
        );

        $this->app->singleton(FcmTokenTopicService::class, function ($app) {
            return new FcmTokenTopicService(
                $app->make(FcmTokenTopicClient::class),
                $app->make(FcmTokenTopicMapping::class),
                $app->make(Helper::class)
            );
        });

        $this->app->bind(FcmNotification::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../Config/firebase_notifications.php' => config_path('firebase_notifications.php'),
        ], 'firebase-notifications');
    }
}
