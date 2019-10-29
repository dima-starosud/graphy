<?php


namespace Staro\Graphy;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Staro\Graphy\Logic\StaticWorkersProvider;
use Zend\Diactoros\Response\HtmlResponse;

final class IndexHandler implements RequestHandlerInterface {
    /**
     * @var StaticWorkersProvider
     */
    private $workersProvider;

    public function __construct(StaticWorkersProvider $workersProvider) {
        $this->workersProvider = $workersProvider;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        /** @noinspection PhpUnusedLocalVariableInspection
         *    it's used inside template
         */
        $workers = $this->workersProvider->getWorkers();
        ob_start();
        include 'templates/index.html.php';
        return new HtmlResponse( ob_get_clean() );
    }
}
