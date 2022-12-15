<?php

namespace Alltvex\ScoutOpenSearch\Jobs;

use Alltvex\ScoutOpenSearch\ProgressReportable;
use Alltvex\ScoutOpenSearch\Searchable\ImportSource;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use OpenSearch\Client;

/**
 * @internal
 */
final class Import
{
    use Queueable;
    use ProgressReportable;

    /**
     * @var ImportSource
     */
    private $source;

    /**
     * @param  ImportSource  $source
     */
    public function __construct(ImportSource $source)
    {
        $this->source = $source;
    }

    /**
     * @param  Client  $opensearch
     */
    public function handle(Client $opensearch): void
    {
        $stages = $this->stages();
        $estimate = $stages->sum->estimate();
        $this->progressBar()->setMaxSteps($estimate);
        $stages->each(function ($stage) use ($opensearch) {
            $this->progressBar()->setMessage($stage->title());
            $stage->handle($opensearch);
            $this->progressBar()->advance($stage->estimate());
        });
    }

    private function stages(): Collection
    {
        return ImportStages::fromSource($this->source);
    }
}
