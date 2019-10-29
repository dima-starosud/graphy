<?php


namespace Staro\Graphy;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Staro\Graphy\Logic\FileHistoryRepository;
use Staro\Graphy\Logic\GenerationLogic;
use Staro\Graphy\Logic\StaticWorkersProvider;
use Zend\Diactoros\Response\HtmlResponse;

final class GenerateHandler implements RequestHandlerInterface {
    /**
     * @var GenerationLogic
     */
    private $generationLogic;
    /**
     * @var FileHistoryRepository
     */
    private $historyRepository;
    /**
     * @var StaticWorkersProvider
     */
    private $workersProvider;
    /**
     * @var DayEditorHtml
     */
    private $dayEditorHtml;

    function __construct(
        GenerationLogic $generationLogic,
        FileHistoryRepository $historyProvider,
        StaticWorkersProvider $workersProvider,
        DayEditorHtml $dayEditorHtml
    ) {
        $this->generationLogic = $generationLogic;
        $this->historyRepository = $historyProvider;
        $this->workersProvider = $workersProvider;
        $this->dayEditorHtml = $dayEditorHtml;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $params = $request->getParsedBody();
        $date = date_parse( $params['date'] );
        unset( $params['date'] );
        $month = $date['month'];
        $day = $date['day'];
        $team3count = intval( $params['team3count'] ?? 0 );
        unset( $params['team3count'] );
        $team2count = intval( $params['team2count'] ?? 0 );
        unset( $params['team2count'] );
        $ids = array_keys( $params );
        $history = $this->historyRepository->getHistory( $month, $day );
        $result = $this->generationLogic->generate( $team2count, $team3count, $ids, $history );
        $this->historyRepository->storeHistory( $month, $day, $result );
        ob_start();
        $this->dayEditorHtml->echoHtml( $params['date'], $result );
        return new HtmlResponse( ob_get_clean() );
    }
}
