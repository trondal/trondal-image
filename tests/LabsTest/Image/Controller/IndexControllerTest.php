<?php

use Labs\Image\Controller\IndexController;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase {

    /**
     * @var IndexController
     */
    protected $controller;
    protected $traceError = true;

    public function setUp() {
        $this->setApplicationConfig(
                include __DIR__ .'/../../../application.config.php'
        );
        parent::setUp();
    }

    /**
     * @test
     */
    public function viewAction() {
        $this->dispatch('http://subdomain.domain.tld/iview/abcdefghijklmno.gif');
        $this->assertControllerName('Labs\Image\Controller\Index');
        $this->assertModuleName('Labs');
        $this->assertActionName('view');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function viewWithWidthAction() {
        $this->dispatch('http://subdomain.domain.tld/iview/abcdefghijklmno.gif?width=100');
        $this->assertControllerName('Labs\Image\Controller\Index');
        $this->assertModuleName('Labs');
        $this->assertActionName('view');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function viewWithCacheAction() {
        $this->dispatch('http://subdomain.domain.tld/iview/abcdefghijklmno.gif?cache=30');
        $this->assertControllerName('Labs\Image\Controller\Index');
        $this->assertModuleName('Labs');
        $this->assertActionName('view');
        $this->assertResponseStatusCode(200);
        $header = $this->getResponseHeader('X-Cache-Control');
        $this->assertEquals('X-Cache-Control', $header->getFieldName());
        $this->assertEquals('max-age= 30, private', $header->getFieldValue());
    }

    /**
     * @test
     */
    public function downloadAction() {
        $this->dispatch('http://subdomain.domain.tld/idownload/abcdefghijklmno.gif');
        $this->assertControllerName('Labs\Image\Controller\Index');
        $this->assertModuleName('Labs');
        $this->assertActionName('download');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function downloadActionReturn404ifImageDoesNotExists() {
        $this->dispatch('http://subdomain.domain.tld/idownload/imagedoesnotexists.gif');
        $this->assertControllerName('Labs\Image\Controller\Index');
        $this->assertModuleName('Labs');
        $this->assertActionName('download');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     */
    public function viewActionWithNotExistingImageShouldGiveGeneric404Response() {
        $this->dispatch('http://subdomain.domain.tld/iview/imagedoesnotexists.gif');
        $this->assertControllerName('Labs\Image\Controller\Index');
        $this->assertModuleName('Labs');
        $this->assertActionName('view');
        $this->assertResponseStatusCode(404);
        $this->assertNotRedirect();
    }

}