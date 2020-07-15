# Intro

Hello all! Now we benchmarking Slim framework's router.

As usual we have two cases:

1. http server accepts request, launches php script, wich handles this request, and then all script data uploads from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# Benchmark principes

Here is our static routes generator

```php
public static function generateSlimStaticRoutes(int $amount): \Slim\App
{
    $router = AppFactory::create();

    for ($i = 0; $i < $amount; $i ++) {
        $router->get('/static/' . $i, '\Mezon\Benchmark\paramCallbackPsr7');
    }

    return $router;
}
```

And here is the generator for non-static routes

```php
public static function generateSlimNonStaticRoutes(int $amount): \Slim\App
{
    $router = AppFactory::create();

    for ($i = 0; $i < $amount; $i ++) {
        $router->get('/param/' . $i . '/{id}', '\Mezon\Benchmark\paramCallbackPsr7');
    }

    return $router;
}
```

Here is our test for the first case:

```php
$_SERVER['REQUEST_METHOD'] = 'GET';

for ($i = 0; $i < \Mezon\Benchmark\Base::$iterationsAmount; $i ++) {
    $_SERVER['REQUEST_URI'] = '/static/' . rand(0, 100 - 1);

    $router = \Mezon\Benchmark\RouteGenerator::generateSlimStaticRoutes(1000);

    $router->run();
}
```

And for the second case:

```php
$_SERVER['REQUEST_METHOD'] = 'GET';
$router = \Mezon\Benchmark\RouteGenerator::generateSlimStaticRoutes(1000);

for ($i = 0; $i < \Mezon\Benchmark\Base::$iterationsAmount; $i ++) {
    $_SERVER['REQUEST_URI'] = '/static/' . rand(0, 100 - 1);
    $router->run();
}
```

# Results

Let's see the results (the bigger numbers means better)!

![table](./images/table-slim.png)

**As you can see Mezon router is up to 60 times faster then Slim. And it is faster almost in all tests.**

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# I'll be very glad if you'll press "STAR" button on [Github](https://github.com/alexdodonov/mezon-router) )