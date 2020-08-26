<?php
namespace Mezon\Router\Tests;

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
        return [
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
            ]
        ];
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
    public function testTypes($pattern, string $route, $expected, string $paramName = 'bar'): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        if (is_string($pattern)) {
            $router->addRoute($pattern, function () {
                // do nothing
            });
        } else {
            foreach ($pattern as $r) {
                $router->addRoute($r, function () {
                    // do nothing
                });
            }
        }
        $router->callRoute($route);

        // test body and assertions
        $this->assertEquals($expected, $router->getParam($paramName));
    }

    /**
     * Testing dynamic routes for DELETE requests.
     */
    public function testDeleteRequestForExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        }, 'DELETE');

        $result = $router->callRoute('/catalog/123/');

        $this->assertEquals($result, 'catalog/123');
    }

    /**
     * Testing dynamic routes for PUT requests.
     */
    public function testPutRequestForExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        }, 'PUT');

        $result = $router->callRoute('/catalog/1024/');

        $this->assertEquals($result, 'catalog/1024');
    }

    /**
     * Testing dynamic routes for POST requests.
     */
    public function testPostRequestForExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        }, 'POST');

        $result = $router->callRoute('/catalog/1024/');

        $this->assertEquals($result, 'catalog/1024');
    }

    /**
     * Testing valid data types behaviour.
     */
    public function testValidTypes(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]/item/[i:item_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/1024/item/2048/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "Illegal parameter type";

        $this->assertFalse(strpos($exception, $msg));
    }

    /**
     * Testing valid integer data types behaviour.
     */
    public function testValidIntegerParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "Illegal parameter type";

        $this->assertFalse(strpos($exception, $msg));
    }

    /**
     * Testing valid alnum data types behaviour.
     */
    public function testValidAlnumParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[a:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/foo/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "Illegal parameter type";

        $this->assertFalse(strpos($exception, $msg));
    }

    /**
     * Testing invalid integer data types behaviour.
     */
    public function testInValidIntegerParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/a1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route catalog/a1024";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }

    /**
     * Testing invalid alnum data types behaviour.
     */
    public function testInValidAlnumParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[a:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/~foo/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route catalog/~foo";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
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
}
