<?php
namespace Mezon\Router\Tests\Base;

class RouterUnitTestUtils
{

    const DELETE_REQUEST_METHOD = 'DELETE';

    /**
     * Function sets $_SERVER['REQUEST_METHOD']
     *
     * @param string $requestMethod
     *            request method
     */
    public static function setRequestMethod(string $requestMethod): void
    {
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
    }
}