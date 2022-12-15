<?php

declare(strict_types=1);

namespace Alltvex\ScoutOpenSearch;

use Alltvex\ScoutOpenSearch\Console\Commands\FlushCommand;
use Alltvex\ScoutOpenSearch\Console\Commands\ImportCommand;
use Alltvex\ScoutOpenSearch\Engines\OpenSearchEngine;
use Alltvex\ScoutOpenSearch\Searchable\DefaultImportSourceFactory;
use Alltvex\ScoutOpenSearch\Searchable\ImportSourceFactory;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;
use Laravel\Scout\ScoutServiceProvider;
use OpenSearch\Client;

final class ScoutOpenSearchServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'scout');

        $this->app->make(EngineManager::class)->extend(OpenSearchEngine::class, function () {
            $opensearch = app(Client::class);

            return new OpenSearchEngine($opensearch);
        });
        $this->registerCommands();
    }

    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->app->register(ScoutServiceProvider::class);
        $this->app->bind(ImportSourceFactory::class, DefaultImportSourceFactory::class);
    }

    /**
     * Register artisan commands.
     *
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportCommand::class,
                FlushCommand::class,
            ]);
        }
    }
}
