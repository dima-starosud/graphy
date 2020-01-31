<?php


namespace Staro\Graphy\Handlers;


use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Staro\Graphy\Logic\WorkersProvider;

final class WorkersHandler implements RequestHandlerInterface {
    /**
     * @var WorkersProvider
     */
    private WorkersProvider $workersProvider;

    public function __construct(WorkersProvider $workersProvider) {
        $this->workersProvider = $workersProvider;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $workers = array_values( $this->workersProvider->getWorkers() );
        return new JsonResponse( $workers );
    }
}
