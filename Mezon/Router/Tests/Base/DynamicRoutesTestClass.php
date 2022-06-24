<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use Mezon\Router\Types\DateRouterType;
use PHPUnit\Framework\TestCase;
use Mezon\Router\SupportedRequestMethods;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class DynamicRoutesTestClass extends BaseRouterUnitTestClass
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

    const TYPES_ROUTE_CATALOG_INT_BAR = '/catalog/[i:bar]/';

    const TYPES_ROUTE_CATALOG_FIX_POINT_BAR = '/catalog/[fp:bar]/';

    /**
     * Data provider for the testTypes
     *
     * @return array test data
     */
    public function typesDataProvider(): array
    {
        $data = [
            // #0
            [
                [
                    DynamicRoutesTestClass::TYPES_ROUTE_CATALOG_INT_BAR
                ],
                '/catalog/1/',
                1
            ],
            // #1
            [
                [
                    DynamicRoutesTestClass::TYPES_ROUTE_CATALOG_INT_BAR
                ],
                '/catalog/-1/',
                - 1
            ],
            // #2
            [
                [
                    DynamicRoutesTestClass::TYPES_ROUTE_CATALOG_FIX_POINT_BAR
                ],
                '/catalog/1.1/',
                1.1
            ],
            // #3
            [
                [
                    DynamicRoutesTestClass::TYPES_ROUTE_CATALOG_FIX_POINT_BAR
                ],
                '/catalog/-1.1/',
                - 1.1
            ],
            // #4
            [
                [
                    '/[a:bar]/'
                ],
                '/.-@/',
                '.-@'
            ],
            // #5
            [
                [
                    '/[s:bar]/'
                ],
                '/, ;:/',
                ', ;:'
            ],
            // #6
            [
                [
                    '/[fp:number]/',
                    '/[s:bar]/'
                ],
                '/abc/',
                'abc'
            ],
            // #7, list of integers
            [
                [
                    '/catalog/[il:bar]/'
                ],
                '/catalog/123,456,789/',
                '123,456,789'
            ],
            // #8, string
            [
                [
                    '/catalog/[s:bar]/'
                ],
                '/catalog/123&456/',
                '123&456'
            ],
            // #9, parameter name chars testing
            [
                [
                    '/[s:Aa_x-0]/'
                ],
                '/abc123/',
                'abc123',
                'Aa_x-0'
            ],
            // #10, date type testing 1
            [
                [
                    '/[date:dfield]/'
                ],
                '/2020-02-02/',
                '2020-02-02',
                'dfield'
            ],
            // #11, date type testing 2
            [
                [
                    '/posts-[date:dfield]/'
                ],
                '/posts-2020-02-02/',
                '2020-02-02',
                'dfield'
            ]
        ];

        $return = [];

        foreach (SupportedRequestMethods::getListOfSupportedRequestMethods() as $method) {
            $tmp = array_merge($data);

            foreach ($tmp as $item) {
                $item = array_merge([
                    $method
                ], $item);
                $return[] = $item;
            }
        }

        return $return;
    }

    /**
     * Testing router types
     *
     * @param string $method
     *            request method
     * @param string[] $pattern
     *            route pattern
     * @param string $route
     *            real route
     * @param mixed $expected
     *            expected value
     * @param string $paramName
     *            name of the validating parameter
     * @dataProvider typesDataProvider
     */
    public function testTypes(string $method, array $pattern, string $route, $expected, string $paramName = 'bar'): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = $method;
        $router = $this->getRouter();
        $router->addType('date', DateRouterType::class);

        foreach ($pattern as $r) {
            $router->addRoute($r, function () {
                // do nothing
            }, $method);
        }

        $router->callRoute($route);

        // test body and assertions
        $this->assertEquals($expected, $router->getParam($paramName));
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

    /**
     * Testing real life example #1
     */
    public function testRealLifeExample1(): void
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
