<?php

namespace App\Providers;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Gate::define('update-event', function ($user, $event) {
            return $user->id === $event->user_id;
        });
        Gate::define('delete-attendee', function (User $user,Event $event, Attendee $attendee) {
            return $user->id === $event->user_id || $attendee->id === $attendee->user_id;
        });
    }
}
