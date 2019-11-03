<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Middlewares\Utils\Factory\DiactorosFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Staro\Graphy\Handlers\UploadHandler;
use Staro\Graphy\Logic\FileCache;
use Staro\Graphy\Handlers\GenerateHandler;
use Staro\Graphy\Handlers\MainPageHandler;
use Staro\Graphy\Middlewares\StupidErrorHandler;
use Tuupola\Middleware\HttpBasicAuthentication;
use Middlewares\Utils\Dispatcher;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;
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
    ->addDefinitions( [
        ResponseFactoryInterface::class => create( DiactorosFactory::class ),
        FileCache::class                => function () {
            return new FileCache( DIR_NAME . '/private/cache' );
        },
    ] )
    ->build();

$errorHandler = new StupidErrorHandler();

$authHandler = new HttpBasicAuthentication( require DIR_NAME . '/private/authconfig.php' );

$routes = simpleDispatcher( function (RouteCollector $r) {
    $r->get( '/', MainPageHandler::class );
    $r->post( '/generate', GenerateHandler::class );
    $r->post( '/upload', UploadHandler::class );
} );

$dispatcher = new Dispatcher( [
    $errorHandler,
    $authHandler,
    new FastRoute( $routes ),
    new RequestHandler( $container ),
] );

$request = ServerRequestFactory::fromGlobals();
$response = $dispatcher->dispatch( $request );
$emitter = new SapiEmitter();
return $emitter->emit( $response );
