<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class DynamicRoutesInvalidCasesTestClass extends BaseRouterUnitTestClass
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
     * Testing getParam for unexisting param.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testGettingUnexistingParameter(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:foo]/', function () {
            // do nothing
        });

        $router->callRoute('/catalog/1/');

        $this->expectException(\Exception::class);

        // test body and assertions
        $router->getParam('unexisting');
    }

    /**
     * Testing exception throwing for unexisting request method.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testExceptionForUnexistingRequestMethod(): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:foo]/', function () {
            // do nothing
        });

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->callRoute('/catalog/1/');
    }

    /**
     * Testing invalid id list data types behaviour.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testInValidIdListParams(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/catalog/[il:cat_id]/', function () {
            return 1;
        });

        // assertion
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The processor was not found for the route catalog/12345.');

        // test body
        $router->callRoute('/catalog/12345./');
    }

    /**
     * Testing dynamic routes for DELETE requests.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testDeleteRequestForUnExistingDynamicRoute(): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:cat_id]', function () {
            return 1;
        });

        // test body
        try {
            $router->callRoute('/catalog/1025/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        // assertions
        $msg = "The processor was not found for the route catalog/1025";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing dynamic routes for PUT requests.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testPutRequestForUnExistingDynamicRoute(): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:cat_id]', function () {
            return 1;
        });

        // test body
        try {
            $router->callRoute('/catalog/1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        // assertions
        $msg = "The processor was not found for the route catalog/1024";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing dynamic routes for POST requests.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testPostRequestForUnExistingDynamicRoute(): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:item_id]', function () {
            return 1;
        });

        // test body
        try {
            $router->callRoute('/catalog/1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        // assertions
        $msg = "The processor was not found for the route catalog/1024";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Data provider for the testNotMatchingRoutes.
     *
     * @return array testing data.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function invalidCasesDataProvider(): array
    {
        return [
            [
                '/catalog/[i:some_id]',
                '/catalog/1/a/'
            ],
            [
                '/existing/[i:bar]/',
                '/unexisting/1/'
            ]
        ];
    }

    /**
     * Testing that all invalid cases will be covered.
     *
     * @dataProvider invalidCasesDataProvider
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testNotMatchingRoutes(string $route, string $calling): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute($route, function () {
            // do nothing
        });

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->callRoute($calling);
    }

    /**
     * Testing invalid data types behaviour.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testInvalidType(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/catalog/[unexisting-type:i]/item/', function () {
            return 1;
        });

        // test body and assertions
        try {
            $router->callRoute('/catalog/1024/item/');
            $this->assertFalse(true, 'Exception expected');
        } catch (\Exception $e) {
            $this->assertFalse(false, '');
        }
    }

    /**
     * Testing invalid data types behaviour.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testValidInvalidTypes(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:cat_id]/item/[unexisting-type-trace:item_id]/', function () {
            return 1;
        });

        // test body and assertions
        try {
            $router->callRoute('/catalog/1024/item/2048/');
            $this->assertFalse(true, 'Exception expected');
        } catch (\Exception $e) {
            $this->assertFalse(false, '');
        }
    }

    /**
     * Testing unexisting route behaviour.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testUnexistingRoute(): void
    {
        // setup
        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/existing-route/', [
            new HelloWorld(),
            'helloWorld'
        ]);

        // test body
        try {
            $router->callRoute('/unexisting-route/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        // assertions
        $msg = "The processor was not found for the route";

        $this->assertNotFalse(strpos($exception, $msg), 'Valid error handling expected');
    }

    /**
     * Testing invalid integer data types behaviour.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testInValidIntegerParams(): void
    {
        // setup
        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/catalog/[i:cat_id]/', [
            new HelloWorld(),
            'helloWorld'
        ]);

        // test body
        try {
            $router->callRoute('/catalog/a1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        // assertions
        $msg = "The processor was not found for the route catalog/a1024";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }

    /**
     * Testing invalid alnum data types behaviour.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testInValidAlnumParams(): void
    {
        // setup
        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/catalog/[a:cat_id]/', [
            new HelloWorld(),
            'helloWorld'
        ]);

        // test body
        try {
            $router->callRoute('/catalog/~foo/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        // assertions
        $msg = "The processor was not found for the route catalog/~foo";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }
}
