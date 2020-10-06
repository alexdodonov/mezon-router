<?php
namespace Mezon\Router\Tests;

use Mezon\Router\Router;
use Mezon\Router\Types\DateRouterType;

class DynamicRoutesUnitTest extends \PHPUnit\Framework\TestCase
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

    const TYPES_ROUTE_CATALOG_INT_BAR = '/catalog/[i:bar]/';

    const TYPES_ROUTE_CATALOG_FIX_POINT_BAR = '/catalog/[fp:bar]/';

    /**
     * Data provider for the testTypes
     *
     * @return array test data
     */
    public function typesDataProvider(): array
    {
        $data = [
            // #0
            [
                DynamicRoutesUnitTest::TYPES_ROUTE_CATALOG_INT_BAR,
                '/catalog/1/',
                1
            ],
            // #1
            [
                DynamicRoutesUnitTest::TYPES_ROUTE_CATALOG_INT_BAR,
                '/catalog/-1/',
                - 1
            ],
            // #2
            [
                DynamicRoutesUnitTest::TYPES_ROUTE_CATALOG_INT_BAR,
                '/catalog/+1/',
                1
            ],
            // #3
            [
                DynamicRoutesUnitTest::TYPES_ROUTE_CATALOG_FIX_POINT_BAR,
                '/catalog/1.1/',
                1.1
            ],
            // #4
            [
                DynamicRoutesUnitTest::TYPES_ROUTE_CATALOG_FIX_POINT_BAR,
                '/catalog/-1.1/',
                - 1.1
            ],
            // #5
            [
                DynamicRoutesUnitTest::TYPES_ROUTE_CATALOG_FIX_POINT_BAR,
                '/catalog/+1.1/',
                1.1
            ],
            // #6
            [
                '/[a:bar]/',
                '/.-@/',
                '.-@'
            ],
            // #7
            [
                '/[s:bar]/',
                '/, ;:/',
                ', ;:'
            ],
            // #8
            [
                [
                    '/[fp:number]/',
                    '/[s:bar]/'
                ],
                '/abc/',
                'abc'
            ],
            // #9
            [
                '/catalog/[il:bar]/',
                '/catalog/123,456,789/',
                '123,456,789'
            ],
            // #10
            [
                '/catalog/[s:bar]/',
                '/catalog/123&456/',
                '123&456'
            ],
            // #11, parameter name chars testing
            [
                '/[s:Aa_x-0]/',
                '/abc123/',
                'abc123',
                'Aa_x-0'
            ],
            // #12, date type testing 1
            [
                '/[date:dfield]/',
                '/2020-02-02/',
                '2020-02-02',
                'dfield'
            ],
            // #13, date type testing 2
            [
                '/posts-[date:dfield]/',
                '/posts-2020-02-02/',
                '2020-02-02',
                'dfield'
            ]
        ];

        $return = [];

        foreach (Router::getListOfSupportedRequestMethods() as $method) {
            $tmp = array_merge($data);

            foreach ($tmp as $item) {
                $item = array_merge([
                    $method
                ], $item);
                $return[] = $item;
            }
        }

        return $return;
    }

    /**
     * Testing router types
     *
     * @param mixed $pattern
     *            route pattern
     * @param string $route
     *            real route
     * @param mixed $expected
     *            expected value
     * @param string $paramName
     *            name of the validating parameter
     * @dataProvider typesDataProvider
     */
    public function testTypes(string $method, $pattern, string $route, $expected, string $paramName = 'bar'): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = $method;
        $router = new \Mezon\Router\Router();
        $router->addType('date', DateRouterType::class);
        if (is_string($pattern)) {
            $router->addRoute($pattern, function () {
                // do nothing
            }, $method);
        } else {
            foreach ($pattern as $r) {
                $router->addRoute($r, function () {
                    // do nothing
                }, $method);
            }
        }
        $router->callRoute($route);

        // test body and assertions
        $this->assertEquals($expected, $router->getParam($paramName));
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
        $result = $router->callRoute('/multiple/' . rand(0, 15) . '/12345');

        // assertions
        $this->assertEquals('done!', $result);
        $this->assertEquals('12345', $router->getParam('id'));
    }
}
