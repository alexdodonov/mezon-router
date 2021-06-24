Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$router = RouteGenerator::generatePowerStaticRoutes(1000);
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$router = RouteGenerator::generatePowerStaticRoutes(1000);
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
$router = RouteGenerator::generatePowerStaticRoutes(1000);
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());
// and so on up to /static/999 route
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
$router = RouteGenerator::generatePowerNonStaticRoutes(1000);
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());
        
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
$router = RouteGenerator::generatePowerNonStaticRoutes(1000);
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());
        
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1/';
$router = RouteGenerator::generatePowerNonStaticRoutes(1000);
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$router = RouteGenerator::generatePowerStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generatePowerNonStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1/';
$request = ServerRequestFactory::fromGlobals();
$router->start($request, new Response());
// and so on up to /param/999/1 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                          | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 2,936,232b  | 41,057.300μs  | 84,148.370μs  | 47,592.655μs  | 249,780.200μs | 74,221.744μs | 88.20% | 48.66x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,392,992b  | 25,714.700μs  | 26,682.450μs  | 26,362.514μs  | 28,618.500μs  | 819.301μs    | 3.07%  | 15.43x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,847,864b  | 44,176.400μs  | 47,097.030μs  | 45,321.883μs  | 63,058.400μs  | 5,365.782μs  | 11.39% | 27.23x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 3,471,624b  | 126,073.700μs | 130,365.100μs | 128,767.356μs | 143,720.600μs | 4,833.922μs  | 3.71%  | 75.39x  |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 2,287,376b  | 9,073.900μs   | 9,357.890μs   | 9,297.606μs   | 9,896.300μs   | 236.985μs    | 2.53%  | 5.41x   |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 33,649,608b | 29,915.300μs  | 30,749.180μs  | 30,291.549μs  | 33,174.000μs  | 977.528μs    | 3.18%  | 17.78x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 7,085,072b  | 36,595.500μs  | 37,232.050μs  | 37,031.929μs  | 38,438.600μs  | 554.518μs    | 1.49%  | 21.53x  |
| PowerSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 3,862,216b  | 19,049.200μs  | 20,140.870μs  | 19,808.905μs  | 21,000.000μs  | 643.417μs    | 3.19%  | 11.65x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,566,392b  | 207,734.200μs | 210,574.660μs | 210,950.748μs | 212,526.200μs | 1,389.866μs  | 0.66%  | 121.77x |
| ToroSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 1,745,216b  | 1,476.200μs   | 1,729.290μs   | 1,583.368μs   | 2,666.700μs   | 346.981μs    | 20.06% | 1.00x   |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+---------------+--------+--------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev         | rstdev | diff   |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+---------------+--------+--------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 3,903,272b   | 53,137.000μs  | 53,669.140μs  | 53,434.041μs  | 54,304.600μs  | 398.649μs     | 0.74%  | 4.85x  |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 3,385,376b   | 29,081.100μs  | 30,110.190μs  | 29,726.930μs  | 31,490.000μs  | 755.289μs     | 2.51%  | 2.72x  |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,165,464b   | 45,786.400μs  | 46,825.120μs  | 46,833.564μs  | 47,952.800μs  | 583.437μs     | 1.25%  | 4.23x  |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 3,592,344b   | 145,910.100μs | 147,746.210μs | 147,306.262μs | 150,106.800μs | 1,239.601μs   | 0.84%  | 13.36x |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 139,508,728b | 124,171.600μs | 126,109.060μs | 125,425.730μs | 130,917.500μs | 1,915.883μs   | 1.52%  | 11.41x |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 34,508,144b  | 35,645.000μs  | 36,320.080μs  | 36,141.269μs  | 37,270.600μs  | 527.261μs     | 1.45%  | 3.28x  |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 6,669,256b   | 75,824.900μs  | 84,586.110μs  | 78,820.332μs  | 109,839.700μs | 10,653.230μs  | 12.59% | 7.65x  |
| PowerSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 3,862,216b   | 20,937.700μs  | 28,584.620μs  | 24,077.098μs  | 40,265.800μs  | 7,123.452μs   | 24.92% | 2.59x  |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 2,666,344b   | 235,446.700μs | 348,940.010μs | 297,900.248μs | 691,357.600μs | 128,878.867μs | 36.93% | 31.56x |
| ToroSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 1,866,176b   | 9,774.800μs   | 11,056.990μs  | 10,512.857μs  | 12,358.000μs  | 835.968μs     | 7.56%  | 1.00x  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+---------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+-----------------+---------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst           | stdev         | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+-----------------+---------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 2,409,400b | 7,916.900μs   | 8,434.210μs   | 8,309.219μs   | 9,401.900μs     | 425.183μs     | 5.04%  | 410.30x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,652,296b | 10,289.710μs  | 10,796.560μs  | 10,512.391μs  | 13,365.110μs    | 864.430μs     | 8.01%  | 525.23x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,893,936b | 3,576.030μs   | 3,622.525μs   | 3,606.191μs   | 3,731.710μs     | 43.214μs      | 1.19%  | 176.23x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 3,034,880b | 25,829.300μs  | 26,309.220μs  | 26,380.221μs  | 26,774.000μs    | 303.353μs     | 1.15%  | 1,279.88x  |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 1,908,208b | 18.630μs      | 20.556μs      | 18.987μs      | 25.150μs        | 2.436μs       | 11.85% | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,275,192b | 15,524.100μs  | 16,269.690μs  | 16,402.613μs  | 17,264.100μs    | 511.121μs     | 3.14%  | 791.48x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,243,936b | 19,101.900μs  | 20,167.700μs  | 19,701.218μs  | 22,467.300μs    | 1,012.717μs   | 5.02%  | 981.11x    |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 3,029,632b | 15,172.800μs  | 15,635.430μs  | 15,559.212μs  | 16,680.100μs    | 397.706μs     | 2.54%  | 760.63x    |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 2,605,560b | 239,026.100μs | 346,973.110μs | 250,424.922μs | 1,209,825.800μs | 288,217.061μs | 83.07% | 16,879.41x |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 1,734,616b | 351.800μs     | 382.130μs     | 364.151μs     | 480.600μs       | 39.107μs      | 10.23% | 18.59x     |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+-----------------+---------------+--------+------------+
```

## The second case + non-static routes

```
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 2,409,368b | 8,548.600μs   | 9,257.830μs   | 8,950.739μs   | 10,758.200μs  | 640.445μs    | 6.92%  | 6.19x   |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 2,656,688b | 14,344.960μs  | 18,446.126μs  | 16,842.581μs  | 31,828.310μs  | 4,840.866μs  | 26.24% | 12.34x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 3,360,824b | 3,862.030μs   | 4,972.977μs   | 4,750.023μs   | 6,237.760μs   | 730.301μs    | 14.69% | 3.33x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 3,157,360b | 43,412.000μs  | 59,080.640μs  | 54,258.725μs  | 95,701.400μs  | 14,431.920μs | 24.43% | 39.52x  |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 3,129,488b | 1,178.510μs   | 1,494.984μs   | 1,341.991μs   | 2,096.520μs   | 283.938μs    | 18.99% | 1.00x   |
| MiladReactParamBench      | benchParam | 0   | 10   | 10  | 5,402,688b | 22,573.900μs  | 28,562.900μs  | 24,596.006μs  | 49,765.900μs  | 8,124.021μs  | 28.44% | 19.11x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 4,625,032b | 47,438.500μs  | 53,523.160μs  | 49,062.308μs  | 71,146.100μs  | 8,897.750μs  | 16.62% | 35.80x  |
| PowerReactParamBench      | benchParam | 0   | 10   | 10  | 3,029,632b | 16,629.600μs  | 17,320.220μs  | 17,040.916μs  | 18,338.400μs  | 523.710μs    | 3.02%  | 11.59x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 2,664,712b | 250,206.800μs | 270,897.140μs | 256,660.290μs | 299,426.700μs | 16,652.239μs | 6.15%  | 181.20x |
| ToroReactParamBench       | benchParam | 0   | 10   | 10  | 1,785,112b | 8,329.400μs   | 8,866.510μs   | 8,599.075μs   | 9,795.400μs   | 478.527μs    | 5.40%  | 5.93x   |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

As you can see in some cases Power Router is the fastest router, in some cases it is not, but still shows good results.

# What's next?

More articles can be found in my [Twitter](https://twitter.com/mezonphp)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )