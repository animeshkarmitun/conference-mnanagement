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
use App\Events\ProfileEvent;
use App\Listeners\SendProfileNotification;
use App\Events\SessionEvent;
use App\Listeners\SendSessionNotification;
use App\Listeners\SendSessionEmailNotification;
use App\Events\ConferenceEvent;
use App\Listeners\SendConferenceNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Disable default email verification notifications to restrict non-critical emails
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        TravelEvent::class => [
            SendTravelNotification::class,
            // Sending travel emails disabled per restrictions
            // SendTravelEmailNotification::class,
        ],
        TaskEvent::class => [
            SendTaskNotification::class,
            // Sending task emails disabled per restrictions
            // SendTaskEmailNotification::class,
        ],
        ProfileEvent::class => [
            SendProfileNotification::class,
        ],
        SessionEvent::class => [
            SendSessionNotification::class,
            // Sending session emails disabled per restrictions
            // SendSessionEmailNotification::class,
        ],
        ConferenceEvent::class => [
            SendConferenceNotification::class,
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
