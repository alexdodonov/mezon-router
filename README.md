# Routing [![Build Status](https://travis-ci.com/alexdodonov/mezon-router.svg?branch=master)](https://travis-ci.com/alexdodonov/mezon-router) [![codecov](https://codecov.io/gh/alexdodonov/mezon-router/branch/master/graph/badge.svg)](https://codecov.io/gh/alexdodonov/mezon-router)
## Intro
Mezon provides simple routing class for your needs.

## Installation

Just print in console

```
composer require mezon/router
```

And that's all )

## Simple routes

Router allows you to map URLs on your php code and call when ever it needs to be calld.

Router supports simple routes like in the example above - example.com/contacts/

Each Application object implicity creates routes for it's 'action[action-name]' methods, where 'action-name' will be stored as a route. Here is small (as usual)) ) example:

```PHP
class           MySite
{
    /**
    *   Main page.
    */
    public function actionIndex()
    {
        return 'This is the main page of our simple site';
    }

    /**
    *   Contacts page.
    */
    public function actionContacts()
    {
        return 'This is the "Contacts" page';
    }

    /**
    *   Some custom action handler.
    */
    public function someOtherPage()
    {
        return 'Some other page of our site';
    }
    
    public static function someStaticMethod()
    {
        return 'Result of static method';
    }
}
```

And this code

```PHP
$router = new \Mezon\Router\Router();
$router->fetchActions( $mySite = new MySite() );
```

will create router object and loads information about it's actions and create routes. Strictly it will create two routes, because the class MySite has only two methods wich start wth 'action[Suffix]'. Method 'someOtherPage' will not be converted into route automatically.

But we can still use this method as a route handler:

```PHP
$router->addRoute( '/some-any-other-route/' , [ $mySite , 'someOtherPage' ] );
```

And you also can use stati methods:

```PHP
$router->addRoute( '/static-route/' , [ 'MySite' , 'someStaticMethod' ] );
// or in this way
$router->addRoute( '/static-route/' , 'MySite::someStaticMethod' );
```

We just need to create it explicitly.

We can also use simple functions for route creation:

```PHP
function        sitemap()
{
    return( 'Some fake sitemap' );
}

$router->addRoute( '/sitemap/' , 'sitemap' );
```

## One handler for all routes

You can specify one processor for all routes like this:

```PHP
$router->addRoute( '/*/' , function(){} );
```

Note that routing search will stops if the '*' handler will be found. For example:

```PHP
$router->addRoute( '/*/' , function(){} );
$router->addRoute( '/index/' , function(){} );
```

In this example route /index/ will never be reached. All request will be passed to the '*' handler. But in this example:

```PHP
$router->addRoute( '/contacts/' , function(){} );
$router->addRoute( '/*/' , function(){} );
$router->addRoute( '/index/' , function(){} );
```

route /contacts/ will be processed by it's own handler, and all other routes (even /index/) will be processed by the '*' handler.

## Route variables

And now a little bit more complex routes:

```PHP
$router->addRoute( '/catalogue/[i:cat_id]/' , function( $route , $variables ){} );
$router->addRoute( '/catalogue/[a:cat_name]/' , function( $route , $variables ){} );
```

Here:

i - any integer number
a - any [a-z0-9A-Z_\/\-\.\@]+ string
il - comma separated list of integer ids
s - any string

All this variables are passed as second function parameter wich is named in the example above - $Variales. All variables are passed as an associative array.

## Request types and first steps to the REST API

You can bind handlers to different request types as shown bellow:

```PHP
$router->addRoute( '/contacts/' , function(){} , 'POST' ); // this handler will be called for POST requests
$router->addRoute( '/contacts/' , function(){} , 'GET' );  // this handler will be called for GET requests
$router->addRoute( '/contacts/' , function(){} , 'PUT' );  // this handler will be called for PUT requests
$router->addRoute( '/contacts/' , function(){} , 'DELETE' );  // this handler will be called for DELETE requests
```