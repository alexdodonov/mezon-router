# Routing [![Build Status](https://travis-ci.com/alexdodonov/mezon-router.svg?branch=master)](https://travis-ci.com/alexdodonov/mezon-router) [![codecov](https://codecov.io/gh/alexdodonov/mezon-router/branch/master/graph/badge.svg)](https://codecov.io/gh/alexdodonov/mezon-router)
##Intro##
Mezon provides simple routing class for your needs.

##Simple routes##

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
        return( 'This is the main page of our simple site' );
    }

    /**
    *   Contacts page.
    */
    public function actionContacts()
    {
        return( 'This is the "Contacts" page' );
    }

    /**
    *   Some custom action handler.
    */
    public function someOtherPage()
    {
        return( 'Some other page of our site' );
    }
}
```

And this code

```PHP
$Router = new Router();
$Router->fetchActions( $MySite = new MySite() );
```

will create router object and loads information about it's actions and create routes. Strictly it will create two routes, because the class MySite has only two methods wich start wth 'action[Suffix]'. Method 'someOtherPage' will not be converted into route automatically.

But we can still use this method as a route handler:

```PHP
$Router->addRoute( 'some-any-other-route' , array( $MySite , 'someOtherPage' ) );
```

We just need to create it explicitly.

We can also use simple functions for route creation:

```PHP
function        sitemap()
{
    return( 'Some fake sitemap' );
}

$Router->addRoute( 'sitemap' , 'sitemap' );
```

##One handler for all routes##

You can specify one processor for all routes like this:

```PHP
$Router->addRoute( '*' , function(){} );
```

Note that routing search will stops if the '*' handler will be found. For example:

```PHP
$Router->addRoute( '*' , function(){} );
$Router->addRoute( '/index/' , function(){} );
```

In this example route /index/ will never be reached. All request will be passed to the '*' handler. But in this example:

```PHP
$Router->add_route( '/contacts/' , function(){} );
$Router->add_route( '*' , function(){} );
$Router->add_route( '/index/' , function(){} );
```

route /contacts/ will be processed by it's own handler, and all other routes (even /index/) will be processed by the '*' handler.

##Route variables##

And now a little bit more complex routes:

```PHP
$Router->add_route( '/catalogue/[i:cat_id]/' , function( $Route , $Variables ){} );
$Router->add_route( '/catalogue/[a:cat_name]/' , function( $Route , $Variables ){} );
```

Here:

i - any integer number
a - any [a-z0-9A-Z_\/\-\.\@]+ string
il - comma separated list of integer ids
s - any string

All this variables are passed as second function parameter wich is named in the example above - $Variales. All variables are passed as an associative array.

##Request types and first steps to the REST API##

You can bind handlers to different request types as shown bellow:

```PHP
$Router->add_route( '/contacts/' , function(){} , 'POST' ); // this handler will be called for POST requests
$Router->add_route( '/contacts/' , function(){} , 'GET' );  // this handler will be called for GET requests
$Router->add_route( '/contacts/' , function(){} , 'PUT' );  // this handler will be called for PUT requests
$Router->add_route( '/contacts/' , function(){} , 'DELETE' );  // this handler will be called for DELETE requests
```