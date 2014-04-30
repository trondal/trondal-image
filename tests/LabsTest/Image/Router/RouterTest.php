<?php

namespace ImageTest;

use Zend\Http\Request;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class RouterTest extends AbstractHttpControllerTestCase {

    /**
     * @var TreeRouteStack
     */
    protected $router;

    public function setUp() {
        $this->router = new TreeRouteStack();
        $config = include '../config/module.config.php';
        $this->router->addRoutes($config['router']['routes']);
    }

    public function routeProvider() {
        return array(
            array('http://ss.dd.tt/iview/987654321.gif', 'Labs\Image\Controller\Index', 'view', 'image/iview', array('id' => '987654321.gif')),
            array('http://ss.dd.tt/iupload', 'Labs\Image\Controller\Index', 'upload', 'image/iupload', array()),
            array('http://ss.dd.tt/idownload/abcdefghij.jpg', 'Labs\Image\Controller\Index', 'download', 'image/idownload', array('id' => 'abcdefghij.jpg')),
            array('http://ss.dd.tt/idelete/abcdefghij.jpg', 'Labs\Image\Controller\Index', 'delete', 'image/idelete', array('id' => 'abcdefghij.jpg')),

            // Test that url with only two domains also can run
            array('http://ss.tt/iview/987654321.gif', 'Labs\Image\Controller\Index', 'view', 'image/iview', array('id' => '987654321.gif')),

            // Test that url with 5 domains also can run
            array('http://ss.tt.ee.ff.gg/iview/987654321.gif', 'Labs\Image\Controller\Index', 'view', 'image/iview', array('id' => '987654321.gif')),

            // Test that https works
            array('https://ss.tt/iview/987654321.gif', 'Labs\Image\Controller\Index', 'view', 'image/iview', array('id' => '987654321.gif')),

        );
    }

    /**
     * @dataProvider routeProvider
     * @test
     */
    public function testIndexRoute($uri, $controller, $action, $routeName, $params) {
        $request = new Request();
        $request->setUri($uri);
        $match = $this->router->match($request);
        $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);
        $this->assertEquals($match->getParam('controller'), $controller);
        $this->assertEquals($match->getParam('action'), $action);
        $this->assertEquals($match->getMatchedRouteName(), $routeName);

        foreach ($params as $key => $value) {
            $routeParam = $match->getParam($key);
            $this->assertEquals($value, $routeParam);
        }
    }

}