<?php

namespace Alltvex\ScoutOpenSearch\Jobs\Stages;

use Alltvex\ScoutOpenSearch\OpenSearch\Index;
use Alltvex\ScoutOpenSearch\OpenSearch\Params\Indices\Alias\Get;
use Alltvex\ScoutOpenSearch\OpenSearch\Params\Indices\Alias\Update;
use Alltvex\ScoutOpenSearch\Searchable\ImportSource;
use OpenSearch\Client;

/**
 * @internal
 */
final class SwitchToNewAndRemoveOldIndex
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
        $params = Get::anyIndex($source->searchableAs());
        $response = $opensearch->indices()->getAlias($params->toArray());

        $params = new Update();
        foreach ($response as $indexName => $alias) {
            if ($indexName != $this->index->name()) {
                $params->removeIndex((string) $indexName);
            } else {
                $params->add((string) $indexName, $source->searchableAs());
            }
        }
        $opensearch->indices()->updateAliases($params->toArray());
    }

    public function estimate(): int
    {
        return 1;
    }

    public function title(): string
    {
        return 'Switching to the new index';
    }
}
