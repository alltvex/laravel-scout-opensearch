<?php

namespace Alltvex\ScoutOpenSearch\Jobs\Stages;

use Alltvex\ScoutOpenSearch\OpenSearch\DefaultAlias;
use Alltvex\ScoutOpenSearch\OpenSearch\Index;
use Alltvex\ScoutOpenSearch\OpenSearch\Params\Indices\Create;
use Alltvex\ScoutOpenSearch\OpenSearch\WriteAlias;
use Alltvex\ScoutOpenSearch\Searchable\ImportSource;
use OpenSearch\Client;

/**
 * @internal
 */
final class CreateWriteIndex
{
    /**
     * @var ImportSource
     */
    private $source;

    /**
     * @var Index
     */
    private $index;

    /**
     * @param  ImportSource  $source
     * @param  Index  $index
     */
    public function __construct(ImportSource $source, Index $index)
    {
        $this->source = $source;
        $this->index = $index;
    }

    public function handle(Client $opensearch): void
    {
        $source = $this->source;
        $this->index->addAlias(new WriteAlias(new DefaultAlias($source->searchableAs())));

        $params = new Create(
            $this->index->name(),
            $this->index->config()
        );

        $opensearch->indices()->create($params->toArray());
    }

    public function title(): string
    {
        return 'Create write index';
    }

    public function estimate(): int
    {
        return 1;
    }
}
