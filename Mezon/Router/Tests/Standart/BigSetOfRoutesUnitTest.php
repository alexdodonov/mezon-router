<?php
namespace Mezon\Router\Tests\Standart;

use PHPUnit\Framework\TestCase;
use Mezon\Router\Router;
use Mezon\Router\Tests\Base\RouterUnitTestUtils;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class BigSetOfRoutesUnitTest extends TestCase
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
        $router = new Router();
        for ($i = 1; $i <= $amount; $i ++) {
            $router->addRoute('/param/[i:id]/' . $i, function () use ($i): int {
                return $i;
            });
        }

        // test body
        $result = $router->callRoute('/param/1/' . $amount);

        // assertions
        $this->assertEquals($amount, $result);
    }
}
