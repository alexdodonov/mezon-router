<?php
namespace Mezon\Router\Tests;

class DynamicRoutesInvalidCasesUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Default setup
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * Testing getParam for unexisting param
     */
    public function testGettingUnexistingParameter(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function () {
            // do nothing
        });

        $router->callRoute('/catalog/1/');

        $this->expectException(\Exception::class);

        // test body and assertions
        $router->getParam('unexisting');
    }

    /**
     * Testing exception throwing for unexisting request method
     */
    public function testExceptionForUnexistingRequestMethod(): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $router = new \Mezon\Router\Router();
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
     */
    public function testInValidIdListParams(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[il:cat_id]/', [
            $this,
            function () {return 1;}
        ]);

        // assertion
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The processor was not found for the route catalog/12345.');

        // test body
        $router->callRoute('/catalog/12345./');
    }

    /**
     * Testing dynamic routes for DELETE requests.
     */
    public function testDeleteRequestForUnExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', [
            $this,
            function () {return 1;}
        ]);

        try {
            $router->callRoute('/catalog/1025/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route catalog/1025";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing dynamic routes for PUT requests.
     */
    public function testPutRequestForUnExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', [
            $this,
            function () {return 1;}
        ]);

        try {
            $router->callRoute('/catalog/1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route catalog/1024";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing dynamic routes for POST requests.
     */
    public function testPostRequestForUnExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:item_id]', [
            $this,
            function () {return 1;}
        ]);

        try {
            $router->callRoute('/catalog/1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route catalog/1024";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Data provider for the testNotMatchingRoutes
     *
     * @return array testing data
     */
    public function invalidCasesDataProvider(): array
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
     * Testing that all invalid cases will be covered
     *
     * @dataProvider invalidCasesDataProvider
     */
    public function testNotMatchingRoutes(string $route, string $calling): void
    {
        // setup
        $router = new \Mezon\Router\Router();
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
     */
    public function testInvalidType(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[unexisting-type:i]/item/', [
            $this,
            function () {return 1;}
        ]);

        try {
            $router->callRoute('/catalog/1024/item/');
            $this->assertFalse(true, 'Exception expected');
        } catch (\Exception $e) {
            $this->assertFalse(false, '');
        }
    }

    /**
     * Testing invalid data types behaviour.
     */
    public function testValidInvalidTypes(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]/item/[unexisting-type-trace:item_id]/', [
            $this,
            function () {return 1;}
        ]);

        try {
            $router->callRoute('/catalog/1024/item/2048/');
            $this->assertFalse(true, 'Exception expected');
        } catch (\Exception $e) {
            $this->assertFalse(false, '');
        }
    }

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return 'Hello world!';
    }

    /**
     * Testing unexisting route behaviour
     */
    public function testUnexistingRoute(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/existing-route/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/unexisting-route/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route";

        $this->assertNotFalse(strpos($exception, $msg), 'Valid error handling expected');
    }
    
    
    /**
     * Testing invalid integer data types behaviour.
     */
    public function testInValidIntegerParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);
        
        try {
            $router->callRoute('/catalog/a1024/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }
        
        $msg = "The processor was not found for the route catalog/a1024";
        
        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }
    
    /**
     * Testing invalid alnum data types behaviour.
     */
    public function testInValidAlnumParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[a:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);
        
        try {
            $router->callRoute('/catalog/~foo/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }
        
        $msg = "The processor was not found for the route catalog/~foo";
        
        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }
}
