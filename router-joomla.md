Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$router = RouteGenerator::generateJoomlaStaticRoutes(1000);
$controller = $router->getController('/static/0');
$controller->execute();

$router = RouteGenerator::generateJoomlaStaticRoutes(1000);
$controller = $router->getController('/static/99');
$controller->execute();

$router = RouteGenerator::generateJoomlaStaticRoutes(1000);
$controller = $router->getController('/static/199');
$controller->execute();
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generateJoomlaNonStaticRoutes(1000);
$controller = $router->getController('/param/1/0');
$controller->execute();

$router = RouteGenerator::generateJoomlaNonStaticRoutes(1000);
$controller = $router->getController('/param/1/99');
$controller->execute();

$router = RouteGenerator::generateJoomlaNonStaticRoutes(1000);
$controller = $router->getController('/param/1/199');
$controller->execute();
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$router = RouteGenerator::generateJoomlaStaticRoutes(1000);

$controller = $router->getController('/static/0');
$controller->execute();

$controller = $router->getController('/static/99');
$controller->execute();

$controller = $router->getController('/static/199');
$controller->execute();
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generateJoomlaNonStaticRoutes(1000);
$controller = $router->getController('/param/1/0');
$controller->execute();

$controller = $router->getController('/param/1/99');
$controller->execute();

$controller = $router->getController('/param/1/199');
$controller->execute();
// and so on up to /param/1/999 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                          | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,404,528b  | 43,534.600μs  | 50,177.330μs  | 48,991.098μs  | 63,594.100μs  | 5,279.265μs  | 10.52% | 30.05x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 4,861,368b  | 27,343.200μs  | 30,732.450μs  | 29,235.008μs  | 43,455.100μs  | 4,499.777μs  | 14.64% | 18.41x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 5,315,736b  | 54,316.500μs  | 67,348.990μs  | 63,661.563μs  | 91,917.500μs  | 11,210.166μs | 16.64% | 40.34x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 5,103,248b  | 130,910.100μs | 137,524.200μs | 138,438.581μs | 144,848.700μs | 4,057.682μs  | 2.95%  | 82.37x  |
| JoomlaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b  | 29,621.600μs  | 31,002.440μs  | 30,482.219μs  | 33,317.200μs  | 1,111.062μs  | 3.58%  | 18.57x  |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 4,398,688b  | 9,334.800μs   | 10,106.980μs  | 10,422.629μs  | 10,853.600μs  | 516.065μs    | 5.11%  | 6.05x   |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 35,117,472b | 30,258.300μs  | 31,278.410μs  | 30,875.237μs  | 32,388.400μs  | 691.956μs    | 2.21%  | 18.73x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 8,553,280b  | 37,762.400μs  | 41,376.510μs  | 39,392.781μs  | 50,579.700μs  | 3,874.561μs  | 9.36%  | 24.78x  |
| PowerSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 5,330,424b  | 21,602.200μs  | 22,994.960μs  | 22,551.948μs  | 25,547.900μs  | 1,118.206μs  | 4.86%  | 13.77x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 4,398,312b  | 216,991.400μs | 225,593.170μs | 220,035.646μs | 242,493.200μs | 8,941.942μs  | 3.96%  | 135.12x |
| ToroSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 4,398,280b  | 1,544.800μs   | 1,669.560μs   | 1,590.195μs   | 2,072.000μs   | 167.083μs    | 10.01% | 1.00x   |
| ZaphpaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,904,896b  | 34,739.200μs  | 36,445.770μs  | 35,516.025μs  | 44,917.600μs  | 2,848.675μs  | 7.82%  | 21.83x  |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,371,568b   | 56,033.600μs  | 62,292.880μs  | 58,345.649μs  | 95,415.500μs  | 11,334.701μs | 18.20% | 5.38x  |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,853,752b   | 29,971.800μs  | 32,257.860μs  | 31,250.002μs  | 35,602.500μs  | 1,829.865μs  | 5.67%  | 2.78x  |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 5,517,416b   | 48,028.800μs  | 48,611.330μs  | 48,383.815μs  | 49,480.100μs  | 481.693μs    | 0.99%  | 4.20x  |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 5,223,968b   | 149,366.900μs | 153,438.580μs | 151,147.066μs | 159,944.400μs | 3,813.778μs  | 2.49%  | 13.25x |
| JoomlaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,079,144b   | 37,517.200μs  | 38,497.920μs  | 37,859.269μs  | 40,944.600μs  | 1,096.360μs  | 2.85%  | 3.32x  |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 141,043,216b | 124,792.900μs | 129,990.350μs | 128,120.897μs | 147,169.300μs | 6,082.533μs  | 4.68%  | 11.22x |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 35,976,008b  | 36,017.500μs  | 39,203.520μs  | 37,774.049μs  | 51,493.300μs  | 4,231.052μs  | 10.79% | 3.38x  |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 8,137,464b   | 78,652.200μs  | 83,966.830μs  | 80,641.855μs  | 113,711.300μs | 9,985.513μs  | 11.89% | 7.25x  |
| PowerSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 5,330,424b   | 20,859.500μs  | 26,459.820μs  | 22,814.000μs  | 33,501.900μs  | 4,785.089μs  | 18.08% | 2.28x  |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 4,398,312b   | 247,148.100μs | 258,473.440μs | 255,021.572μs | 278,096.900μs | 8,710.483μs  | 3.37%  | 22.32x |
| ToroSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 4,398,280b   | 9,239.200μs   | 11,582.900μs  | 10,480.060μs  | 15,709.400μs  | 2,140.567μs  | 18.48% | 1.00x  |
| ZaphpaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,008,304b   | 47,485.900μs  | 54,025.800μs  | 49,874.424μs  | 62,742.600μs  | 5,420.759μs  | 10.03% | 4.66x  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+-----------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff      |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+-----------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b | 8,523.700μs   | 10,449.440μs  | 9,612.044μs   | 15,475.500μs  | 2,020.365μs  | 19.33% | 277.63x   |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,398,280b | 12,227.090μs  | 17,322.570μs  | 15,671.005μs  | 23,580.900μs  | 3,599.847μs  | 20.78% | 460.24x   |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,398,264b | 4,105.160μs   | 5,525.071μs   | 4,952.101μs   | 9,196.770μs   | 1,450.048μs  | 26.24% | 146.80x   |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 4,666,504b | 28,153.900μs  | 51,272.790μs  | 44,242.506μs  | 81,194.400μs  | 17,183.030μs | 33.51% | 1,362.26x |
| JoomlaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b | 6,186.200μs   | 7,030.880μs   | 6,705.301μs   | 8,554.600μs   | 710.474μs    | 10.11% | 186.80x   |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 4,398,688b | 19.220μs      | 37.638μs      | 24.863μs      | 65.730μs      | 16.569μs     | 44.02% | 1.00x     |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 6,743,056b | 18,441.000μs  | 23,391.940μs  | 19,809.413μs  | 35,496.100μs  | 6,099.798μs  | 26.08% | 621.50x   |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,712,144b | 22,390.000μs  | 28,629.560μs  | 26,450.778μs  | 48,993.300μs  | 7,090.284μs  | 24.77% | 760.66x   |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,497,840b | 17,450.400μs  | 19,512.030μs  | 18,807.150μs  | 21,923.300μs  | 1,507.499μs  | 7.73%  | 518.41x   |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 4,398,280b | 286,389.500μs | 337,075.230μs | 307,103.881μs | 511,603.300μs | 66,116.975μs | 19.61% | 8,955.72x |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 4,398,280b | 424.200μs     | 719.480μs     | 795.556μs     | 1,186.300μs   | 230.550μs    | 32.04% | 19.12x    |
| ZaphpaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b | 14,775.000μs  | 20,191.100μs  | 17,568.654μs  | 31,374.500μs  | 5,021.912μs  | 24.87% | 536.46x   |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+-----------+
```

## The second case + non-static routes

```
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 4,398,280b | 9,245.400μs   | 20,334.480μs  | 13,207.514μs  | 42,989.400μs  | 11,875.763μs | 58.40% | 15.15x  |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 4,398,280b | 14,468.990μs  | 15,908.489μs  | 15,218.939μs  | 22,133.470μs  | 2,152.343μs  | 13.53% | 11.86x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 4,712,776b | 3,896.900μs   | 6,163.972μs   | 4,777.923μs   | 12,724.400μs  | 2,672.981μs  | 43.36% | 4.59x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 4,788,984b | 37,337.300μs  | 39,048.190μs  | 38,665.748μs  | 42,599.600μs  | 1,415.728μs  | 3.63%  | 29.10x  |
| JoomlaReactParamBench     | benchParam | 0   | 10   | 10  | 4,398,280b | 4,051.800μs   | 5,954.850μs   | 4,927.056μs   | 14,216.700μs  | 2,899.302μs  | 48.69% | 4.44x   |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 4,663,336b | 1,188.000μs   | 1,341.824μs   | 1,360.385μs   | 1,558.120μs   | 103.731μs    | 7.73%  | 1.00x   |
| MiladReactParamBench      | benchParam | 0   | 10   | 10  | 6,870,552b | 22,524.700μs  | 27,019.990μs  | 28,513.528μs  | 30,371.600μs  | 2,631.604μs  | 9.74%  | 20.14x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 6,093,240b | 49,391.600μs  | 56,062.640μs  | 57,623.171μs  | 61,873.300μs  | 4,064.076μs  | 7.25%  | 41.78x  |
| PowerReactParamBench      | benchParam | 0   | 10   | 10  | 4,497,840b | 16,589.900μs  | 18,423.650μs  | 17,683.516μs  | 24,689.000μs  | 2,219.289μs  | 12.05% | 13.73x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 4,398,280b | 251,958.800μs | 326,234.640μs | 266,491.670μs | 537,585.600μs | 98,733.433μs | 30.26% | 243.13x |
| ToroReactParamBench       | benchParam | 0   | 10   | 10  | 4,398,264b | 7,915.400μs   | 9,308.860μs   | 8,484.868μs   | 11,421.400μs  | 1,177.174μs  | 12.65% | 6.94x   |
| ZaphpaReactParamBench     | benchParam | 0   | 10   | 10  | 4,404,680b | 24,087.100μs  | 25,874.300μs  | 26,509.111μs  | 27,204.600μs  | 967.386μs    | 3.74%  | 19.28x  |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

As you can see almost in all cases Mezon Router is faster that Joomla router.

# What's next?

More articles can be found in my [Twitter](https://twitter.com/mezonphp)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )