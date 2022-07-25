<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class NonAsciiCharsUnitTestClass extends BaseRouterUnitTestClass
{

    /**
     * Some action
     *
     * @return int
     */
    public function actionRoute(): int
    {
        return 1;
    }

    /**
     * Testing callig non-ASCII char route
     */
    public function testCallNonAsciiCharRoute(): void
    {
        // setup
        $router = $this->getRouter();
        RouterUnitTestUtils::setRequestMethod('GET');

        // test body
        $router->addGetRoute('кириллический-урл', $this, 'actionRoute');

        // assertions
        $this->assertEquals(1, $router->callRoute(urlencode('кириллический-урл')));
    }

    /**
     * Testing method
     */
    public function testNonAsciiParams(): void
    {
        // setup
        $router = $this->getRouter();
        RouterUnitTestUtils::setRequestMethod('GET');

        // test body
        $router->addRoute(
            'кириллический-урл/[s:non-ascii-param]',
            function (string $route, array $params): string {
                return $params['non-ascii-param'];
            },
            'GET');

        // assertions
        $this->assertEquals('ни разу не ASCII - 日本語', $router->callRoute(urlencode('кириллический-урл/ни разу не ASCII - 日本語')));
    }
}
