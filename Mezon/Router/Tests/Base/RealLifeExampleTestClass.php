<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class RealLifeExampleTestClass extends BaseRouterUnitTestClass
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
     * Testing real life example
     */
    public function testRealLifeExample(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/user/[s:login]/custom-field/[s:name]', function () {
            return 'get-custom-field';
        });
        $router->addRoute('/user/[s:login]/custom-field/[s:name]/add', function () {
            return 'add-custom-field';
        });
        $router->addRoute('/user/[s:login]/custom-field/[s:name]/delete', function () {
            return 'delete-custom-field';
        });
        $router->addRoute('/restore-password/[s:token]', function () {
            return 'restore-password';
        });
        $router->addRoute('/reset-password/[s:token]', function () {
            return 'reset-password';
        });
        $router->addRoute('/user/[s:login]/delete', function () {
            return 'user-delete';
        });

        // test body
        /** @var string $result */
        $result = $router->callRoute('/user/index@localhost/custom-field/name/add');

        // assertions
        $this->assertEquals('add-custom-field', $result);
    }
}
