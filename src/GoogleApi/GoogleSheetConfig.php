<?php


namespace Staro\Graphy\GoogleApi;


final class GoogleSheetConfig {
    private $id;
    private $range;

    function __construct(string $id, string $range) {
        $this->id = $id;
        $this->range = $range;
    }

    function getId(): string {
        return $this->id;
    }

    function getRange(): string {
        return $this->range;
    }
}
