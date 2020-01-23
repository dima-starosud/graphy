<?php


namespace Staro\Graphy\GoogleApi;


final class GoogleServiceSheetsProviderConfig {
    private $baseDir;

    function __construct(string $baseDir) {
        $this->baseDir = $baseDir;
    }

    function getBaseDir(): string {
        return $this->baseDir;
    }
}