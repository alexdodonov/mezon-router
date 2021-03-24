# Routing

[![Build Status](https://travis-ci.com/alexdodonov/mezon-router.svg?branch=master)](https://travis-ci.com/alexdodonov/mezon-router) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexdodonov/mezon-router/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexdodonov/mezon-router/?branch=master) [![codecov](https://codecov.io/gh/alexdodonov/mezon-router/branch/master/graph/badge.svg)](https://codecov.io/gh/alexdodonov/mezon-router)

## Intro
[Mezon Framework](https://github.com/alexdodonov/mezon) provides simple routing class for your needs. It is already used in [Web Application](https://github.com/alexdodonov/mezon-common-application), [Service](https://github.com/alexdodonov/mezon-service), [CRUD Service](https://github.com/alexdodonov/mezon-crud-service)

## Contributors

[@jaumarar](https://github.com/jaumarar) - have implemented multiple middleware for routes. Global middleware is also made by him.

[@sinakuhestani](https://github.com/sinakuhestani) - have provided a better way for processing universal route. Plus some bug fixes.

Once again thank you people for your contributions )

## Installation

Just print in console

```
composer require mezon/router
```

And that's all )

## Reasons to use

The mezon/router is 

- more than 25 times faster then klein/klein router;
- 7 to 15 times faster then Symfony router;
- 30 to 50 times faster then Laravel router;
- more then 1,5 times faster then nikic/fast-toute;

More benchmarks can be found [here](https://github.com/alexdodonov/mezon-router-benchmark)

# Learn more

More information can be found here:

[Twitter](https://twitter.com/mezonphp)

[dev.to](https://dev.to/alexdodonov)

## What is "First case" and "Second case"?

1. **First case** - http server accepts request, launches php script, wich handles this request, and then all script data uploads from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. **Second case** - php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster

In this table you can see requests per second. The bigger numbers mean better )

![results](https://github.com/alexdodonov/mezon-router/blob/doc/images/table-1.2.8.jpg?raw=true)

[mezon and klein comparison](doc/router.md)

[mezon and symfony comparison](doc/router-symfony.md)

[mezon and laravel comparison](doc/router-laravel.md)

[mezon and fast-route comparison](doc/fast-route.md)

[mezon and yii2 router comparison](doc/yii2.md)

# I'll be very glad if you'll press "STAR" button )



## Simple routes

Router allows you to map URLs on your php code and call when ever it needs to be called.

Router supports simple routes like in the example above - example.com/contacts/

Each Application object implicitly creates routes for it's 'action[action-name]' methods, where 'action-name' will be stored as a route. Here is small (as usual)) ) example:

```PHP
class           MySite
{
    /**
     * Main page
     */
    public function actionIndex()
    {
        return 'This is the main page of our simple site';
    }

    /**
     * Contacts page
     */
    public function actionContacts()
    {
        return 'This is the "Contacts" page';
    }

    /**
     * FAQ page
     */
    public function actionFaq()
    {
        return 'This is the "FAQ" page';
    }

    /**
     * Contacts page
     */
    public function actionContacts()
    {
        return 'This is the "Contacts" page';
    }

    /**
     * Some custom action handler
     */
    public function someOtherPage()
    {
        return 'Some other page of our site';
    }
    
    /**
     * Some static method
     */
    public static function someStaticMethod()
    {
        return 'Result of static method';
    }
}
```

And this code

```PHP
$router = new \Mezon\Router\Router();
$router->fetchActions($mySite = new MySite());
```

will create router object and loads information about it's actions and create routes. Strictly it will create two routes, because the class MySite has only two methods wich start with 'action[Suffix]'. Method 'someOtherPage' will not be converted into route automatically. By default this method will create routes wich handle both POST and GET request methods.

Then just call to run callback by URL:

```php
$router->callRoute('/index/');
```

There is a way to specify request methods for each action:

```php
$router->fetchActions($mySite = new MySite(), [
	'Index' => 'GET',
	'Contacts' => 'POST',
	'Faq' => ['GET', 'POST'],
]);
```

You can manually specify callbacks for every URL in your application:

```PHP
$router->addRoute('/some-any-other-route/', [$mySite, 'someOtherPage']);
```

And you also can use static methods:

```PHP
$router->addRoute('/static-route/', ['MySite', 'someStaticMethod']);
// or in this way
$router->addRoute('/static-route/', 'MySite::someStaticMethod');
```

We just need to create it explicitly.

We can also use simple functions for route creation:

```PHP
function        sitemap()
{
    return 'Some fake sitemap';
}

$router->addRoute('/sitemap/', 'sitemap');
```

And you can find callback without launching it:

```php
$router->addRoute('/static-route/', 'MySite::someStaticMethod');
$callback = $router->getCallback('/static-route/');
var_dump($callback());
```

## Supported request methods

Mezon Router supports: GET, POST, PUT, DELETE, OPTION

To get the list of these methods you can use method getListOfSupportedRequestMethods:

```php
$router = new \Mezon\Router\Router();
var_dump($router->getListOfSupportedRequestMethods());
```

## One handler for all routes

You can specify one processor for all routes like this:

```PHP
$router->addRoute('/*/', function(){});
```

Note that routing search will stops if the '*' handler will be found. For example:

```PHP
$router->addRoute('/*/', function(){});
$router->addRoute('/index/', function(){});
```

In this example route /index/ will never be reached. All request will be passed to the '*' handler. But in this example:

```PHP
$router->addRoute('/contacts/', function(){});
$router->addRoute('/*/', function(){});
$router->addRoute('/index/', function(){});
```

route /contacts/ will be processed by it's own handler, and all other routes (even /index/) will be processed by the '*' handler.

## Route variables

And now a little bit more complex routes:

```PHP
$router->addRoute('/catalogue/[i:cat_id]/', function($route, $variables){});
$router->addRoute('/catalogue/[a:cat_name]/', function($route, $variables){});
```

Here:

i - any integer number
a - any [a-z0-9A-Z_\/\-\.\@]+ string
il - comma separated list of integer ids
s - any string

Parameter name must consist of the following chars: [a-zA-Z0-9_\-] 

All this variables are passed as second function parameter wich is named in the example above - $variales. All variables are passed as an associative array.

## Request types and first steps to the REST API

You can bind handlers to different request types as shown bellow:

```PHP
$router->addRoute('/contacts/', function(){}, 'POST'); // this handler will be called for POST requests
$router->addRoute('/contacts/', function(){}, 'GET');  // this handler will be called for GET requests
$router->addRoute('/contacts/', function(){}, 'PUT');  // this handler will be called for PUT requests
$router->addRoute('/contacts/', function(){}, 'DELETE');  // this handler will be called for DELETE requests
$router->addRoute('/contacts/', function(){}, 'OPTION');  // this handler will be called for OPTION requests
$router->addRoute('/contacts/', function(){}, 'PATCH');  // this handler will be called for PATCH requests
```

## Reverse routes

You can reverse routes and compile URLs by route's name. For example:

```php
$router = new \Mezon\Router\Router();
$router->addRoute('/some-route/[i:id]', function(){}, 'GET', 'name of the route');
// will output /some-route/123
var_dump($router->reverse('name of the route', ['id' => 123]));
```

## Routes caching

Since version 1.1.0 you can cache routes on disk and read them from this cache.

To dump cache on disk use:

```php
$router->dumpOnDisk('./cache/cache.php');
```

And after that you can load routes:

```php
$router->loadFromDisk('./cache/cache.php');
```

But these methods have limitations - they can not dump and load closures because of obvious reasons

You can also worm cache without dumping:

```php
$router->warmCache();
```

## Middleware and parameters modification

Types of middlewares that you can add which will be called before the route handler will be executed. This middleware can transform common parameters $route and $parameters into something different.
- Multiple global middlewares that will be **called in order of attachment**
- Multiple route specific middlewares that **will be called in order of attachment**

Order of execution of the middlewares
1. Global middlewares ``$router->addRoute('*', ...)``
2. Before calling route callback ``$router->addRoute('/example', ...)`` all those matching the route will be executed

Let's look at a simple example:

```php
$router = new Router();
$router->addRoute('/user/[i:id]', function(string $route, array $parameters){
    $userModel = new UserModel();
    $userObject = $userModel->getUserById($parameters['id']);

    // use $userObject for any purpose you need
});
```

Now let's watch an example with all the possibilities 

```php
$router = new Router();

// First step. We have an API that talks JSON, convert the body
$router->registerMiddleware('*', function (string $route, array $parameters){
    $request = Request::createFromGlobals();
    
    $parameters['_request'] = $request;
    $parameters['_body'] = json_decode($request->getContent(), true);

    return $parameters;    
});

// Second step. Ensure that we are logged in when we are in the private area
$router->registerMiddleware('*', function (string $route, array $parameters){
    // Is not a private area
    if (mb_strpos($route, '/user') !== 0 || empty($parameters['user_id'])) {
        return $parameters;
    }

    $token = $parameters['_request']->headers->get('oauth_token');

    $auth = new SomeAuth();
    $auth->validateTokenOrFail(
        $token,
        $parameters['user_id']
    );

    // We don't need to return nothing
});

// Last step. Now we will modify the parameters so the handler can work with them
$router->registerMiddleware('/user/[i:user_id]', function(string $route, array $parameters){
    $userModel = new UserModel();
    
    return $userModel->getUserById(
        $parameters['user_id']
    );
});

// Final destination. We have ended the middlewares, now we can work with the processed data
$router->addRoute('/user/[i:user_id]', function (UserObject $userObject){
    // Do everything
});
```

## PSR-7 routes processing

Originally Mezon Router was not designed to be PSR-7 compatible. But one of the latest features have made it possible. You can use middleware for this purpose. For example:

```php
$router = new Router();
$router->addRoute('/user/[i:id]', function(\Nyholm\Psr7\Request $request){
    // work here with the request in PSR-7 way

    $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

    $responseBody = $psr17Factory->createStream('Hello world');
    $response = $psr17Factory->createResponse(200)->withBody($responseBody);
    (new \Zend\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
});

$router->registerMiddleware('/user/[i:id]', function(string $route, array $parameters){
    $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

    $creator = new \Nyholm\Psr7Server\ServerRequestCreator(
        $psr17Factory, // ServerRequestFactory
        $psr17Factory, // UriFactory
        $psr17Factory, // UploadedFileFactory
        $psr17Factory  // StreamFactory
    );

    return $creator->fromGlobals();
});
```

The best thing about it - if you don't use PSR-7 in your project, then you don't "pay" for it )

## Custom types

You can define your own types for URL parser. Let's try to create `date` type.

First of all we should create simple class:

```php
class DateRouterType
{

    /**
     * Method returns regexp for searching this entity in the URL
     *
     * @return string regexp for searching
     */
    public static function searchRegExp(): string
    {
        return '(\[date:'.BaseType::PARAMETER_NAME_REGEXP.'\])';
    }
}
```

Here BaseType::PARAMETER_NAME_REGEXP is a global setting wich tells router that parameter names must consist of:

- a-z and A-Z letters
- 0-9
- and symbols _ and -

Now we need to define one more class method wich will parse date if it will occur:

```php
public static function parserRegExp(): string
{
    // pretty simple regexp
    return '([0-9]{4}-[0-9]{2}-[0-9]{2})';
}
```

And somewhere in your setup files you need to switch this type on:

```php
$router->addType('date', DateRouterType::class);
```

Now you can handle routes like this:

```bash
/some-url-part/2020-02-02/ending-part/
/posts-for-2020-02-02/
```

But be careful. For example you will define such routes:

```php
$router->addRoute('/posts-for-[date:posts-date]/', function(UserObject $userObject){
    // some activities here
});

$router->addRoute('/[s:some-url]/', function(UserObject $userObject){
    // some activities here
});
```

Then the first handler `/posts-for-[date:posts-date]/` will be called for the route `/posts-for-2020-02-02/`.
