<?php

declare(strict_types=1);

namespace Alltvex\ScoutOpenSearch;

use Alltvex\ScoutOpenSearch\OpenSearch\Config\Config;
use Alltvex\ScoutOpenSearch\OpenSearch\EloquentHitsIteratorAggregate;
use Alltvex\ScoutOpenSearch\OpenSearch\HitsIteratorAggregate;
use Illuminate\Support\ServiceProvider;
use OpenSearch\Client;
use OpenSearch\ClientBuilder;

final class OpenSearchServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/opensearch.php', 'opensearch');

        $this->app->bind(Client::class, function () {
            $clientBuilder = ClientBuilder::create()->setHosts(Config::hosts());
            if ($user = Config::user()) {
                $clientBuilder->setBasicAuthentication($user, Config::password());
            }

            if ($cloudId = Config::accessKey()) {
                $clientBuilder
                    ->setSigV4CredentialProvider([
                        'key' => Config::accessKey(),
                        'secret' => Config::secret(),
                    ])
                    ->setSigV4Region(Config::region());
            }

            return $clientBuilder->build();
        });

        $this->app->bind(
            HitsIteratorAggregate::class,
            EloquentHitsIteratorAggregate::class
        );
    }

    /**
     * @inheritdoc
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/opensearch.php' => config_path('opensearch.php'),
        ], 'config');
    }

    /**
     * @inheritdoc
     */
    public function provides(): array
    {
        return [Client::class];
    }
}
