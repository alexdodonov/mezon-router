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
 * Testing getting non-ASCII char route
 */
    // public function testGetNonAsciiCharRoute(): void
    // {
    // setup
    // test body
    // assertions
    // }
}
