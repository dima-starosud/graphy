<?php

namespace Staro\Graphy\Logic;

final class Utils {
    static function combinations($items, int $combination_count) {
        if ( $combination_count < 1 ) {
            yield [];
            return;
        }
        foreach (static::combinations( $items, $combination_count - 1 ) as $init) {
            $end = end( $init ) ?? -INF;
            foreach ($items as $item) {
                if ( $end < $item ) {
                    yield array_merge( $init, [$item] );
                }
            }
        }
    }

    static function pairs($items) {
        return static::combinations( $items, 2 );
    }
}
