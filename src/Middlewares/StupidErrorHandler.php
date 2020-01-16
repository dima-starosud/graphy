<?php

namespace Staro\Graphy\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Laminas\Diactoros\Response\TextResponse;

final class StupidErrorHandler implements MiddlewareInterface {
    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        try {
            return $handler->handle( $request );
        } catch (Throwable $e) {
            return new TextResponse( (string)$e, 500 );
        }
    }
}
