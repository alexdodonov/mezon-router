<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class StaticMethodsCallsTestClass extends BaseRouterUnitTestClass
{

    /**
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp(): void
    {
        // TODO в трейт
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * Static method for calling
     *
     * @return string
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function staticMethod(): string
    {
        return 'static method was called';
    }

    /**
     * Testing data provider
     *
     * @return array testing data
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function staticMethodCallDataProvider(): array
    {
        return [
            // #0, the first case
            [
                [
                    StaticMethodsCallsTestClass::class,
                    'staticMethod'
                ]
            ],
            // #1, the second case
            [
                '\Mezon\Router\Tests\Base\StaticMethodsCallsTestClass::staticMethod'
            ]
        ];
    }

    /**
     * Testing static method calls
     *
     * @param array{0: object|string, 1: string}|callable|string $callback
     *            callback
     * @dataProvider staticMethodCallDataProvider
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testStaticMethodCalls($callback): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('static', $callback, 'GET');

        // test body and assertions
        $this->assertEquals('static method was called', $router->callRoute('static'));
    }
}
