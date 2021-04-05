Hi all! Once again we shall benchmark PHP routers.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

But! Since now I shall 

1. use [phpbench](https://github.com/phpbench/phpbench)
2. publish benchmarks in [github repo](https://github.com/alexdodonov/mezon-router-benchmark)

# The first case

Benchmark code was a little bit changed - I have removed random part and since now we just requesting the same routes:

```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';

$router = RouteGenerator::generateHoaStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['argv'][1] = '/static/0';
$dispatcher->dispatch($router);

$router = RouteGenerator::generateHoaStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['argv'][1] = '/static/99';
$dispatcher->dispatch($router);

$router = RouteGenerator::generateHoaStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['argv'][1] = '/static/199';
$dispatcher->dispatch($router);
// and so on till the URL '/static/999' will be dispatched
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';

$router = RouteGenerator::generateHoaNonStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['argv'][1] = '/param/0/1';
$dispatcher->dispatch($router);

$router = RouteGenerator::generateHoaNonStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['argv'][1] = '/param/99/1';
$dispatcher->dispatch($router);

$router = RouteGenerator::generateHoaNonStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['argv'][1] = '/param/199/1';
$dispatcher->dispatch($router);

// and so on till /param/999/1
```

# The second case

```php
// static routes
$router = RouteGenerator::generateHoaStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['REQUEST_METHOD'] = 'GET';

$_SERVER['argv'][1] = '/static/0';
$dispatcher->dispatch($router);

$_SERVER['argv'][1] = '/static/99';
$dispatcher->dispatch($router);

$_SERVER['argv'][1] = '/static/199';
$dispatcher->dispatch($router);

// and so on till '/static/999'
```

For non static routes the code will be almost the same:

```php
// noon static routes
$router = RouteGenerator::generateHoaNonStaticRoutes(1000);
$dispatcher = new \Hoa\Dispatcher\Basic();
$_SERVER['REQUEST_METHOD'] = 'GET';

$_SERVER['argv'][1] = '/param/0/1';
$dispatcher->dispatch($router);

$_SERVER['argv'][1] = '/param/99/1';
$dispatcher->dispatch($router);

// and so on till '/param/999/1'
```

# OK What do we have?

## The first case + static routes
```
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| benchmark                     | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev        | rstdev | diff  |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| HoaSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,760,704b | 48,051.900μs | 54,332.420μs | 50,230.044μs | 90,860.800μs | 12,281.704μs | 22.60% | 4.80x |
| MezonSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,200,168b | 9,542.800μs  | 11,313.750μs | 10,500.511μs | 17,310.900μs | 2,170.652μs  | 19.19% | 1.00x |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
```

## The first case + non-static routes

```
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| benchmark                     | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev        | rstdev | diff  |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| HoaSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,760,704b | 48,051.900μs | 54,332.420μs | 50,230.044μs | 90,860.800μs | 12,281.704μs | 22.60% | 4.80x |
| MezonSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,200,168b | 9,542.800μs  | 11,313.750μs | 10,500.511μs | 17,310.900μs | 2,170.652μs  | 19.19% | 1.00x |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
```

## The second case + static routes

```
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| benchmark                     | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev        | rstdev | diff  |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| HoaSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,760,704b | 48,051.900μs | 54,332.420μs | 50,230.044μs | 90,860.800μs | 12,281.704μs | 22.60% | 4.80x |
| MezonSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,200,168b | 9,542.800μs  | 11,313.750μs | 10,500.511μs | 17,310.900μs | 2,170.652μs  | 19.19% | 1.00x |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
```

## The second case + non static routes

```
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| benchmark                     | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev        | rstdev | diff  |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
| HoaSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,760,704b | 48,051.900μs | 54,332.420μs | 50,230.044μs | 90,860.800μs | 12,281.704μs | 22.60% | 4.80x |
| MezonSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,200,168b | 9,542.800μs  | 11,313.750μs | 10,500.511μs | 17,310.900μs | 2,170.652μs  | 19.19% | 1.00x |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+--------------+--------+-------+
```

# What's next?

More articles can be found in my [Twitter](https://twitter.com/mezonphp)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )