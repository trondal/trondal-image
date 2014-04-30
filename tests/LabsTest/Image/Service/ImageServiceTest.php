<?php

namespace Labs\ImageTest\Service;

use Labs\Image\Service\ImageService;
use Labs\Image\Service\PathService;

class ImageServiceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ImageTreeService
     */
    protected $imageService;

    public function setUp() {
        $pathService = new PathService();
        $pathService->setConfiguration(include '../config/module.config.php');
        $this->imageService = new ImageService();
        $this->imageService->setPathService($pathService);
    }

    public function fileProvider() {
        return array(
            array(realpath('LabsTest/Image/assets/animated.gif'), true, false),
            array(realpath('LabsTest/Image/assets/world.gif'), false, false),
            array(realpath('LabsTest/Image/assets/miserable.jpg'), false, false),
            array(realpath('LabsTest/Image/assets/transparent.png'), false, true),
            array(realpath('LabsTest/Image/assets/semitransparent.png'), false, true),
            array(realpath('LabsTest/Image/assets/opaque.png'), false, false),
        );
    }

    /**
     * @test
     * @dataProvider fileProvider
     */
    public function canDetectAnimatedGif($path, $isAnimated, $isTransparent) {
        $value = $this->imageService->isAnimatedGif($path);
        $this->assertEquals($isAnimated, $value);
    }

    /**
     * @test
     * @dataProvider fileProvider
     */
    public function canDetectTransparent($path, $isAnimated, $hasTransparency) {
        $value = $this->imageService->hasTransparency($path);
        $this->assertEquals($hasTransparency, $value);
    }

}