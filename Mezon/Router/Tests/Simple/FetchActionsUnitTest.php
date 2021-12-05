<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Simple;

use PHPUnit\Framework\TestCase;
use Mezon\Router\Tests\Base\RouterUnitTestUtils;
use Mezon\Router\SimpleRouter;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class FetchActionsUnitTest extends TestCase
{

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

    /**
     * Two word action name
     *
     * @return string
     */
    public function actionDoubleWord(): string
    {
        return 'action double word';
    }

    /**
     * Data provider for the test testClassAction
     *
     * @return array test data
     */
    public function classActionDataProvider(): array
    {
        $testData = [];

        foreach ([
            'GET',
            'POST'
        ] as $requestMethod) {
            $testData[] = [
                $requestMethod,
                '/a1/',
                'action #1'
            ];

            $testData[] = [
                $requestMethod,
                '/a2/',
                'action #2'
            ];

            $testData[] = [
                $requestMethod,
                '/double-word/',
                'action double word'
            ];
        }

        return $testData;
    }

    /**
     * Method tests class actions
     *
     * @param string $requestMethod
     *            request method
     * @param string $route
     *            requesting route
     * @param string $expectedResult
     *            expected result of route processing
     * @dataProvider classActionDataProvider
     */
    public function testClassAction(string $requestMethod, string $route, string $expectedResult): void
    {
        // setup
        RouterUnitTestUtils::setRequestMethod($requestMethod);
        $router = new SimpleRouter();

        // test body
        $router->fetchActions($this);

        // assertions
        $this->assertEquals($expectedResult, $router->callRoute($route));
    }

    /**
     * Testing map for the fetchActions method
     */
    public function testFetchActionsWithMap(): void
    {
        // setup
        $router = new SimpleRouter();

        // test body
        $router->fetchActions($this, [
            'A1' => 'GET',
            'A2' => 'POST',
            'DoubleWord' => [
                'GET',
                'POST'
            ]
        ]);

        // assertions #1
        RouterUnitTestUtils::setRequestMethod('GET');
        $this->assertEquals('action #1', $router->callRoute('a1'));

        // assertions #2
        RouterUnitTestUtils::setRequestMethod('POST');
        $this->assertEquals('action #2', $router->callRoute('a2'));

        // assertions #3
        RouterUnitTestUtils::setRequestMethod('GET');
        $this->assertEquals('action double word', $router->callRoute('double-word'));

        // assertions #4
        RouterUnitTestUtils::setRequestMethod('POST');
        $this->assertEquals('action double word', $router->callRoute('double-word'));

        // assertions #5
        RouterUnitTestUtils::setRequestMethod('POST');
        $this->expectException(\Exception::class);
        $router->callRoute('a1');

        // assertions #6
        RouterUnitTestUtils::setRequestMethod('GET');
        $this->expectException(\Exception::class);
        $router->callRoute('a2');

        // assertions #7
        RouterUnitTestUtils::setRequestMethod('DELETE');
        $this->expectException(\Exception::class);
        $router->callRoute('double-word');
    }
}
