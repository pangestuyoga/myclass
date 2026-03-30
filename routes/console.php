<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:generate-daily-sessions')->dailyAt('00:00');
