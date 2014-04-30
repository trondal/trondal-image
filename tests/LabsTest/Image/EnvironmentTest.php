<?php

namespace ImageTest;

class EnvironmentTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function testImageick() {
        $result = extension_loaded('imagick');
        $this->assertTrue($result);
    }
}