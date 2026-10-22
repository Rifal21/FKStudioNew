<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Each subscription has its own send time (WIB), so the check runs every minute; it is a cheap query.
// The server needs one cron entry: * * * * * php artisan schedule:run
Schedule::command('subscriptions:generate')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer();
