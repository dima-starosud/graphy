<?php


namespace Staro\Graphy\Logic;


final class Uploader {
    const WORKERS_FIRST_INDEX = 3;

    /**
     * @var FileCache
     */
    private $cache;

    function __construct(FileCache $cache) {
        $this->cache = $cache;
    }

    public function upload(string $text) {
        list( $workers, $history ) = $this->parseTable( $text );

        $this->cache->set( 'workers', $workers );
        $this->cache->set( 'history', $history );
    }

    static function parseTable(string $text) {
        $table = array_map( function ($line) {
            return explode( "\t", $line );
        }, explode( "\n", $text ) );

        $table = array_map( null, ...$table );

        $workers = $table[0];

        $first_day = 0;
        while (intval( $table[++$first_day][0] ) != 1) ;
        $length = 0;
        while (intval( $table[++$length + $first_day][0] ) > 1) ;

        $days = array_slice( $table, $first_day, $length );

        $workers = static::parseWorkers( $workers );

        $indexes = array_map( function ($worker) {
            return $worker->getIndex();
        }, $workers );
        $history = static::parseHistory( $indexes, $days );

        return [$workers, $history];
    }

    static function parseWorkers($names) {
        $index = static::WORKERS_FIRST_INDEX;
        $workers = [];
        foreach ([Worker::CARRIER, Worker::DRIVER] as $post) {
            while (empty( $names[$index] )) {
                ++$index;
            };
            while (!empty( $names[$index] )) {
                $workers[$index] = new Worker( $index, $names[$index], $post );
                ++$index;
            }
        }

        return $workers;
    }

    static function parseHistory($indexes, $days) {
        $history = [];
        $historyIndex = 0;
        foreach ($days as $day) {
            $teams = [];
            foreach ($indexes as $index) {
                $team = intval( $day[$index] );
                if ($team == 0) continue;
                $teams[$team] = $teams[$team] ?? [];
                $teams[$team][] = $index;
            }
            $history[++$historyIndex] = $teams;
        }
        return $history;
    }
}
