<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule::command('reports:weekly')->weekly()->sundays()->at('08:00');
// Schedule::command('streaks:check')->dailyAt('01:00');
