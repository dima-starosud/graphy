<?php


namespace Staro\Graphy\Handlers;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Staro\Graphy\Logic\Uploader;
use Laminas\Diactoros\Response\RedirectResponse;

final class UploadHandler implements RequestHandlerInterface {
    /**
     * @var Uploader
     */
    private $uploader;

    function __construct(Uploader $uploader) {
        $this->uploader = $uploader;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $this->uploader->upload( $request->getParsedBody()['text'] );
        return new RedirectResponse( '/' );
    }
}
