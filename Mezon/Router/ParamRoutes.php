<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait ParamRoutes
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, aeon.org
 */
trait ParamRoutes
{

    /**
     * List of non static routes
     *
     * @var array<string, array<array-key, array{pattern: string, callback: array{0: string, 1:string}|callable|string}|array{bunch: array<int<1, max>, array{callback: mixed, pattern: string}>}>>
     */
    protected $paramRoutes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'OPTION' => [],
        'PATCH' => []
    ];
}
