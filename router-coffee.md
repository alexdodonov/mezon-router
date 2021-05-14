Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case

```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['route'] = '/static/0';
$router = RouteGenerator::generateCoffeeStaticRoutes(1000);
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['route'] = '/static/99';
$router = RouteGenerator::generateCoffeeStaticRoutes(1000);
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['route'] = '/static/199';
$router = RouteGenerator::generateCoffeeStaticRoutes(1000);
$router->dispatch();
// and so on up to '/static/999'
```

```php
// non-static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['route'] = '/param/0/1';
$router = RouteGenerator::generateCoffeeNonStaticRoutes(1000);
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['route'] = '/param/99/1';
$router = RouteGenerator::generateCoffeeNonStaticRoutes(1000);
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['route'] = '/param/199/1';
$router = RouteGenerator::generateCoffeeNonStaticRoutes(1000);
$router->dispatch();
// and so on up to '/param/999/1'
```

# The second case

```php
// static routes
$router = RouteGenerator::generateCoffeeStaticRoutes(1000);
$_SERVER['REQUEST_METHOD'] = 'GET';

$_GET['route'] = '/static/0';
$router->dispatch();

$_GET['route'] = '/static/99';
$router->dispatch();

$_GET['route'] = '/static/199';
$router->dispatch();
// and so on up to '/static/999'
```

```php
// non-static routes
$router = RouteGenerator::generateCoffeeStaticRoutes(1000);
$_SERVER['REQUEST_METHOD'] = 'GET';

$_GET['route'] = '/param/0/1';
$router->dispatch();

$_GET['route'] = '/param/99/1';
$router->dispatch();

$_GET['route'] = '/param/199/1';
$router->dispatch();
// and so on up to '/param/999/1'
```

# OK What do we have?

## The first case + static routes
```
+--------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+-------------+--------+-------+
| benchmark                      | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst         | stdev       | rstdev | diff  |
+--------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+-------------+--------+-------+
| CoffeeSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,717,816b | 57,114.600μs | 72,248.750μs | 72,835.937μs | 84,073.000μs  | 6,224.424μs | 8.62%  | 4.07x |
| DVKSingleRequestStaticBench    | benchStatic | 0   | 10   | 10  | 3,174,616b | 45,853.400μs | 46,328.820μs | 46,264.563μs | 47,343.500μs  | 400.064μs   | 0.86%  | 2.61x |
| HoaSingleRequestStaticBench    | benchStatic | 0   | 10   | 10  | 3,776,920b | 77,757.900μs | 83,331.410μs | 79,498.998μs | 107,414.600μs | 8,859.008μs | 10.63% | 4.69x |
| MezonSingleRequestStaticBench  | benchStatic | 0   | 10   | 10  | 2,216,384b | 15,580.700μs | 17,758.980μs | 16,988.564μs | 24,923.800μs  | 2,484.516μs | 13.99% | 1.00x |
| PeceeSingleRequestStaticBench  | benchStatic | 0   | 10   | 10  | 6,944,176b | 64,954.500μs | 66,785.700μs | 65,917.391μs | 68,890.800μs  | 1,319.612μs | 1.98%  | 3.76x |
+--------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+-------------+--------+-------+
```

## The first case + non-static routes
```
+-------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| benchmark                     | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff  |
+-------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| CoffeeSingleRequestParamBench | benchParam | 0   | 10   | 10  | 3,684,856b   | 88,839.700μs  | 103,671.360μs | 96,351.329μs  | 125,747.800μs | 11,218.091μs | 10.82% | 1.94x |
| DVKSingleRequestParamBench    | benchParam | 0   | 10   | 10  | 3,167,000b   | 49,482.300μs  | 53,409.750μs  | 51,611.445μs  | 62,434.600μs  | 3,710.176μs  | 6.95%  | 1.00x |
| HoaSingleRequestParamBench    | benchParam | 0   | 10   | 10  | 4,095,848b   | 79,121.100μs  | 82,699.740μs  | 80,400.389μs  | 92,740.200μs  | 4,316.946μs  | 5.22%  | 1.55x |
| MezonSingleRequestParamBench  | benchParam | 0   | 10   | 10  | 139,437,736b | 132,078.700μs | 223,817.640μs | 241,220.955μs | 356,061.400μs | 65,446.202μs | 29.24% | 4.19x |
| PeceeSingleRequestParamBench  | benchParam | 0   | 10   | 10  | 6,528,360b   | 130,647.300μs | 135,673.060μs | 132,906.329μs | 152,846.600μs | 6,435.790μs  | 4.74%  | 2.54x |
+-------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
```

## The second case + static routes

```
+------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+-----------+
| benchmark              | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev       | rstdev | diff      |
+------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+-----------+
| CoffeeReactStaticBench | benchStatic | 0   | 10   | 10  | 2,190,984b | 14,511.400μs | 15,293.250μs | 14,892.025μs | 17,984.600μs | 991.885μs   | 6.49%  | 658.23x   |
| DVKReactStaticBench    | benchStatic | 0   | 100  | 10  | 2,433,920b | 18,777.050μs | 22,603.372μs | 25,267.912μs | 26,731.080μs | 3,140.461μs | 13.89% | 972.86x   |
| HoaReactStaticBench    | benchStatic | 0   | 100  | 10  | 2,822,992b | 4,101.870μs  | 5,395.865μs  | 4,728.676μs  | 8,812.130μs  | 1,388.968μs | 25.74% | 232.24x   |
| MezonReactStaticBench  | benchStatic | 0   | 100  | 10  | 1,837,216b | 18.850μs     | 23.234μs     | 20.104μs     | 33.090μs     | 4.651μs     | 20.02% | 1.00x     |
| PeceeReactStaticBench  | benchStatic | 0   | 10   | 10  | 4,103,040b | 23,918.000μs | 38,718.750μs | 36,545.299μs | 52,853.200μs | 9,860.320μs | 25.47% | 1,666.47x |
+------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+-----------+
```
## The second case + non-static routes
```
+-----------------------+------------+-----+------+-----+------------+--------------+---------------+--------------+---------------+--------------+--------+--------+
| benchmark             | subject    | set | revs | its | mem_peak   | best         | mean          | mode         | worst         | stdev        | rstdev | diff   |
+-----------------------+------------+-----+------+-----+------------+--------------+---------------+--------------+---------------+--------------+--------+--------+
| CoffeeReactParamBench | benchParam | 0   | 10   | 10  | 2,190,952b | 9,237.900μs  | 14,861.380μs  | 14,696.220μs | 21,579.500μs  | 2,970.912μs  | 19.99% | 6.68x  |
| DVKReactParamBench    | benchParam | 0   | 100  | 10  | 2,438,312b | 13,488.270μs | 21,691.879μs  | 15,864.905μs | 33,076.340μs  | 7,410.175μs  | 34.16% | 9.75x  |
| HoaReactParamBench    | benchParam | 0   | 100  | 10  | 3,291,208b | 6,370.420μs  | 7,746.860μs   | 6,946.499μs  | 14,326.540μs  | 2,275.580μs  | 29.37% | 3.48x  |
| MezonReactParamBench  | benchParam | 0   | 100  | 10  | 3,058,496b | 1,819.160μs  | 2,225.287μs   | 1,964.548μs  | 4,004.260μs   | 641.462μs    | 28.83% | 1.00x  |
| PeceeReactParamBench  | benchParam | 0   | 10   | 10  | 4,484,136b | 59,662.400μs | 101,070.560μs | 93,969.409μs | 156,518.100μs | 25,535.783μs | 25.27% | 45.42x |
+-----------------------+------------+-----+------+-----+------------+--------------+---------------+--------------+---------------+--------------+--------+--------+
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