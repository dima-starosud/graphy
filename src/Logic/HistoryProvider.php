<?php

namespace Staro\Graphy\Logic;

use Staro\Graphy\Utils\FileCache;

final class HistoryProvider {
    const CACHE_KEY = 'history';
    /**
     * @var FileCache
     */
    private $cache;

    function __construct(FileCache $cache) {
        $this->cache = $cache;
    }

    function getHistory(int $day = 0): array {
        $history = $this->cache->get( static::CACHE_KEY, [] );

        if ($day < 1) {
            return $history;
        }

        $result = [];
        foreach (range( 1, $day - 1 ) as $day) {
            $result = array_merge( $result, $history[$day] ?? [] );
        }
        return $result;
    }
}
