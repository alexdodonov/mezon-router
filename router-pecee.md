Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case

```php
// static routes
$router = RouteGenerator::generatePeceeStaticRoutes(1000);
$router->getRequest()->setUrl(new Url('/static/0'));
$router->getRequest()->setMethod('get');
$router->start();

$router = RouteGenerator::generatePeceeStaticRoutes(1000);
$router->getRequest()->setUrl(new Url('/static/99'));
$router->getRequest()->setMethod('get');
$router->start();

$router = RouteGenerator::generatePeceeStaticRoutes(1000);
$router->getRequest()->setUrl(new Url('/static/199'));
$router->getRequest()->setMethod('get');
$router->start();
// and so on up to '/static/999'
```
Non-static routes:

```php
$router = RouteGenerator::generatePeceeNonStaticRoutes(1000);
$router->getRequest()->setUrl(new Url('/param/0/1'));
$router->getRequest()->setMethod('get');
$router->start();

$router = RouteGenerator::generatePeceeNonStaticRoutes(1000);
$router->getRequest()->setUrl(new Url('/param/99/1'));
$router->getRequest()->setMethod('get');
$router->start();

$router = RouteGenerator::generatePeceeNonStaticRoutes(1000);
$router->getRequest()->setUrl(new Url('/param/199/1'));
$router->getRequest()->setMethod('get');
$router->start();
// and so on up to '/param/999/1'
```

# The second case

```php
$router = RouteGenerator::generatePeceeStaticRoutes(1000);

$router->getRequest()->setUrl(new Url('/static/0'));
$router->getRequest()->setMethod('get');
$router->start();

$router->getRequest()->setUrl(new Url('/static/99'));
$router->getRequest()->setMethod('get');
$router->start();

$router->getRequest()->setUrl(new Url('/static/199'));
$router->getRequest()->setMethod('get');
$router->start();
// and so on up to '/static/999'
```
Non-static routes:

```php
$router = RouteGenerator::generatePeceeNonStaticRoutes(1000);
$router->getRequest()->setUrl(new Url('/param/0/1'));
$router->getRequest()->setMethod('get');
$router->start();

$router->getRequest()->setUrl(new Url('/param/99/1'));
$router->getRequest()->setMethod('get');
$router->start();

$router->getRequest()->setUrl(new Url('/param/199/1'));
$router->getRequest()->setMethod('get');
$router->start();
// and so on up to '/param/999/1'
```
# OK What do we have?

## The first case + static routes
```
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+-------+
| benchmark                     | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst         | stdev        | rstdev | diff  |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+-------+
| DVKSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,169,760b | 32,444.300μs | 40,121.470μs | 37,618.470μs | 48,182.400μs  | 5,328.241μs  | 13.28% | 3.19x |
| HoaSingleRequestStaticBench   | benchStatic | 0   | 10   | 10  | 3,772,064b | 55,991.800μs | 64,991.640μs | 62,583.264μs | 81,316.900μs  | 7,115.357μs  | 10.95% | 5.16x |
| MezonSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,211,528b | 10,973.500μs | 12,595.210μs | 11,765.806μs | 14,942.800μs  | 1,358.407μs  | 10.79% | 1.00x |
| PeceeSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 6,939,320b | 47,342.800μs | 68,375.800μs | 53,452.352μs | 167,419.000μs | 35,936.088μs | 52.56% | 5.43x |
+-------------------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+-------+
```

## The first case + non-static routes
```
+------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| benchmark                    | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff  |
+------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| DVKSingleRequestParamBench   | benchParam | 0   | 10   | 10  | 3,162,144b   | 32,829.700μs  | 41,110.350μs  | 39,790.715μs  | 49,529.600μs  | 5,064.512μs  | 12.32% | 1.00x |
| HoaSingleRequestParamBench   | benchParam | 0   | 10   | 10  | 4,090,992b   | 51,596.500μs  | 65,339.800μs  | 55,031.464μs  | 108,218.000μs | 20,450.626μs | 31.30% | 1.59x |
| MezonSingleRequestParamBench | benchParam | 0   | 10   | 10  | 139,432,880b | 149,946.900μs | 214,165.110μs | 171,992.241μs | 323,257.200μs | 59,659.846μs | 27.86% | 5.21x |
| PeceeSingleRequestParamBench | benchParam | 0   | 10   | 10  | 6,523,504b   | 79,722.600μs  | 92,934.320μs  | 91,920.295μs  | 117,962.000μs | 9,651.642μs  | 10.39% | 2.26x |
+------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
```

## The second case + static routes

```
+-----------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+---------+
| benchmark             | subject     | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev       | rstdev | diff    |
+-----------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+---------+
| DVKReactStaticBench   | benchStatic | 0   | 100  | 10  | 2,429,064b | 11,877.080μs | 18,817.626μs | 15,714.995μs | 27,952.280μs | 5,398.121μs | 28.69% | 272.60x |
| HoaReactStaticBench   | benchStatic | 0   | 100  | 10  | 2,818,136b | 4,588.740μs  | 9,116.453μs  | 10,712.551μs | 13,069.140μs | 2,891.315μs | 31.72% | 132.07x |
| MezonReactStaticBench | benchStatic | 0   | 100  | 10  | 1,832,360b | 21.040μs     | 69.030μs     | 55.919μs     | 199.270μs    | 49.229μs    | 71.32% | 1.00x   |
| PeceeReactStaticBench | benchStatic | 0   | 10   | 10  | 4,098,184b | 24,875.300μs | 32,002.620μs | 34,481.087μs | 41,128.800μs | 5,201.623μs | 16.25% | 463.60x |
+-----------------------+-------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+---------+
```
## The second case + non-static routes
```
+----------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+--------+
| benchmark            | subject    | set | revs | its | mem_peak   | best         | mean         | mode         | worst         | stdev        | rstdev | diff   |
+----------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+--------+
| DVKReactParamBench   | benchParam | 0   | 100  | 10  | 2,433,456b | 14,381.460μs | 18,069.357μs | 15,746.601μs | 26,204.970μs  | 3,907.676μs  | 21.63% | 6.82x  |
| HoaReactParamBench   | benchParam | 0   | 100  | 10  | 3,286,352b | 3,862.330μs  | 4,365.385μs  | 4,270.722μs  | 5,134.820μs   | 341.324μs    | 7.82%  | 1.65x  |
| MezonReactParamBench | benchParam | 0   | 100  | 10  | 3,053,640b | 1,155.640μs  | 2,648.767μs  | 2,063.057μs  | 6,195.750μs   | 1,445.991μs  | 54.59% | 1.00x  |
| PeceeReactParamBench | benchParam | 0   | 10   | 10  | 4,479,280b | 59,373.000μs | 74,590.120μs | 65,226.368μs | 114,763.200μs | 17,391.520μs | 23.32% | 28.16x |
+----------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+---------------+--------------+--------+--------+
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