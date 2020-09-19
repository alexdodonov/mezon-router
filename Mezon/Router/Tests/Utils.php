<?php
namespace Mezon\Router\Tests;

/**
 * Common unit-tests utilities
 * 
 * @author gdever
 */
trait Utils
{

    /**
     * Method sets request uri
     *
     * @param string $requestUri
     *            request uri
     */
    protected function setRequestUri(string $requestUri): void
    {
        $_SERVER['REQUEST_URI'] = $requestUri;
    }
}
