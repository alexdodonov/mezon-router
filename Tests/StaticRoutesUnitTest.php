<?php

class StaticRoutesUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return 'Hello world!';
    }

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
     * Testing one processor for all routes.
     */
    public function testSingleAllProcessor(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        $content = $router->callRoute('/some-route/');

        $this->assertEquals('Hello world!', $content);
    }
}
