<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class BoolResultTestClass extends BaseRouterUnitTestClass
{

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
     * Data provider for the test testBoolResult
     *
     * @return array testing data
     */
    public function boolResultDataProvider(): array
    {
        return [
            [
                function (): bool {
                    return true;
                },
                true
            ],
            [
                function (): bool {
                    return false;
                },
                false
            ]
        ];
    }

    /**
     * Testing bool return values for route handlers, checking that false return value works correctly
     *
     * @param callable $handler
     *            route handler
     * @param bool $expected
     *            expected result
     * @dataProvider boolResultDataProvider
     */
    public function testBoolResult(callable $handler, bool $expected): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/catalog/[a:cat_id]/', $handler);

        // test body
        /** @var bool $result */
        $result = $router->callRoute('/catalog/foo/');

        // assertions
        $this->assertEquals($expected, $result);
    }
}
