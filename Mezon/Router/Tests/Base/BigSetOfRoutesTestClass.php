<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class BigSetOfRoutesTestClass extends StaticRoutesTestClass
{

    /**
     * Method provides testing data
     *
     * @return array testing data
     */
    public function bigSetOfRoutesDataProvider(): array
    {
        return [
            [
                99
            ],
            [
                101
            ]
        ];
    }

    /**
     * Testing method
     *
     * @param int $amount
     *            amount of routes
     * @dataProvider bigSetOfRoutesDataProvider
     */
    public function testBigSetOfRoutes(int $amount): void
    {
        // setup
        RouterUnitTestUtils::setRequestMethod('GET');
        $router = $this->getRouter();
        for ($i = 1; $i <= $amount; $i ++) {
            $router->addRoute('/param/[i:id]/' . $i, function () use ($i): int {
                return $i;
            });
        }

        // test body
        $result = (int) $router->callRoute('/param/1/' . $amount);

        // assertions
        $this->assertEquals($result, $amount);
    }
}
