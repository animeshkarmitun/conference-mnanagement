<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TravelEvent;
use App\Listeners\SendTravelNotification;
use App\Listeners\SendTravelEmailNotification;
use App\Events\TaskEvent;
use App\Listeners\SendTaskNotification;
use App\Listeners\SendTaskEmailNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TravelEvent::class => [
            SendTravelNotification::class,
            SendTravelEmailNotification::class,
        ],
        TaskEvent::class => [
            SendTaskNotification::class,
            SendTaskEmailNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
