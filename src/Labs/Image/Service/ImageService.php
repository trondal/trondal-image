<?php

namespace Labs\Image\Service;

class ImageService {

    protected $strategy;

    public function __construct(\Labs\Image\Strategy\AbstractStrategy $strategy) {
        $this->strategy = $strategy;
    }

    public function get($imageName) {
        return $this->strategy->get($imageName);
    }

    public function getVariant($imageName, $width) {
        return $this->strategy->getVariant($imageName, $width);
    }

    public function delete($imageName) {
        return $this->strategy->delete($imageName);
    }

    public function save($imageName) {
        return $this->strategy->save($imageName);
    }

}