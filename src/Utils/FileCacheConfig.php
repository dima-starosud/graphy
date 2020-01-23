<?php


namespace Staro\Graphy\Utils;


final class FileCacheConfig {
    private $dir;

    function __construct(string $dir) {
        $this->dir = $dir;
    }

    function getDir(): string {
        return $this->dir;
    }
}
