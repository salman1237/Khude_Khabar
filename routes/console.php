<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Email each mother her children's meals for the day, every morning.
// Mothers are in Bangladesh; the app's default timezone is UTC, so pin Asia/Dhaka.
Schedule::command('meal-plans:notify-daily')
    ->dailyAt('06:00')
    ->timezone('Asia/Dhaka');

