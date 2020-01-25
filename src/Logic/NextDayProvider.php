<?php


namespace Staro\Graphy\Logic;


final class NextDayProvider {
    /**
     * @var HistoryProvider
     */
    private $historyProvider;

    function __construct(HistoryProvider $historyProvider) {
        $this->historyProvider = $historyProvider;
    }

    function getNextDay(): string {
        $data = $this->historyProvider->getHistory();

        $data = array_filter( $data, function ($teams) {
            return sizeof( $teams ) >= 6;
        } );

        $data = array_keys( $data );

        $plus = 1 + (
            empty( $data )
                ? 0
                : max( 0, max( $data ) - (int)date( 'd' ) )
            );
        return date( 'Y-m-d', strtotime( "+ {$plus} day" ) );
    }
}
