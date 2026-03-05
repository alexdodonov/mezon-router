<?php
namespace Mezon\Router\Tests\Base;

class HelloWorld
{
    
    /**
     * Handler string.
     * 
     * @var string
     */
    public const HELLO_WORLD_STRING = 'Hello World!!!';

    /**
     * Member function
     * 
     * @return string Hello world string
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function helloWorld(): string
    {
        return HelloWorld::HELLO_WORLD_STRING;
    }
    
    /**
     * Static function
     *
     * @return string Hello world string
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function staticHelloWorld(): string
    {
        return HelloWorld::HELLO_WORLD_STRING;
    }
}