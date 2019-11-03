<?php


namespace Staro\Graphy\Logic;


final class HistoryProvider {
    const CACHE_KEY = 'history';
    /**
     * @var FileCache
     */
    private $cache;

    function __construct(FileCache $cache) {
        $this->cache = $cache;
    }

    function getHistory(int $day): array {
        $history = $this->cache->get( static::CACHE_KEY, [] );
        $result = [];
        foreach (range( 1, $day - 1 ) as $day) {
            $result = array_merge( $result, $history[$day] ?? [] );
        }
        return $result;
    }
}
