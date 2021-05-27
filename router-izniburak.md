Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$router = RouteGenerator::generateIzniburakStaticRoutes(1000);
$router->run();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$router = RouteGenerator::generateIzniburakStaticRoutes(1000);
$router->run();
// and so on up to /static/999 route
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
$router = RouteGenerator::generateIzniburakNonStaticRoutes(1000);
$router->run();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
$router = RouteGenerator::generateIzniburakNonStaticRoutes(1000);
$router->run();
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$router = RouteGenerator::generateIzniburakStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0/';
$router->getRequest()->setRequest(Request::createFromGlobals());
$router->run();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$router->getRequest()->setRequest(Request::createFromGlobals());
$router->run();
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generateIzniburakNonStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1';
$router->getRequest()->setRequest(Request::createFromGlobals());
$router->run();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1';
$router->getRequest()->setRequest(Request::createFromGlobals());
$router->run();
// and so on up to /param/999/1 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                          | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+------------------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 2,726,688b | 44,930.100μs  | 48,843.620μs  | 49,042.423μs  | 53,759.500μs  | 2,576.378μs  | 5.27%  | 4.76x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,183,488b | 26,231.000μs  | 29,232.720μs  | 27,717.013μs  | 38,679.400μs  | 3,563.856μs  | 12.19% | 2.85x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,785,824b | 47,827.200μs  | 53,422.280μs  | 51,314.538μs  | 64,962.100μs  | 5,122.040μs  | 9.59%  | 5.20x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 3,410,008b | 131,565.100μs | 150,642.360μs | 145,408.308μs | 194,165.800μs | 16,645.691μs | 11.05% | 14.67x |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 2,225,288b | 9,263.700μs   | 10,269.020μs  | 9,843.289μs   | 11,379.200μs  | 728.175μs    | 7.09%  | 1.00x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 6,953,048b | 39,218.400μs  | 52,893.890μs  | 45,318.560μs  | 121,249.500μs | 23,081.904μs | 43.64% | 5.15x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,504,304b | 209,973.500μs | 222,837.330μs | 215,014.655μs | 241,388.500μs | 10,517.884μs | 4.72%  | 21.70x |
+------------------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 3,693,728b   | 54,305.800μs  | 62,155.690μs  | 57,876.706μs  | 75,041.400μs  | 6,818.389μs  | 10.97% | 1.63x |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 3,175,872b   | 32,183.600μs  | 38,111.540μs  | 35,103.260μs  | 59,309.900μs  | 7,789.136μs  | 20.44% | 1.00x |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,104,752b   | 50,285.400μs  | 58,662.590μs  | 54,524.056μs  | 75,767.200μs  | 7,612.619μs  | 12.98% | 1.54x |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 3,530,728b   | 157,378.900μs | 165,344.290μs | 161,849.894μs | 177,074.400μs | 6,574.235μs  | 3.98%  | 4.34x |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 139,446,640b | 123,874.200μs | 133,030.600μs | 126,617.895μs | 156,479.500μs | 10,813.653μs | 8.13%  | 3.49x |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 6,537,232b   | 73,576.900μs  | 75,998.870μs  | 76,030.112μs  | 78,102.500μs  | 1,134.951μs  | 1.49%  | 1.99x |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 2,604,256b   | 220,071.400μs | 226,266.080μs | 223,442.114μs | 251,388.400μs | 8,572.150μs  | 3.79%  | 5.94x |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+-----------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff      |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+-----------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 2,199,856b | 7,857.300μs   | 8,099.410μs   | 8,020.821μs   | 8,653.100μs   | 217.096μs    | 2.68%  | 270.90x   |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,442,792b | 10,369.860μs  | 10,689.749μs  | 10,538.042μs  | 12,054.980μs  | 471.448μs    | 4.41%  | 357.54x   |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,831,896b | 3,504.580μs   | 3,676.266μs   | 3,590.923μs   | 4,265.290μs   | 214.528μs    | 5.84%  | 122.96x   |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 2,973,264b | 28,254.800μs  | 32,670.450μs  | 31,438.739μs  | 42,280.600μs  | 3,961.679μs  | 12.13% | 1,092.73x |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 1,846,120b | 18.860μs      | 29.898μs      | 22.572μs      | 94.730μs      | 21.939μs     | 73.38% | 1.00x     |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,111,912b | 19,633.900μs  | 28,712.160μs  | 24,375.286μs  | 48,138.000μs  | 8,648.113μs  | 30.12% | 960.34x   |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 2,543,472b | 241,983.600μs | 272,281.160μs | 254,860.090μs | 366,132.400μs | 37,485.927μs | 13.77% | 9,107.00x |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+-----------+
```

## The second case + non-static routes
```
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev       | rstdev | diff    |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 2,199,824b | 7,967.600μs   | 8,293.840μs   | 8,154.505μs   | 8,860.200μs   | 280.842μs   | 3.39%  | 7.51x   |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 2,447,184b | 12,884.070μs  | 14,004.567μs  | 13,337.607μs  | 16,102.920μs  | 1,139.453μs | 8.14%  | 12.69x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 3,300,112b | 3,666.650μs   | 4,047.296μs   | 3,901.218μs   | 4,718.090μs   | 356.743μs   | 8.81%  | 3.67x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 3,095,744b | 34,336.400μs  | 35,516.560μs  | 35,116.274μs  | 38,531.300μs  | 1,156.322μs | 3.26%  | 32.18x  |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 3,067,400b | 1,062.440μs   | 1,103.840μs   | 1,091.468μs   | 1,205.070μs   | 40.157μs    | 3.64%  | 1.00x   |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 4,493,008b | 44,313.100μs  | 46,629.070μs  | 48,080.800μs  | 49,138.400μs  | 1,842.240μs | 3.95%  | 42.24x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 2,602,624b | 220,789.000μs | 225,492.690μs | 222,547.406μs | 230,555.800μs | 3,651.505μs | 1.62%  | 204.28x |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
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