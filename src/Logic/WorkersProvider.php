<?php


namespace Staro\Graphy\Logic;


final class WorkersProvider {
    const CACHE_KEY = 'workers';
    /**
     * @var FileCache
     */
    private $cache;

    function __construct(FileCache $cache) {
        $this->cache = $cache;
    }

    /**
     * @return Worker[]
     */
    function getWorkers(): array {
        return $this->cache->get( static::CACHE_KEY, [] );
    }
}
