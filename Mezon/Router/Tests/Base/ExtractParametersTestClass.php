<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class ExtractParametersTestClass extends BaseRouterUnitTestClass
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
     * Testing hasParam method
     */
    public function testValidatingParameter(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:foo]/', function (): void {
            // do nothing
        });

        $router->callRoute('/catalog/1/');

        // test body and assertions
        $this->assertTrue($router->hasParam('foo'));
        $this->assertFalse($router->hasParam('unexisting'));
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameter(): void
    {
        $router = $this->getRouter();
        $router->addRoute('/catalog/[a:cat_id]/', /**
         *
         * @param string $route
         *            route
         * @param string[] $parameters
         */
        function (string $route, array $parameters): string {
            return $parameters['cat_id'];
        });

        /** @var string $result */
        $result = $router->callRoute('/catalog/foo/');

        $this->assertEquals('foo', $result);
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameters(): void
    {
        $router = $this->getRouter();
        $router->addRoute('/catalog/[a:cat_id]/[i:item_id]', /**
         *
         * @param string $route
         *            route
         * @param string[] $parameters
         */
        function (string $route, array $parameters): string {
            return $parameters['cat_id'] . $parameters['item_id'];
        });

        /** @var string $result */
        $result = $router->callRoute('catalog/foo/1024');

        $this->assertEquals('foo1024', $result);
    }

    /**
     * Testing multyple routes
     */
    public function testMultyple(): void
    {
        // setup
        $router = $this->getRouter();
        for ($i = 0; $i < 15; $i ++) {
            $router->addRoute('/multiple/' . $i . '/[i:id]', function () {
                return 'done!';
            });
        }

        // test body
        /** @var string $result */
        $result = $router->callRoute('/multiple/' . rand(0, 14) . '/12345');

        // assertions
        $this->assertEquals('done!', $result);
        $this->assertEquals('12345', $router->getParam('id'));
    }
}
