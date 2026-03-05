<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use PHPUnit\Framework\TestCase;
use Mezon\Router\Tests\Base\HelloWorld;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor, UnusedClass
 */
class RouterUtilsUnitTest extends TestCase
{

    /**
     * Datapdovider for the test testGetCallableDescription
     *
     * @return array testing data
     */
    public static function getCallableDescriptionDataProvider(): array
    {
        $handler = new HelloWorld();

        return [
            [
                'string-processor',
                'string-processor'
            ],
            [
                [
                    $handler,
                    'helloWorld'
                ],
                get_class($handler) . '::helloWorld'
            ],
            [
                [
                    'class-name',
                    'static-method-name'
                ],
                'class-name::static-method-name'
            ],
            [
                [
                    'string-processor'
                ],
                'a:1:{i:0;s:16:"string-processor";}'
            ]
        ];
    }

    /**
     * Testing getCallableDescription method
     *
     * @param mixed $callable
     * @param string $result
     * @dataProvider getCallableDescriptionDataProvider
     */
    public function testGetCallableDescription($callable, string $result): void
    {
        // test body and assertions
        $this->assertEquals($result, \Mezon\Router\Utils::getCallableDescription($callable));
    }

    /**
     * Testing convertMethodNameToRoute
     */
    public function testMethodNameToRouteConversion(): void
    {
        // test body
        $result = \Mezon\Router\Utils::convertMethodNameToRoute('actionSomeRouter');

        // assertions
        $this->assertEquals('some-router', $result);
    }
}
