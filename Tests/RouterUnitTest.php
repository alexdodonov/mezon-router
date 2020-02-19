<?php

/**
 * Mockup router class
 */
class MockRouter extends \Mezon\Router\Router
{

    // TODO can we remove this class?
    public $errorVar = 0;

    /**
     * Mock error handler.
     */
    public function setErrorVar()
    {
        $this->errorVar = 7;
    }
}

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
     * Method for checking id list.
     */
    public function ilTest($route, $params): string
    {
        return $params['ids'];
    }

    /**
     * Function simply returns string.
     */
    static public function staticHelloWorldOutput(): string
    {
        return 'Hello static world!';
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
     * Testing one component router.
     */
    public function testOneComponentRouterStatic(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');

        $content = $router->callRoute('/index/');

        $this->assertEquals('Hello static world!', $content);
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
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapUnexisting(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);
        $router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');

        $content = $router->callRoute('/some-route/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapExisting(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);
        $router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');

        $content = $router->callRoute('/index/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorExisting(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        $content = $router->callRoute('/index/');

        $this->assertEquals('Hello static world!', $content);
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorUnexisting(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', 'RouterUnitTest::staticHelloWorldOutput');
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        $content = $router->callRoute('/some-route/');

        $this->assertEquals('Hello world!', $content);
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
        $router->addRoute('/catalog/[a:cat_id]/[i:item_id]', function ($route, $parameters) {
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
     * Testing static routes for PUT requests.
     */
    public function testPutRequestForExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function ($route) {
            return $route;
        }, 'PUT');

        $result = $router->callRoute('/catalog/');

        $this->assertEquals($result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for PUT requests.
     */
    public function testPutRequestForExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        }, 'PUT');

        $result = $router->callRoute('/catalog/1024/');

        $this->assertEquals($result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for PUT requests.
     */
    public function testPutRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

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
     * Testing dynamic routes for PUT requests.
     */
    public function testPutRequestForUnExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

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
     * Testing static routes for DELETE requests.
     */
    public function testDeleteRequestForExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function ($route) {
            return $route;
        }, 'DELETE');

        $result = $router->callRoute('/catalog/');

        $this->assertEquals($result, '/catalog/', 'Invalid extracted route');
    }

    /**
     * Testing dynamic routes for DELETE requests.
     */
    public function testDeleteRequestForExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:cat_id]', function ($route) {
            return $route;
        }, 'DELETE');

        $result = $router->callRoute('/catalog/1024/');

        $this->assertEquals($result, '/catalog/1024/', 'Invalid extracted route');
    }

    /**
     * Testing static routes for DELETE requests.
     */
    public function testDeleteRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

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
     * Testing dynamic routes for DELETE requests.
     */
    public function testDeleteRequestForUnExistingDynamicRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

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
     * Test validate custom error handlers.
     */
    public function testSetErrorHandler(): void
    {
        $router = new MockRouter();
        $current = $router->setNoProcessorFoundErrorHandler([
            $router,
            'setErrorVar'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $router->callRoute('/unexisting/');

        $router->setNoProcessorFoundErrorHandler($current);

        $this->assertEquals($router->errorVar, 7, 'Handler was not set');
    }

    /**
     * Testing command special chars.
     */
    public function testCommandSpecialChars(): void
    {
        $router = new \Mezon\Router\Router();

        $router->addRoute('/[a:url]/', function () {
            return 'GET';
        }, 'GET');

        $result = $router->callRoute('/.-@/');
        $this->assertEquals($result, 'GET', 'Invalid selected route');
    }

    /**
     * Testing strings.
     */
    public function testStringSpecialChars(): void
    {
        $router = new \Mezon\Router\Router();

        $router->addRoute('/[s:url]/', function () {
            return 'GET';
        }, 'GET');

        $result = $router->callRoute('/, ;:/');
        $this->assertEquals($result, 'GET', 'Invalid selected route');
    }

    /**
     * Testing invalid id list data types behaviour.
     */
    public function testInValidIdListParams(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[il:cat_id]/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/catalog/12345./');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route /catalog/12345./";

        $this->assertNotFalse(strpos($exception, $msg), 'Invalid error response');
    }

    /**
     * Testing valid id list data types behaviour.
     */
    public function testValidIdListParams(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[il:ids]/', [
            $this,
            'ilTest'
        ]);

        $result = $router->callRoute('/catalog/123,456,789/');

        $this->assertEquals($result, '123,456,789', 'Invalid router response');
    }

    /**
     * Testing valid id list data types behaviour.
     */
    public function testStringParamSecurity(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[s:foo]/', function ($route, $parameters) {
            return $parameters['foo'];
        });

        $result = $router->callRoute('/catalog/123&456/');

        $this->assertEquals($result, '123&amp;456', 'Security data violation');
    }

    /**
     * Testing float value.
     */
    public function testFloatI(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function ($route, $parameters) {
            return $parameters['foo'];
        });

        $result = $router->callRoute('/catalog/1.1/');

        $this->assertEquals($result, '1.1', 'Float data violation');
    }

    /**
     * Testing negative float value.
     */
    public function testNegativeFloatI(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function ($route, $parameters) {
            return $parameters['foo'];
        });

        $result = $router->callRoute('/catalog/-1.1/');

        $this->assertEquals($result, '-1.1', 'Float data violation');
    }

    /**
     * Testing positive float value.
     */
    public function testPositiveFloatI(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function ($route, $parameters) {
            return $parameters['foo'];
        });

        $result = $router->callRoute('/catalog/+1.1/');

        $this->assertEquals($result, '+1.1', 'Float data violation');
    }

    /**
     * Testing negative integer value
     */
    public function testNegativeIntegerI(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function ($route, $parameters) {
            return $parameters['foo'];
        });

        $result = $router->callRoute('/catalog/-1/');

        $this->assertEquals('-1', $result, 'Float data violation');
    }

    /**
     * Testing positive integer value
     */
    public function testPositiveIntegerI(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function ($route, $parameters) {
            return $parameters['foo'];
        });

        $result = $router->callRoute('/catalog/1/');

        $this->assertEquals('1', $result, 'Float data violation');
    }

    /**
     * Testing array routes
     */
    public function testArrayRoutes(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/item/', function ($route) {
            return $route;
        }, 'GET');

        $result = $router->callRoute([
            'catalog',
            'item'
        ]);

        $this->assertEquals($result, '/catalog/item/', 'Invalid extracted route');
    }

    /**
     * Testing empty array routes
     */
    public function testEmptyArrayRoutes(): void
    {
        $_SERVER['REQUEST_URI'] = '/catalog/item/';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/item/', function ($route) {
            return $route;
        }, 'GET');

        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, '/catalog/item/', 'Invalid extracted route');
    }

    /**
     * Testing empty array routes
     */
    public function testIndexRoute(): void
    {
        $_SERVER['REQUEST_URI'] = '/';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', function ($route) {
            return $route;
        }, 'GET');

        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, '/index/', 'Invalid extracted route');
    }

    /**
     * Testing saving of the route parameters
     */
    public function testSavingParameters(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function ($route, $parameters) {
            return $parameters['foo'];
        });

        $router->callRoute('/catalog/-1/');

        $this->assertEquals($router->getParam('foo'), '-1', 'Float data violation');
    }

    /**
     * Testing empty array routes
     */
    public function testMultipleRequestTypes(): void
    {
        // setup
        $_SERVER['REQUEST_URI'] = '/';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', function ($route) {
            return $route;
        }, [
            'GET',
            'POST'
        ]);

        $router->callRoute([
            0 => ''
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, '/index/', 'Invalid extracted route');
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

        $this->expectException(Exception::class);

        // test body and assertions
        $router->getParam('unexisting');
    }

    /**
     * Testing getParam for existing param
     */
    public function testGettingExistingParameter(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function () {
            // do nothing
        });

        $router->callRoute('/catalog/1/');

        // test body
        $foo = $router->getParam('foo');

        // assertions
        $this->assertEquals(1, $foo);
    }

    /**
     * Testing hasParam method
     */
    public function testValidatingParameter(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/[i:foo]/', function () {
            // do nothing
        });

        $router->callRoute('/catalog/1/');

        // test body and assertions
        $this->assertTrue($router->hasParam('foo'));
        $this->assertFalse($router->hasParam('unexisting'));
    }
}
