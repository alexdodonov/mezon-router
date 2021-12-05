<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use PHPUnit\Framework\TestCase;
use Mezon\Router\Router;
use Mezon\Router\Tests\Base\RouterUnitTestUtils;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
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
        RouterUnitTestUtils::setRequestMethod('GET');
        $router = new Router();
        $router->addRoute($route, function () {
            return 'route result';
        });

        // test body
        $callback = $router->getCallback($url);

        // assertions
        if (is_callable($callback)) {
            $this->assertEquals('route result', $callback());
        } else {
            $this->fail();
        }
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
