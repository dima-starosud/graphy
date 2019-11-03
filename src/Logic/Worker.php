<?php


namespace Staro\Graphy\Logic;


final class Worker {
    const CARRIER = 0;
    const DRIVER = 1;

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
}
