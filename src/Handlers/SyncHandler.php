<?php


namespace Staro\Graphy\Handlers;


use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Staro\Graphy\GoogleApi\GoogleServiceSheetsProvider;
use Staro\Graphy\Logic\Uploader;
use Staro\Graphy\Utils\GoogleSheetConfig;

final class SyncHandler implements RequestHandlerInterface {
    /**
     * @var Uploader
     */
    private $uploader;
    /**
     * @var GoogleServiceSheetsProvider
     */
    private $googleServiceSheetsProvider;
    /**
     * @var GoogleSheetConfig
     */
    private $config;

    function __construct(
        Uploader $uploader, GoogleServiceSheetsProvider $googleServiceSheetsProvider,
        GoogleSheetConfig $config
    ) {
        $this->uploader = $uploader;
        $this->googleServiceSheetsProvider = $googleServiceSheetsProvider;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface {
        $service = $this->googleServiceSheetsProvider->getService();

        $response = $service->spreadsheets_values->get(
            $this->config->getId(), $this->config->getRange(),
            ['majorDimension' => 'ROWS']
        );
        $table = $response->getValues();

        $this->uploader->upload( $table );
        return new RedirectResponse( '/' );
    }
}
