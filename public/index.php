<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Factory\DiactorosFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Staro\Graphy\Handlers\SyncHandler;
use Staro\Graphy\Handlers\UploadHandler;
use Staro\Graphy\Handlers\GenerateHandler;
use Staro\Graphy\Handlers\MainPageHandler;
use Staro\Graphy\Middlewares\StupidErrorHandler;
use Tuupola\Middleware\HttpBasicAuthentication;
use Middlewares\Utils\Dispatcher;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\Diactoros\ServerRequestFactory;
use function DI\create;
use function FastRoute\simpleDispatcher;

define( 'DIR_NAME', dirname( __DIR__ ) );

require_once DIR_NAME . '/vendor/autoload.php';

set_error_handler( function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }
    throw new ErrorException( $message, 0, $severity, $file, $line );
} );

/** @noinspection PhpUnhandledExceptionInspection */
$container = (new ContainerBuilder())
    ->useAnnotations( false )
    ->addDefinitions( array_merge(
        require DIR_NAME . '/private/config.php',
        [ResponseFactoryInterface::class => create( DiactorosFactory::class )]
    ) )
    ->build();

$routes = simpleDispatcher( function (RouteCollector $r) {
    $r->get( '/', MainPageHandler::class );
    $r->post( '/generate', GenerateHandler::class );
    $r->post( '/upload', UploadHandler::class );
    $r->post( '/sync', SyncHandler::class );
} );

/** @noinspection PhpUnhandledExceptionInspection */
$dispatcher = new Dispatcher( [
    new StupidErrorHandler(),
    $container->get( HttpBasicAuthentication::class ),
    new FastRoute( $routes ),
    new RequestHandler( $container ),
] );

$request = ServerRequestFactory::fromGlobals();

@include DIR_NAME . '/private/hook.php';

$response = $dispatcher->dispatch( $request );
$emitter = new SapiEmitter();
return $emitter->emit( $response );
