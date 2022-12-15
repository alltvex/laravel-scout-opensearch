<?php

namespace Alltvex\ScoutOpenSearch\Jobs\Stages;

use Alltvex\ScoutOpenSearch\OpenSearch\Index;
use Alltvex\ScoutOpenSearch\OpenSearch\Params\Indices\Refresh;
use OpenSearch\Client;

/**
 * @internal
 */
final class RefreshIndex
{
    /**
     * @var Index
     */
    private $index;

    /**
     * RefreshIndex constructor.
     *
     * @param  Index  $index
     */
    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    public function handle(Client $opensearch): void
    {
        $params = new Refresh($this->index->name());
        $opensearch->indices()->refresh($params->toArray());
    }

    public function estimate(): int
    {
        return 1;
    }

    public function title(): string
    {
        return 'Refreshing index';
    }
}
