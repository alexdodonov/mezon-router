<?php

class RouterUnitTest extends \PHPUnit\Framework\TestCase
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
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return 'Hello world!';
    }

    /**
     * Testing action #1.
     */
    public function actionA1(): string
    {
        return 'action #1';
    }

    /**
     * Testing action #2.
     */
    public function actionA2(): string
    {
        return 'action #2';
    }

    public function actionDoubleWord(): string
    {
        return 'action double word';
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterClassMethod(): void
    {
        $router = new \Mezon\Router\Router();

        $router->addRoute('/index/', [
            $this,
            'helloWorldOutput'
        ]);

        $content = $router->callRoute('/index/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterLambda(): void
    {
        $router = new \Mezon\Router\Router();

        $router->addRoute('/index/', function () {
            return 'Hello world!';
        });

        $content = $router->callRoute('/index/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing unexisting route behaviour.
     */
    public function testUnexistingRoute(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/unexisting-route/');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route";

        $this->assertNotFalse(strpos($exception, $msg), 'Valid error handling expected');
    }

    /**
     * Testing action fetching method.
     */
    public function testClassActions(): void
    {
        $router = new \Mezon\Router\Router();
        $router->fetchActions($this);

        $content = $router->callRoute('/a1/');
        $this->assertEquals('action #1', $content, 'Invalid a1 route');

        $content = $router->callRoute('/a2/');
        $this->assertEquals('action #2', $content, 'Invalid a2 route');

        $content = $router->callRoute('/double-word/');
        $this->assertEquals('action double word', $content, 'Invalid a2 route');
    }

    /**
     * Method tests POST actions
     */
    public function testPostClassAction(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $router = new \Mezon\Router\Router();
        $router->fetchActions($this);
        $content = $router->callRoute('/a1/');
        $this->assertEquals('action #1', $content, 'Invalid a1 route');
    }

    /**
     * Testing invalid data types behaviour.
     */
    public function testInvalidType(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[unexisting-type:i]/item/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/1024/item/');
            $this->assertFalse(true, 'Exception expected');
        } catch (Exception $e) {
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
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/1024/item/2048/');
            $this->assertFalse(true, 'Exception expected');
        } catch (Exception $e) {
            $this->assertFalse(false, '');
        }
    }

    /**
     * Testing valid data types behaviour.
     */
    public function testValidTypes(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]/item/[i:item_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/1024/item/2048/');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "Illegal parameter type";

        $this->assertFalse(strpos($exception, $msg), 'Valid type expected');
    }

    /**
     * Testing valid integer data types behaviour.
     */
    public function testValidIntegerParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/1024/');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "Illegal parameter type";

        $this->assertFalse(strpos($exception, $msg), 'Valid type expected');
    }

    /**
     * Testing valid alnum data types behaviour.
     */
    public function testValidAlnumParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[a:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/foo/');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "Illegal parameter type";

        $this->assertFalse(strpos($exception, $msg), 'Valid type expected');
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
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route /catalog/a1024/";

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
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route /catalog/~foo/";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameter(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[a:cat_id]/', function ($route, $parameters) {
            return $parameters['cat_id'];
        });

        $result = $router->callRoute('/catalog/foo/');

        $this->assertEquals($result, 'foo', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidExtractedParameters(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute(
            '/catalog/[a:cat_id]/[i:item_id]',
            function ($route, $parameters) {
                return $parameters['cat_id'] . $parameters['item_id'];
            });

        $result = $router->callRoute('/catalog/foo/1024/');

        $this->assertEquals($result, 'foo1024', 'Invalid extracted parameter');
    }

    /**
     * Testing parameter extractor.
     */
    public function testValidRouteParameter(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function ($route) {
            return $route;
        });
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        });

        $result = $router->callRoute('/catalog/');

        $this->assertEquals($result, '/catalog/', 'Invalid extracted route');

        $result = $router->callRoute('/catalog/1024/');

        $this->assertEquals($result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for POST requests.
     */
    public function testPostRequestForExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function ($route) {
            return $route;
        }, 'POST');

        $result = $router->callRoute('/catalog/');

        $this->assertEquals($result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for POST requests.
     */
    public function testPostRequestForExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        }, 'POST');

        $result = $router->callRoute('/catalog/1024/');

        $this->assertEquals($result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for POST requests.
     */
    public function testPostRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route /catalog/";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }

    /**
     * Testing dynamic routes for POST requests.
     */
    public function testPostRequestForUnExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/1024/');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route /catalog/1024/";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }

    /**
     * Testing case when both GET and POST processors exists.
     */
    public function testGetPostPostDeleteRouteConcurrency(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function () {
            return 'POST';
        }, 'POST');
        $router->addRoute('/catalog/', function () {
            return 'GET';
        }, 'GET');
        $router->addRoute('/catalog/', function () {
            return 'PUT';
        }, 'PUT');
        $router->addRoute('/catalog/', function () {
            return 'DELETE';
        }, 'DELETE');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'POST');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'GET');

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'PUT');

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'DELETE');
    }

    /**
     * Testing 'clear' method.
     */
    public function testClearMethod(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function () {
            return 'POST';
        }, 'POST');
        $router->addRoute('/catalog/', function () {
            return 'GET';
        }, 'GET');
        $router->addRoute('/catalog/', function () {
            return 'PUT';
        }, 'PUT');
        $router->addRoute('/catalog/', function () {
            return 'DELETE';
        }, 'DELETE');
        $router->clear();

        try {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $router->callRoute('/catalog/');
            $flag = 'not cleared';
        } catch (Exception $e) {
            $flag = 'cleared';
        }
        $this->assertEquals($flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $router->callRoute('/catalog/');
            $flag = 'not cleared';
        } catch (Exception $e) {
            $flag = 'cleared';
        }
        $this->assertEquals($flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'PUT';
            $router->callRoute('/catalog/');
            $flag = 'not cleared';
        } catch (Exception $e) {
            $flag = 'cleared';
        }
        $this->assertEquals($flag, 'cleared', 'Data was not cleared');

        try {
            $_SERVER['REQUEST_METHOD'] = 'DELETE';
            $router->callRoute('/catalog/');
            $flag = 'not cleared';
        } catch (Exception $e) {
            $flag = 'cleared';
        }
        $this->assertEquals($flag, 'cleared', 'Data was not cleared');
    }

    /**
     * Method increments assertion count
     */
    protected function errorHandler(): void
    {
        $this->addToAssertionCount(1);
    }

    /**
     * Test validate custom error handlers.
     */
    public function testSetErrorHandler(): void
    {
        $router = new \Mezon\Router\Router();
        $router->setNoProcessorFoundErrorHandler(function () {
            $this->errorHandler();
        });

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $router->callRoute('/unexisting/');
    }
}
