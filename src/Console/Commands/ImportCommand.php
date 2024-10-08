<?php

declare(strict_types=1);

namespace Alltvex\ScoutOpenSearch\Console\Commands;

use Alltvex\ScoutOpenSearch\Jobs\Import;
use Alltvex\ScoutOpenSearch\Jobs\QueueableJob;
use Alltvex\ScoutOpenSearch\OpenSearch\Config\Config;
use Alltvex\ScoutOpenSearch\Searchable\ImportSource;
use Alltvex\ScoutOpenSearch\Searchable\ImportSourceFactory;
use Alltvex\ScoutOpenSearch\Searchable\SearchableListFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class ImportCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected $signature = 'scout:import
                            {searchable?* : The name of the searchable}
                            {--queue= : Whether the job should be queued}';

    /**
     * @inheritdoc
     */
    protected $description = 'Create new index and import all searchable into the one';

    /**
     * @inheritdoc
     */
    public function handle(): void
    {
        $this->searchableList((array) $this->argument('searchable'))
        ->each(function ($searchable) {
            $this->import($searchable);
        });
    }

    private function searchableList(array $argument): Collection
    {
        return collect($argument)->whenEmpty(function () {
            $factory = new SearchableListFactory(app()->getNamespace(), app()->path());

            return $factory->make();
        });
    }

    private function import(string $searchable): void
    {
        $sourceFactory = app(ImportSourceFactory::class);
        $source = $sourceFactory::from($searchable);
        $job = new Import($source);
        $job->timeout = Config::queueTimeout();

        if ($this->shouldQueue()) {
            $job = (new QueueableJob())->chain([$job]);
            $job->timeout = Config::queueTimeout();
        }

        $bar = (new ProgressBarFactory($this->output))->create();
        $job->withProgressReport($bar);

        $startMessage = trans('scout::import.start', ['searchable' => "<comment>$searchable</comment>"]);
        $this->line($startMessage);

        /* @var ImportSource $source */
        dispatch($job)->allOnQueue($source->syncWithSearchUsingQueue())
            ->allOnConnection($source->syncWithSearchUsing());

        $doneMessage = trans(config('scout.queue') ? 'scout::import.done.queue' : 'scout::import.done', [
            'searchable' => $searchable,
        ]);
        $this->output->success($doneMessage);
    }

    private function shouldQueue()
    {
        $enabled = config('scout.queue');

        $enabled = is_bool($enabled) ? false : (is_array($enabled) && !empty($enabled) && $enabled['enable'] ?? false);

        return filter_var($this->option('queue') ?: $enabled, FILTER_VALIDATE_BOOLEAN);
    }
}
