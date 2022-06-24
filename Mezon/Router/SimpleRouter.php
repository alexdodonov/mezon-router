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
 * Simple router class
 */
class SimpleRouter extends RouterBase implements RouterInterface
{

    use SimpleRoutesSet, SimpleUrlParser;
}
