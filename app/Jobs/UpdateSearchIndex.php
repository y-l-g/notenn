<?php

namespace App\Jobs;

use App\Models\Arrangement;
use App\Models\Composer;
use App\Models\Tune;
use App\Models\Tunebook;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateSearchIndex implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): void
    {
        Tune::makeAllSearchable();
        Arrangement::makeAllSearchable();
        Composer::makeAllSearchable();
        Tunebook::makeAllSearchable();
    }
}
