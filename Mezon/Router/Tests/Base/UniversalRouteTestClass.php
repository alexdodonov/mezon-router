<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use Mezon\Router\Tests\Utils;
use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class UniversalRouteTestClass extends StaticRoutesTestClass
{

    const HELLO_WORLD = 'Hello world!';

    const HELLO_STATIC_WORLD = 'Hello static world!';

    const HELLO_STATIC_WORLD_METHOD = '\Mezon\Router\Tests\StaticRoutesUnitTest::staticHelloWorldOutput';

    use Utils;

    /**
     * Function simply returns string
     */
    public function helloWorldOutput(): string
    {
        return UniversalRouteTestClass::HELLO_WORLD;
    }

    /**
     * Function simply returns string
     */
    static public function staticHelloWorldOutput(): string
    {
        return UniversalRouteTestClass::HELLO_STATIC_WORLD;
    }

    /**
     * Default setup
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
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
        $setup = function (): array {
            return [
                [
                    '/hello/[s:name]/',
                    function (): string {
                        return 'hello';
                    }
                ],
                [
                    '*',
                    function (): string {
                        return 'universal';
                    }
                ],
                [
                    '/bye/[s:name]/',
                    function (): string {
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
     * @param
     *            list<array{0: string, 1: callable}> $routes
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
        $router = $this->getRouter();

        /** @var array{0: string, 1: callable} $route */
        foreach ($routes as $route) {
            $router->addRoute($route[0], $route[1]);
        }

        // test body
        $content = (string) $router->callRoute($uri);

        // assertions
        $this->assertEquals($result, $content);
    }
}
