<?php

use App\Jobs\CalculateSimilarities;
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('01:30');
Schedule::command('backup:monitor')->daily()->at('03:00');
Schedule::job(new CalculateSimilarities)->hourly();
