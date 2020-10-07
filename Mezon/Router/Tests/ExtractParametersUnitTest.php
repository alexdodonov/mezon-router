<?php
namespace Mezon\Router\Tests;

use Mezon\Router\Router;

class ExtractParametersUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Default setup
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * Testing hasParam method
     */
    public function testValidatingParameter(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function () {
            // do nothing
        });

        $router->callRoute('/catalog/1/');

        // test body and assertions
        $this->assertTrue($router->hasParam('foo'));
        $this->assertFalse($router->hasParam('unexisting'));
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameter(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[a:cat_id]/', function ($route, $parameters) {
            return $parameters['cat_id'];
        });

        $result = $router->callRoute('/catalog/foo/');

        $this->assertEquals($result, 'foo', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameters(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute(
            '/catalog/[a:cat_id]/[i:item_id]',
            function ($route, $parameters) {
                return $parameters['cat_id'] . $parameters['item_id'];
            });

        $result = $router->callRoute('catalog/foo/1024');

        $this->assertEquals($result, 'foo1024', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidRouteParameter(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/all/', function ($route) {
            return $route;
        });
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        });

        // first reading
        $result = $router->callRoute('/catalog/all/');
        $this->assertEquals($result, 'catalog/all');

        // second route
        $result = $router->callRoute('/catalog/1024/');
        $this->assertEquals($result, 'catalog/1024');

        // reading regexp from cache in the _getRouteMatcherRegExPattern method
        $router->warmCache();
        $result = $router->callRoute('/catalog/1024/');
        $this->assertEquals($result, 'catalog/1024');
    }

    /**
     * Testing multyple routes
     */
    public function testMultyple(): void
    {
        // setup
        $router = new Router();
        for ($i = 0; $i < 15; $i ++) {
            $router->addRoute('/multiple/' . $i . '/[i:id]', function () {
                return 'done!';
            });
        }

        // test body
        $result = $router->callRoute('/multiple/' . rand(0, 14) . '/12345');

        // assertions
        $this->assertEquals('done!', $result);
        $this->assertEquals('12345', $router->getParam('id'));
    }
}
