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
        $text = $request->getParsedBody()['text'];

        $table = array_map( function ($line) {
            return explode( "\t", $line );
        }, explode( "\n", $text ) );

        $this->uploader->upload( $table );
        return new RedirectResponse( '/' );
    }
}
