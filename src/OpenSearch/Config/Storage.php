<?php

namespace Alltvex\ScoutOpenSearch\OpenSearch\Config;

class Storage
{
    protected string $config;

    /**
     * @param  string  $config
     */
    private function __construct(string $config)
    {
        $this->config = $config;
    }

    /**
     * @param  string  $config
     * @return Storage
     */
    public static function load(string $config): self
    {
        return new self($config);
    }

    /**
     * @return array
     */
    public function hosts(): array
    {
        return explode(',', $this->loadConfig('host'));
    }

    /**
     * @return ?string
     */
    public function user(): ?string
    {
        return $this->loadConfig('user');
    }

    /**
     * @return ?string
     */
    public function password(): ?string
    {
        return $this->loadConfig('password');
    }

    /**
     * @return ?string
     */
    public function accessKey(): ?string
    {
        return $this->loadConfig('access_key');
    }

    /**
     * @return ?string
     */
    public function secret(): ?string
    {
        return $this->loadConfig('secret');
    }

    /**
     * @return ?string
     */
    public function region(): ?string
    {
        return $this->loadConfig('region');
    }

    /**
     * @return ?int
     */
    public function queueTimeout(): ?int
    {
        return (int) $this->loadConfig('queue.timeout') ?: null;
    }

    /**
     * @param  string  $path
     * @return mixed
     */
    private function loadConfig(string $path): mixed
    {
        return config($this->getKey($path));
    }

    /**
     * @param  string  $path
     * @return string
     */
    private function getKey(string $path): string
    {
        return sprintf('%s.%s', $this->config, $path);
    }
}
