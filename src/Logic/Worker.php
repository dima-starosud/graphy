<?php


namespace Staro\Graphy\Logic;


use \JsonSerializable;

final class Worker implements JsonSerializable {
    const CARRIER = "CARRIER";
    const DRIVER = "DRIVER";

    private $index;
    private $name;
    private $post;

    function __construct($index, $name, $post) {
        $this->index = $index;
        $this->name = $name;
        $this->post = $post;
    }

    static function __set_state($an_array) {
        return new Worker( $an_array['index'], $an_array['name'], $an_array['post'] );
    }

    public function getIndex() {
        return $this->index;
    }

    public function getName() {
        return $this->name;
    }

    public function getPost() {
        return $this->post;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return [
            'index' => $this->getIndex(),
            'name'  => $this->getName(),
            'post'  => $this->getPost(),
        ];
    }
}
