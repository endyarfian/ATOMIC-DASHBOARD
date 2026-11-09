<?php

use App\Services\SessionInterruptionDetector;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sessions:detect-interruptions', function () {
    $ids = app(SessionInterruptionDetector::class)->detect();

    $this->info('Session interruption scan complete. Persisted interrupted sessions: ' . count($ids));
})->purpose('Persist sessions whose device heartbeat has timed out');

Schedule::command('sessions:detect-interruptions')
    ->everyMinute()
    ->withoutOverlapping();
