<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class ReverseRouteTestClass extends BaseRouterUnitTestClass
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
                    'id' => '123'
                ],
                'route-with-params/123'
            ],
            [
                'route-with-foo/[i:id]',
                [
                    'foo' => '123'
                ],
                'route-with-foo/[i:id]'
            ],
            [
                'route-no-params/[i:id]',
                [],
                'route-no-params/[i:id]'
            ]
        ];
    }

    /**
     * Testing reverse route by it's name
     *
     * @param string $route
     *            route
     * @param string[] $parameters
     *            parameters to be substituted
     * @param string $extectedResult
     *            quite obviuous to describe it here )
     * @dataProvider reverseRouteByNameDataProvider
     */
    public function testReverseRouteByName(string $route, array $parameters, string $extectedResult): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute($route, function () {
            return 'named route result';
        }, 'GET', 'named-route');

        // test body
        /** @var string $url */
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
        $router = $this->getRouter();

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->getRouteByName('unexisting-name');
    }
}
