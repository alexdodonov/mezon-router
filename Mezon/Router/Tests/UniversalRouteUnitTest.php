<?php
namespace Mezon\Router\Tests;

class UniversalRouteUnitTest extends \PHPUnit\Framework\TestCase
{

    const HELLO_WORLD = 'Hello world!';

    const HELLO_STATIC_WORLD = 'Hello static world!';

    const HELLO_STATIC_WORLD_METHOD = '\Mezon\Router\Tests\StaticRoutesUnitTest::staticHelloWorldOutput';

    use Utils;

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return UniversalRouteUnitTest::HELLO_WORLD;
    }

    /**
     * Function simply returns string.
     */
    static public function staticHelloWorldOutput(): string
    {
        return UniversalRouteUnitTest::HELLO_STATIC_WORLD;
    }

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
     * Method returns data sets
     *
     * @return array testing data
     */
    public function universalRouteDataProvider(): array
    {
        $setup = function () {
            return [
                [
                    '/hello/[s:name]/',
                    function () {
                        return 'hello';
                    }
                ],
                [
                    '*',
                    function () {
                        return 'universal';
                    }
                ],
                [
                    '/bye/[s:name]/',
                    function () {
                        return 'bye';
                    }
                ]
            ];
        };

        return [
            [
                $setup(),
                '/hello/joe/',
                'hello'
            ],
            [
                $setup(),
                '/bye/joe/',
                'bye'
            ],
            [
                $setup(),
                '/unexisting/',
                'universal'
            ]
        ];
    }

    /**
     * Testing one processor for all routes
     *
     * @param array $routes
     *            list of routes
     * @param string $uri
     *            uri to be requested
     * @param string $result
     *            expected result
     * @dataProvider universalRouteDataProvider
     */
    public function testUniversalRoute(array $routes, string $uri, string $result): void
    {
        // setup
        $router = new \Mezon\Router\Router();

        foreach ($routes as $route) {
            $router->addRoute($route[0], $route[1]);
        }

        // test body
        $content = $router->callRoute($uri);

        // assertions
        $this->assertEquals($result, $content);
    }
}
