<?php

namespace Staro\Graphy\Logic;

final class GenerationLogic {
    /**
     * @var WorkersProvider
     */
    private $workersProvider;

    function __construct(WorkersProvider $workersProvider) {
        $this->workersProvider = $workersProvider;
    }

    /**
     * @param int[] $workerIds
     * @param int[][] $history
     * @param int[] $teamSizes
     * @return int[][]
     */
    function generate(array $workerIds, array $history, array $teamSizes): array {
        $seenPairs = [];
        foreach ($history as $team) {
            foreach (combinations( $team, 2 ) as $pair) {
                $seenPairs[serialize( $pair )] = 1 + ($seenPairs[serialize( $pair )] ?? 0);
            }
        }

        $result = [];
        $combinations = $this->teamCombinations( $workerIds, $teamSizes );
        foreach ($combinations as $team) {
            $used = false;
            foreach (combinations( $team, 2 ) as $pair) {
                if (($seenPairs[serialize( $pair )] ?? 0) >= $this->maxPairRepetitionCount( $pair )) {
                    $used = true;
                    break;
                }
            }
            if (!$used) {
                $result[] = $team;
            }
        }

        return $result;
    }

    private function teamCombinations($workerIds, $teamSizes) {
        $workers = $this->workersProvider->getWorkers();
        $groupedWorkerIds = [Worker::CARRIER => [], Worker::DRIVER => []];
        foreach ($workerIds as $id) {
            $groupedWorkerIds[$workers[$id]->getPost()][] = $id;
        }

        $result = array_map( function ($size) use ($groupedWorkerIds) {
            foreach (combinations( $groupedWorkerIds[Worker::CARRIER], $size - 1 ) as $carriers) {
                foreach ($groupedWorkerIds[Worker::DRIVER] as $driver) {
                    yield array_merge( $carriers, [$driver] );
                }
            }
        }, $teamSizes );

        $result = array_map( 'iterator_to_array', $result );

        return array_merge( ...$result );
    }

    private function maxPairRepetitionCount($pair) {
        $posts = array_unique( array_map( function ($id) {
            return $this->workersProvider->getWorkers()[$id]->getPost();
        }, $pair ) );
        if ($posts === [Worker::DRIVER]) {
            return 0;
        } elseif ($posts === [Worker::CARRIER]) {
            return 1;
        } else {
            return 2;
        }
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
