Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case

```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';

$router = RouteGenerator::generateDVKStaticRoutes(1000);
$_SERVER['REQUEST_URI'] = '/static/0';
$router->matchCurrentRequest();

$router = RouteGenerator::generateDVKStaticRoutes(1000);
$_SERVER['REQUEST_URI'] = '/static/99';
$router->matchCurrentRequest();

$router = RouteGenerator::generateDVKStaticRoutes(1000);
$_SERVER['REQUEST_URI'] = '/static/199';
$router->matchCurrentRequest();
// and so on till the URL '/static/999' will be dispatched
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';

$router = RouteGenerator::generateDVKNonStaticRoutes(1000);
$_SERVER['REQUEST_URI'] = '/param/0/1';
$router->matchCurrentRequest();

$router = RouteGenerator::generateDVKNonStaticRoutes(1000);
$_SERVER['REQUEST_URI'] = '/param/99/1';
$router->matchCurrentRequest();

$router = RouteGenerator::generateDVKNonStaticRoutes(1000);
$_SERVER['REQUEST_URI'] = '/param/199/1';
$router->matchCurrentRequest();
// and so on till /param/999/1
```

# The second case

```php
// static routes
$router = RouteGenerator::generateDVKStaticRoutes(1000);
$_SERVER['REQUEST_METHOD'] = 'GET';

$_SERVER['REQUEST_URI'] = '/static/0';
$router->matchCurrentRequest();

$_SERVER['REQUEST_URI'] = '/static/99';
$router->matchCurrentRequest();

$_SERVER['REQUEST_URI'] = '/static/199';
$router->matchCurrentRequest();

// and so on till '/static/999'
```

For non static routes the code will be almost the same:

```php
// noon static routes
$router = RouteGenerator::generateDVKNonStaticRoutes(1000);
$_SERVER['REQUEST_METHOD'] = 'GET';

$_SERVER['REQUEST_URI'] = '/param/0/1/';
$router->matchCurrentRequest();

$_SERVER['REQUEST_URI'] = '/param/99/1';
$router->matchCurrentRequest();

$_SERVER['REQUEST_URI'] = '/param/199/1';
$router->matchCurrentRequest();

// and so on till '/param/999/1'
```

# OK What do we have?

## The first case + static routes
```
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+-------+
| benchmark                     | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst         | stdev        | rstdev | diff  |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+-------+
| DVKSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,164,904b | 26,676.000μs | 37,299.030μs | 32,646.041μs | 72,208.700μs  | 12,914.306μs | 34.62% | 2.82x |
| HoaSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,767,208b | 45,560.200μs | 67,694.660μs | 51,770.530μs | 129,072.800μs | 30,490.339μs | 45.04% | 5.12x |
| MezonSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,206,672b | 9,709.900μs  | 13,222.950μs | 11,321.774μs | 24,963.000μs  | 4,410.786μs  | 33.36% | 1.00x |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+-------+
```
## The first case + non-static routes
```
+------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| benchmark                    | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff  |
+------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| DVKSingleRequestParamBench   | benchParam | 0   | 10   | 10  | 3,157,288b   | 33,162.900μs  | 52,719.290μs  | 39,991.057μs  | 97,777.500μs  | 21,746.969μs | 41.25% | 1.00x |
| HoaSingleRequestParamBench   | benchParam | 0   | 10   | 10  | 4,086,136b   | 48,539.900μs  | 55,639.510μs  | 52,388.467μs  | 82,447.100μs  | 9,470.701μs  | 17.02% | 1.06x |
| MezonSingleRequestParamBench | benchParam | 0   | 10   | 10  | 139,428,024b | 136,006.900μs | 197,098.140μs | 149,144.021μs | 345,790.300μs | 71,218.783μs | 36.13% | 3.74x |
+------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
```
## The second case + static routes
```
+-----------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+---------+
| benchmark             | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev       | rstdev | diff    |
+-----------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+---------+
| DVKReactStaticBench   | benchStatic | 0   | 100  | 10  | 2,424,208b | 11,048.590μs | 15,240.144μs | 12,817.814μs | 22,492.560μs | 3,833.736μs | 25.16% | 699.12x |
| HoaReactStaticBench   | benchStatic | 0   | 100  | 10  | 2,813,280b | 3,766.940μs  | 4,199.743μs  | 3,987.875μs  | 5,587.870μs  | 524.565μs   | 12.49% | 192.66x |
| MezonReactStaticBench | benchStatic | 0   | 100  | 10  | 1,827,504b | 18.830μs     | 21.799μs     | 19.519μs     | 27.630μs     | 3.473μs     | 15.93% | 1.00x   |
+-----------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+---------+
```
## The second case + non static routes
```
+----------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
| benchmark            | subject    | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev     | rstdev | diff   |
+----------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
| DVKReactParamBench   | benchParam | 0   | 100  | 10  | 2,428,600b | 13,478.530μs | 14,429.128μs | 14,759.621μs | 15,486.620μs | 636.758μs | 4.41%  | 12.53x |
| HoaReactParamBench   | benchParam | 0   | 100  | 10  | 3,281,496b | 3,659.830μs  | 3,723.384μs  | 3,697.556μs  | 3,874.030μs  | 60.340μs  | 1.62%  | 3.23x  |
| MezonReactParamBench | benchParam | 0   | 100  | 10  | 3,048,784b | 1,094.830μs  | 1,151.188μs  | 1,112.331μs  | 1,263.570μs  | 59.018μs  | 5.13%  | 1.00x  |
+----------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-----------+--------+--------+
```

# What's next?

More articles can be found in my 

- [Twitter](https://twitter.com/mezonphp)
- [dev.to blog](https://dev.to/alexdodonov)
- [Medium blog](https://gdvever.medium.com/)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )