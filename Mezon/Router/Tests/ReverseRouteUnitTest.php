<?php
namespace Mezon\Router\Tests;

use PHPUnit\Framework\TestCase;

class ReverseRouteUnitTest extends TestCase
{

    /**
     * Data provider for the test testReverseRouteByName
     *
     * @return array test data
     */
    public function reverseRouteByNameDataProvider(): array
    {
        return [
            [
                'named-route-url',
                [],
                'named-route-url'
            ],
            [
                'route-with-params/[i:id]',
                [
                    'id' => 123
                ],
                'route-with-params/123',
            ],
            [
                'route-with-foo/[i:id]',
                [
                    'foo' => 123
                ],
                'route-with-foo/[i:id]',
            ],
            [
                'route-no-params/[i:id]',
                [],
                'route-no-params/[i:id]',
            ]
        ];
    }

    /**
     * Testing reverse route by it's name
     *
     * @param string $route
     *            route
     * @param array $parameters
     *            parameters to be substituted
     * @param string $extectedResult
     *            quite obviuous to describe it here )
     * @dataProvider reverseRouteByNameDataProvider
     */
    public function testReverseRouteByName(string $route, array $parameters, string $extectedResult): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute($route, function () {
            return 'named route result';
        }, 'GET', 'named-route');

        // test body
        $url = $router->reverse('named-route', $parameters);

        // assertions
        $this->assertEquals($extectedResult, $url);
    }

    /**
     * Trying to fetch unexisting route by name
     */
    public function testFetchUnexistingRouteByName(): void
    {
        // setup
        $router = new \Mezon\Router\Router();

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->getRouteByName('unexisting-name');
    }
}
