<?php


namespace Staro\Graphy\Utils;


final class GoogleSheetConfig {
    private $id;
    private $range;

    function __construct($config) {
        $this->id = $config['id'];
        $this->range = $config['range'];
    }

    function getId(): string {
        return $this->id;
    }

    function getRange(): string {
        return $this->range;
    }
}
