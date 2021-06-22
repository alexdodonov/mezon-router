# Intro

Hi all! Today we have one more benchmark.

As usual we have two cases:

http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$routes = RouteGenerator::generateToroStaticRoutes(1000);
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$routes = RouteGenerator::generateToroStaticRoutes(1000);
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
$routes = RouteGenerator::generateToroStaticRoutes(1000);
\Toro::serve($routes);
// and so on up to /static/999 route
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1';
$routes = RouteGenerator::generateToroStaticRoutes(1000);
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1';
$routes = RouteGenerator::generateToroStaticRoutes(1000);
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1';
$routes = RouteGenerator::generateToroStaticRoutes(1000);
\Toro::serve($routes);
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$routes = RouteGenerator::generateToroStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
\Toro::serve($routes);
// and so on up to /static/999 route
```

```php
// non static routes
$routes = RouteGenerator::generateToroStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
\Toro::serve($routes);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1/';
\Toro::serve($routes);
// and so on up to /param/999/1 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                          | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 2,936,232b  | 51,788.600μs  | 72,561.940μs  | 64,995.644μs  | 120,653.900μs | 19,539.896μs | 26.93% | 32.80x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,392,992b  | 30,391.000μs  | 34,287.980μs  | 32,068.417μs  | 41,819.800μs  | 3,676.896μs  | 10.72% | 15.50x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,847,864b  | 51,521.200μs  | 60,500.540μs  | 55,449.889μs  | 76,615.700μs  | 8,949.335μs  | 14.79% | 27.35x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 3,471,624b  | 145,029.000μs | 157,675.860μs | 151,599.563μs | 185,481.500μs | 11,959.481μs | 7.58%  | 71.28x  |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 2,287,376b  | 10,700.700μs  | 11,377.070μs  | 10,910.978μs  | 12,893.600μs  | 787.896μs    | 6.93%  | 5.14x   |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 33,649,608b | 34,688.700μs  | 36,498.910μs  | 36,175.851μs  | 40,116.800μs  | 1,493.226μs  | 4.09%  | 16.50x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 7,085,072b  | 42,979.500μs  | 50,972.690μs  | 44,869.381μs  | 76,280.500μs  | 12,341.746μs | 24.21% | 23.04x  |
| PowerSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 3,862,216b  | 22,560.900μs  | 24,287.840μs  | 23,619.461μs  | 27,762.100μs  | 1,503.188μs  | 6.19%  | 10.98x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,566,392b  | 242,619.200μs | 293,642.760μs | 275,573.834μs | 421,766.200μs | 49,686.592μs | 16.92% | 132.75x |
| ToroSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 1,745,216b  | 1,648.400μs   | 2,212.000μs   | 1,935.956μs   | 4,529.600μs   | 812.268μs    | 36.72% | 1.00x   |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 3,903,272b   | 61,363.600μs  | 73,230.700μs  | 65,808.914μs  | 92,480.800μs  | 11,389.641μs | 15.55% | 4.81x  |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 3,385,376b   | 34,263.500μs  | 36,090.960μs  | 35,074.116μs  | 38,970.600μs  | 1,553.119μs  | 4.30%  | 2.37x  |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,165,464b   | 55,861.500μs  | 69,817.250μs  | 60,315.758μs  | 105,342.500μs | 17,027.445μs | 24.39% | 4.59x  |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 3,592,344b   | 171,847.700μs | 199,650.260μs | 179,596.867μs | 259,843.800μs | 31,804.423μs | 15.93% | 13.12x |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 139,508,728b | 146,697.900μs | 158,502.100μs | 151,439.221μs | 196,143.100μs | 15,167.294μs | 9.57%  | 10.42x |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 34,508,144b  | 41,190.600μs  | 44,517.500μs  | 42,656.145μs  | 60,393.000μs  | 5,470.118μs  | 12.29% | 2.93x  |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 6,669,256b   | 88,335.200μs  | 97,452.940μs  | 91,227.214μs  | 152,588.200μs | 18,551.188μs | 19.04% | 6.40x  |
| PowerSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 3,862,216b   | 24,931.000μs  | 43,285.750μs  | 37,113.992μs  | 86,569.700μs  | 17,254.528μs | 39.86% | 2.84x  |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 2,666,344b   | 349,656.300μs | 409,768.320μs | 384,646.479μs | 505,134.400μs | 50,775.289μs | 12.39% | 26.93x |
| ToroSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 1,866,176b   | 13,830.500μs  | 15,216.090μs  | 14,714.330μs  | 17,033.600μs  | 1,019.982μs  | 6.70%  | 1.00x  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 2,409,400b | 10,106.800μs  | 19,048.790μs  | 13,410.792μs  | 35,305.900μs  | 8,214.204μs  | 43.12% | 771.02x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,652,296b | 11,424.930μs  | 12,610.662μs  | 11,873.045μs  | 14,696.170μs  | 1,066.321μs  | 8.46%  | 510.43x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,893,936b | 3,872.190μs   | 3,991.312μs   | 3,915.664μs   | 4,213.960μs   | 121.955μs    | 3.06%  | 161.55x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 3,034,880b | 27,870.900μs  | 28,814.580μs  | 28,855.359μs  | 29,924.200μs  | 559.066μs    | 1.94%  | 1,166.30x  |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 1,908,208b | 18.760μs      | 24.706μs      | 20.367μs      | 45.250μs      | 8.214μs      | 33.25% | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,275,192b | 17,146.600μs  | 20,339.820μs  | 20,271.373μs  | 26,539.300μs  | 2,747.209μs  | 13.51% | 823.27x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,243,936b | 21,130.700μs  | 26,316.200μs  | 24,040.710μs  | 45,508.000μs  | 6,815.499μs  | 25.90% | 1,065.17x  |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 3,029,632b | 17,328.700μs  | 19,601.060μs  | 18,353.480μs  | 23,417.800μs  | 2,064.396μs  | 10.53% | 793.37x    |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 2,605,560b | 257,290.900μs | 268,508.740μs | 260,911.613μs | 297,512.300μs | 14,117.695μs | 5.26%  | 10,868.16x |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 1,734,616b | 357.100μs     | 403.060μs     | 369.654μs     | 551.500μs     | 65.056μs     | 16.14% | 16.31x     |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
```

## The second case + non-static routes

```
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev       | rstdev | diff    |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 2,656,688b | 14,377.450μs  | 15,335.535μs  | 14,586.373μs  | 17,427.720μs  | 1,169.105μs | 7.62%  | 11.91x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 3,360,824b | 4,414.300μs   | 6,874.798μs   | 4,710.450μs   | 18,171.830μs  | 4,534.653μs | 65.96% | 5.34x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 3,157,360b | 40,450.500μs  | 45,353.040μs  | 42,672.966μs  | 54,646.500μs  | 4,782.835μs | 10.55% | 35.23x  |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 3,129,488b | 1,243.050μs   | 1,287.188μs   | 1,253.139μs   | 1,420.820μs   | 67.379μs    | 5.23%  | 1.00x   |
| MiladReactParamBench      | benchParam | 0   | 10   | 10  | 5,402,688b | 24,902.500μs  | 25,396.180μs  | 25,264.709μs  | 26,153.100μs  | 338.754μs   | 1.33%  | 19.73x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 4,625,032b | 52,426.000μs  | 53,136.740μs  | 53,193.426μs  | 53,674.900μs  | 369.031μs   | 0.69%  | 41.28x  |
| PowerReactParamBench      | benchParam | 0   | 10   | 10  | 3,029,632b | 17,787.400μs  | 18,722.020μs  | 18,676.215μs  | 19,720.100μs  | 475.076μs   | 2.54%  | 14.54x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 2,664,712b | 268,663.100μs | 273,656.960μs | 269,978.707μs | 288,435.900μs | 7,371.649μs | 2.69%  | 212.60x |
| ToroReactParamBench       | benchParam | 0   | 10   | 10  | 1,785,112b | 8,908.600μs   | 9,703.240μs   | 9,498.031μs   | 10,670.000μs  | 539.366μs   | 5.56%  | 7.54x   |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
```

As you can see in some cases Toro is the fastest router, in some cases it is not, but still shows good results.

# What's next?

More articles can be found in my [Twitter](https://twitter.com/mezonphp)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )