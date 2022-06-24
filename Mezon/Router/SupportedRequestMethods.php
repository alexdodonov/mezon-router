<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Class SimpleRouter
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, http://aeon.su
 */

/**
 * Supported request types
 *
 * @author gdever
 */
class SupportedRequestMethods
{

    /**
     * Method returns a list of supported request methods
     *
     * @return string[] list of supported request methods
     */
    public static function getListOfSupportedRequestMethods(): array
    {
        return [
            'GET',
            'POST',
            'PUT',
            'DELETE',
            'OPTION',
            'PATCH'
        ];
    }

    /**
     * Method validates request method
     *
     * @param string $requestMethod
     *            HTTP request method
     */
    public static function validateRequestMethod(string $requestMethod): void
    {
        if (!in_array($requestMethod, static::getListOfSupportedRequestMethods())) {
            throw (new \Exception('Unsupported request method: "' . $requestMethod . '"', -1));
        }
    }
}
