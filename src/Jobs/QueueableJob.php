<?php

namespace Alltvex\ScoutOpenSearch\Jobs;

use Alltvex\ScoutOpenSearch\ProgressReportable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueableJob implements ShouldQueue
{
    use Queueable;
    use ProgressReportable;

    public function handle(): void
    {
    }
}
