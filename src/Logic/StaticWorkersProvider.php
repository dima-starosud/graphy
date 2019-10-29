<?php


namespace Staro\Graphy\Logic;


final class StaticWorkersProvider {
    /**
     * @var array
     */
    private $workers;

    function __construct(array $workers) {
        $this->workers = $workers;
    }

    function getWorkers(): array {
        return $this->workers;
    }
}
