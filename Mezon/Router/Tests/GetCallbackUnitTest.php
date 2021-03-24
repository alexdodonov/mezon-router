<?php
namespace Mezon\Router\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Router\Router;

class GetCallbackUnitTest extends TestCase
{

    /**
     * Data provider for the
     *
     * @return array testing dataset
     */
    public function getCallbackDataProvider(): array
    {
        return [
            [
                'some-static-route',
                'some-static-route'
            ],
            [
                'some/non-static/route/[i:id]',
                'some/non-static/route/1'
            ],
            [
                '*',
                'unexisting-route'
            ]
        ];
    }

    /**
     * Testing getting callback
     *
     * @param string $route
     *            route
     * @param string $url
     *            concrete URL
     * @dataProvider getCallbackDataProvider
     */
    public function testGetCallback(string $route, string $url): void
    {
        // setup
        RouterUnitTest::setRequestMethod('GET');
        $router = new Router();
        $router->addRoute($route, function () {
            return 'route result';
        });

        // test body
        $callback = $router->getCallback($url);

        // assertions
        $this->assertEquals('route result', $callback());
    }

    /**
     * Testing case with unexisting callback
     */
    public function testGetCallbackWithUnexistingRoute(): void
    {
        // setup
        $router = new Router();
        $router->addRoute('existing-route', function () {
            return 'existing route result';
        });

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->getCallback('unexisting-route');
    }
}
