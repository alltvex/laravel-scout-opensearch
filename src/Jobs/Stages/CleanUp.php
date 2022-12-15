<?php

namespace Alltvex\ScoutOpenSearch\Jobs\Stages;

use Alltvex\ScoutOpenSearch\OpenSearch\Params\Indices\Alias\Get as GetAliasParams;
use Alltvex\ScoutOpenSearch\OpenSearch\Params\Indices\Delete as DeleteIndexParams;
use Alltvex\ScoutOpenSearch\Searchable\ImportSource;
use OpenSearch\Client;
use OpenSearch\Common\Exceptions\OpenSearchException;

/**
 * @internal
 */
final class CleanUp
{
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

    public function handle(Client $opensearch): void
    {
        $source = $this->source;
        $params = GetAliasParams::anyIndex($source->searchableAs());
        try {
            $response = $opensearch->indices()->getAlias($params->toArray());
        } catch (OpenSearchException $e) {
            $response = [];
        }
        foreach ($response as $indexName => $data) {
            foreach ($data['aliases'] as $alias => $config) {
                if (array_key_exists('is_write_index', $config) && $config['is_write_index']) {
                    $params = new DeleteIndexParams((string) $indexName);
                    $opensearch->indices()->delete($params->toArray());
                    continue 2;
                }
            }
        }
    }

    public function title(): string
    {
        return 'Clean up';
    }

    public function estimate(): int
    {
        return 1;
    }
}
