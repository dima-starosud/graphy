<?php


namespace Staro\Graphy\Handlers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Staro\Graphy\Logic\GenerationLogic;
use Staro\Graphy\Logic\HistoryProvider;
use Staro\Graphy\Logic\PairsTracker;
use Staro\Graphy\Logic\WorkersProvider;
use Laminas\Diactoros\Response\JsonResponse;

final class GenerateHandler implements RequestHandlerInterface {
    /**
     * @var GenerationLogic
     */
    private $generationLogic;
    /**
     * @var HistoryProvider
     */
    private $historyRepository;
    /**
     * @var WorkersProvider
     */
    private $workersProvider;

    function __construct(
        GenerationLogic $generationLogic,
        HistoryProvider $historyProvider,
        WorkersProvider $workersProvider
    ) {
        $this->generationLogic = $generationLogic;
        $this->historyRepository = $historyProvider;
        $this->workersProvider = $workersProvider;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getParsedBody();

        $date = date_parse( $params['date'] );
        unset( $params['date'] );
        $day = $date['day'];

        $teamSizes = [];
        if ( $params['team3count'] === 'on' ) {
            $teamSizes[] = 3;
            unset( $params['team3count'] );
        }
        if ( $params['team2count'] === 'on' ) {
            $teamSizes[] = 2;
            unset( $params['team2count'] );
        }

        $ids = array_keys( $params );
        $history = $this->historyRepository->getHistory( $day );
        $result = $this->generationLogic->generate( $ids, $history, $teamSizes );

        $pairsTracker = new PairsTracker( $history );
        $result = array_map( function ($team) use ($pairsTracker) {
            return [
                'ids'   => $team,
                'count' => array_sum( $pairsTracker->counts( $team ) ),
            ];
        }, $result );
        usort( $result, function ($a, $b) {
            $a = $a['count'];
            $b = $b['count'];
            if ( $a == $b ) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        } );

        return new JsonResponse( $result );
    }
}
