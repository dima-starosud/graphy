<?php

namespace Staro\Graphy\Logic;

final class PairsTracker {
    /**
     * @var array
     */
    private $seenPairs;

    function __construct($history) {
        $this->seenPairs = [];
        foreach ($history as $team) {
            foreach (Utils::pairs( $team ) as $pair) {
                $this->seenPairs[serialize( $pair )] = 1 + $this->count( $pair );
            }
        }
    }

    function count($pair): int {
        return $this->seenPairs[serialize( $pair )] ?? 0;
    }

    function counts($team) {
        return array_map(
            [$this, 'count'],
            iterator_to_array( Utils::pairs( $team ) )
        );
    }
}
