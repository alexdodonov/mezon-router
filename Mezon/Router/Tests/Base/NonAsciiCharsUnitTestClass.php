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
     * Some action.
     *
     * @return int Result.
     *        
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function actionRoute(): int
    {
        return 1;
    }

    /**
     * Testing callig non-ASCII char route.
     *
     * @psalm-suppress PossiblyUnusedMethod
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
     * Testing callig non-ASCII char route.
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testNonAsciiParams(): void
    {
        // setup
        $router = $this->getRouter();
        RouterUnitTestUtils::setRequestMethod('GET');

        // test body
        $router->addRoute('кириллический-урл/[s:non-ascii-param]', /**
         *
         * @psalm-param array{non-ascii-param: string} $parameters
         */
        function (string $route, array $parameters): string {
            return $parameters['non-ascii-param'];
        }, 'GET');

        // assertions
        $this->assertEquals('ни разу не ASCII - 日本語', $router->callRoute(urlencode('кириллический-урл/ни разу не ASCII - 日本語')));
    }
}
