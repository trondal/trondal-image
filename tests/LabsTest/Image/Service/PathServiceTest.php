<?php

namespace LabsTest\Image\Service;

use Labs\Image\Service\PathService;

class PathServiceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var PathService
     */
    protected $pathService;

    protected $emptyPath;

    public function setUp() {
        $this->pathService = new PathService();
        $this->pathService->setConfiguration(include '../config/module.config.php');
        $this->emptyPath = __DIR__ . '/../assets/empty';
    }

    public function tearDown() {
        if (file_exists($this->emptyPath)) {
            if (file_exists($this->emptyPath . '/sub')) {
                rmdir($this->emptyPath . '/sub');
            }
            rmdir($this->emptyPath);
        }
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getContentPath() {
        $this->pathService->setConfiguration(array(
            'image' => array(
                'content_directory' => __DIR__
            ))
        );
        $this->pathService->getContentPath();
        $this->assertEquals(__DIR__, $this->pathService->getContentPath());
    }

    public function filePathProvider() {
        return array(
            array('abcdefghijklmn.gif', 0, __DIR__ . '/abcdefghijklmn.gif'),
            array('abcdefghijklmn.gif', 1, __DIR__ . '/ab/abcdefghijklmn.gif'),
            array('abcdefghijklmn.gif', 2, __DIR__ . '/ab/cd/abcdefghijklmn.gif'),
            array('abcdefghijklmn.gif', 3, __DIR__ . '/ab/cd/ef/abcdefghijklmn.gif'),
        );
    }

    /**
     * @test
     * @dataProvider filePathProvider
     */
    public function getOriginalPathFilename($fileName, $depth, $expected) {
        $this->pathService->setConfiguration(array(
            'image' => array(
                'content_directory' => __DIR__,
                'tree' => array(
                    'directory_depth' => $depth
                )
            ))
        );
        $this->assertEquals($expected, $this->pathService->getOriginalPathFilename($fileName));
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage filename cannot contain path
     */
    public function getFilenameWithPathThrowsException() {
        $this->pathService->getOriginalPathFilename(__DIR__ . '/abcdefghijklmn.gif');
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage filename must have an extension
     */
    public function getFilenameWithNoExtensionThrowsException() {
        $this->pathService->getOriginalPathFilename('abcdefghijklmn');
    }

    public function pathProvider() {
        return array(
            array('abcdefghijklmn.gif', 0, 'images/'),
            array('abcdefghijklmn.gif', 1, 'images/ab/'),
            array('abcdefghijklmn.gif', 2, 'images/ab/cd/'),
            array('abcdefghijklmn.gif', 3, 'images/ab/cd/ef/'),
        );
    }

    /**
     * @test
     * @dataProvider pathProvider
     */
    public function getDept($imageName, $depth, $expected) {
        $this->pathService->setConfiguration(
                array('image' => array(
                        'content_directory' => 'images',
                        'tree' => array(
                            'directory_depth' => $depth
                        )
                    ))
        );
        $actual = $this->pathService->getOriginalPath($imageName);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function tooLowDepthThrowsException() {
        $this->pathService->setConfiguration(
                array('image' => array('tree' => array('directory_depth' => -1)))
        );
        $this->pathService->getDepth();
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function tooHighDepthThrowsException() {
        $this->pathService->setConfiguration(
                array('image' => array('tree' => array('directory_depth' => 4)))
        );
        $this->pathService->getDepth();
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function stringDepthThrowsException() {
        $this->pathService->setConfiguration(
                array('image' => array('tree' => array('directory_depth' => '1')))
        );
        $this->pathService->getDepth();
    }

    /**
     * @test
     */
    public function isDirEmpty() {
        mkdir($this->emptyPath, 0777);

        // assert empty dir is reported as empty
        $this->assertTrue($this->pathService->isDirEmpty($this->emptyPath));

        // create sub dir im emptyPath
        mkdir($this->emptyPath. '/sub', 0777);

        // empty dir is now not empty
        $this->assertFalse($this->pathService->isDirEmpty($this->emptyPath));

        // but the sub dir is
        $this->assertTrue($this->pathService->isDirEmpty($this->emptyPath .'/sub'));
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage filename cannot contain path
     */
    public function getOriginalPathWithPathParameterThrowsException() {
        $this->pathService->getOriginalPath($this->emptyPath);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage filename cannot contain path
     */
    public function getEditPathWithPathParameterThrowsException() {
        $this->pathService->getEditPath($this->emptyPath);
    }

    public function editPathProvider() {
        return array(
            array('images/', 0, false),
            array('images/abcdefghijklmn/', 0, true),
            array('images/ab/', 1, false),
            array('images/ab/abcdefghijklmn/', 1, true)
        );
    }

    /**
     * @test
     * @dataProvider editPathProvider
     */
    public function getEditPath($expected, $depth, $storeResized) {
        $this->pathService->setConfiguration(
                array('image' => array(
                    'content_directory' => 'images',
                    'tree' => array(
                        'directory_depth' => $depth,
                        'store_resized' => $storeResized)
               ))
        );
        $path = $this->pathService->getEditPath('abcdefghijklmn.gif');

        $this->assertEquals($expected, $path);
    }

}