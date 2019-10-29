<?php

namespace Staro\Graphy\Logic;

final class GenerationLogic {
    function generate(int $team2count, int $team3count, array $workers, array $history): array {
        $seenPairs = [];
        foreach ($history as $team) {
            foreach (combinations( $team, 2 ) as $pair) {
                $seenPairs[serialize( $pair )] = 1 + ($seenPairs[serialize( $pair )] ?? 0);
            }
        }

        $today = [];

        $total = [];
        foreach ([[3, $team3count], [2, $team2count]] as list($membersCount, $teamsCount)) {
            $combinations = iterator_to_array( combinations( $workers, $membersCount ) );
            shuffle( $combinations );
            $result = [];
            foreach ($combinations as $combination) {
                if (count( $result ) >= $teamsCount) break;

                $used = false;
                foreach (combinations( $combination, 2 ) as $pair) {
                    if (($seenPairs[serialize( $pair )] ?? 0) >= 2) {
                        $used = true;
                        break;
                    }
                }
                if ($used) continue;
                foreach ($combination as $person) {
                    if (in_array( $person, $today )) {
                        $used = true;
                        break;
                    }
                }
                if ($used) continue;

                $result[] = $combination;
                foreach (combinations( $combination, 2 ) as $pair) {
                    $seenPairs[serialize( $pair )] = 1 + ($seenPairs[serialize( $pair )] ?? 0);
                }
                $today = array_merge( $today, $combination );
            }

            $total = array_merge( $total, $result );
        }

        return $total;
    }
}

function combinations($items, int $combination_count) {
    if ($combination_count < 1) {
        yield [];
        return;
    }
    foreach (combinations( $items, $combination_count - 1 ) as $init) {
        $end = end( $init ) ?? -INF;
        foreach ($items as $item) {
            if ($end < $item) {
                yield array_merge( $init, [$item] );
            }
        }
    }
}
