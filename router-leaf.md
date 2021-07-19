# Intro

Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$router = RouteGenerator::generateTetoStaticRoutes(1000);
$router->match('GET', '/static/0');

$router = RouteGenerator::generateTetoStaticRoutes(1000);
$router->match('GET', '/static/99');

$router = RouteGenerator::generateTetoStaticRoutes(1000);
$router->match('GET', '/static/199');
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generateTetoNonStaticRoutes(1000);
$router->match('GET', '/param/0/1');

$router = RouteGenerator::generateTetoNonStaticRoutes(1000);
$router->match('GET', '/param/99/1');

$router = RouteGenerator::generateTetoNonStaticRoutes(1000);
$router->match('GET', '/param/199/1');
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$router = RouteGenerator::generateTetoStaticRoutes(1000);

$router->match('GET', '/static/0');

$router->match('GET', '/static/99');

$router->match('GET', '/static/199');
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generateTetoNonStaticRoutes(1000);

$router->match('GET', '/param/0/1');

$router->match('GET', '/param/99/1');

$router->match('GET', '/param/199/1');
// and so on up to /param/1/999 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                          | subject     | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+------------------------------------+-------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,416,760b   | 47,939.100μs  | 57,073.950μs  | 52,448.314μs  | 92,250.800μs  | 12,711.502μs | 22.27% | 29.40x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 4,873,600b   | 29,406.000μs  | 31,359.430μs  | 30,224.780μs  | 33,764.300μs  | 1,578.558μs  | 5.03%  | 16.15x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 5,327,968b   | 50,925.400μs  | 55,086.900μs  | 52,131.973μs  | 69,609.000μs  | 5,744.275μs  | 10.43% | 28.38x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 5,115,480b   | 144,288.000μs | 156,082.490μs | 149,775.315μs | 179,781.900μs | 11,638.906μs | 7.46%  | 80.40x  |
| JoomlaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,402,888b   | 34,357.600μs  | 37,628.510μs  | 35,417.780μs  | 43,386.800μs  | 3,418.865μs  | 9.09%  | 19.38x  |
| LeafSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 152,263,656b | 35,078.800μs  | 38,101.440μs  | 37,164.095μs  | 43,742.100μs  | 2,487.527μs  | 6.53%  | 19.63x  |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 4,403,296b   | 10,426.900μs  | 11,419.360μs  | 11,486.902μs  | 12,418.300μs  | 619.609μs    | 5.43%  | 5.88x   |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 35,129,704b  | 34,830.900μs  | 36,623.310μs  | 37,527.545μs  | 37,886.300μs  | 1,128.127μs  | 3.08%  | 18.87x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 8,565,512b   | 44,259.500μs  | 47,010.690μs  | 46,139.941μs  | 49,370.700μs  | 1,646.278μs  | 3.50%  | 24.22x  |
| PowerSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 5,342,656b   | 23,509.500μs  | 27,317.570μs  | 26,116.556μs  | 37,681.900μs  | 3,827.772μs  | 14.01% | 14.07x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 4,402,920b   | 245,642.000μs | 252,848.030μs | 251,697.069μs | 266,980.900μs | 5,706.743μs  | 2.26%  | 130.25x |
| TetoSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 6,760,328b   | 49,796.100μs  | 52,161.840μs  | 50,862.160μs  | 54,660.000μs  | 1,743.920μs  | 3.34%  | 26.87x  |
| ToroSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 4,402,888b   | 1,598.300μs   | 1,941.310μs   | 1,947.188μs   | 2,284.000μs   | 178.139μs    | 9.18%  | 1.00x   |
| ZaphpaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,917,128b   | 39,126.200μs  | 41,894.490μs  | 40,677.525μs  | 50,615.000μs  | 3,206.580μs  | 7.65%  | 21.58x  |
+------------------------------------+-------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,383,800b   | 64,149.900μs  | 68,643.870μs  | 66,966.558μs  | 77,000.900μs  | 3,921.354μs  | 5.71%  | 5.62x  |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,865,984b   | 35,881.000μs  | 38,117.380μs  | 36,357.450μs  | 42,288.000μs  | 2,323.897μs  | 6.10%  | 3.12x  |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 5,529,648b   | 55,389.700μs  | 59,949.570μs  | 58,228.467μs  | 69,895.800μs  | 4,100.430μs  | 6.84%  | 4.91x  |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 5,236,200b   | 174,096.700μs | 183,709.660μs | 182,310.005μs | 200,004.100μs | 6,536.611μs  | 3.56%  | 15.05x |
| JoomlaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,091,376b   | 43,591.300μs  | 48,020.070μs  | 44,911.551μs  | 75,717.400μs  | 9,302.832μs  | 19.37% | 3.93x  |
| LeafSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 152,273,112b | 40,056.100μs  | 46,651.370μs  | 43,610.004μs  | 65,999.600μs  | 7,444.629μs  | 15.96% | 3.82x  |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 141,056,056b | 160,933.400μs | 172,964.680μs | 167,216.711μs | 199,617.400μs | 12,057.579μs | 6.97%  | 14.17x |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 35,988,240b  | 42,030.700μs  | 47,074.700μs  | 43,814.086μs  | 72,407.700μs  | 8,792.596μs  | 18.68% | 3.86x  |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 8,149,696b   | 90,550.200μs  | 96,066.750μs  | 93,528.156μs  | 110,313.000μs | 5,935.073μs  | 6.18%  | 7.87x  |
| PowerSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 5,342,656b   | 24,573.500μs  | 26,827.200μs  | 25,561.859μs  | 31,686.900μs  | 2,271.591μs  | 8.47%  | 2.20x  |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 4,402,920b   | 270,994.300μs | 280,615.200μs | 283,062.232μs | 289,402.400μs | 6,105.135μs  | 2.18%  | 22.99x |
| TetoSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 6,721,408b   | 66,515.100μs  | 69,275.460μs  | 70,647.850μs  | 72,914.600μs  | 2,245.638μs  | 3.24%  | 5.67x  |
| ToroSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 4,402,888b   | 10,670.000μs  | 12,207.530μs  | 11,418.532μs  | 14,782.900μs  | 1,274.212μs  | 10.44% | 1.00x  |
| ZaphpaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,020,536b   | 53,622.100μs  | 63,750.200μs  | 57,301.614μs  | 79,378.700μs  | 9,849.251μs  | 15.45% | 5.22x  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,402,888b  | 10,034.700μs  | 11,286.760μs  | 10,773.621μs  | 14,051.600μs  | 1,197.867μs  | 10.61% | 496.03x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,402,888b  | 13,148.010μs  | 13,771.349μs  | 13,981.083μs  | 14,271.230μs  | 374.943μs    | 2.72%  | 605.23x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,402,872b  | 4,298.550μs   | 4,763.865μs   | 4,589.042μs   | 5,725.870μs   | 416.916μs    | 8.75%  | 209.36x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 4,678,736b  | 30,335.300μs  | 32,897.550μs  | 33,364.751μs  | 34,822.400μs  | 1,411.764μs  | 4.29%  | 1,445.79x  |
| JoomlaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,402,888b  | 6,436.100μs   | 7,693.700μs   | 7,049.448μs   | 10,506.500μs  | 1,209.691μs  | 15.72% | 338.13x    |
| LeafReactStaticBench       | benchStatic | 0   | 10   | 10  | 17,027,288b | 9,771.700μs   | 10,912.610μs  | 10,306.522μs  | 14,403.800μs  | 1,344.440μs  | 12.32% | 479.59x    |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 4,403,296b  | 18.780μs      | 22.754μs      | 19.670μs      | 47.190μs      | 8.421μs      | 37.01% | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 6,755,288b  | 17,780.200μs  | 19,608.880μs  | 18,943.244μs  | 22,691.900μs  | 1,404.706μs  | 7.16%  | 861.78x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,724,376b  | 22,200.700μs  | 23,723.330μs  | 23,272.309μs  | 25,580.900μs  | 1,023.059μs  | 4.31%  | 1,042.60x  |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,510,072b  | 18,329.000μs  | 19,799.810μs  | 19,385.573μs  | 21,834.900μs  | 1,008.011μs  | 5.09%  | 870.17x    |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 4,402,888b  | 278,668.000μs | 294,515.740μs | 288,365.878μs | 351,544.700μs | 19,453.272μs | 6.61%  | 12,943.47x |
| TetoReactStaticBench       | benchStatic | 0   | 10   | 10  | 5,233,440b  | 4,675.600μs   | 5,433.200μs   | 5,129.949μs   | 6,929.700μs   | 654.188μs    | 12.04% | 238.78x    |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 4,402,888b  | 397.800μs     | 528.510μs     | 467.933μs     | 796.000μs     | 122.898μs    | 23.25% | 23.23x     |
| ZaphpaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,402,888b  | 14,429.300μs  | 15,781.300μs  | 16,399.930μs  | 17,093.300μs  | 852.563μs    | 5.40%  | 693.56x    |
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
```

## The second case + non-static routes

```
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,402,888b  | 10,034.700μs  | 11,286.760μs  | 10,773.621μs  | 14,051.600μs  | 1,197.867μs  | 10.61% | 496.03x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,402,888b  | 13,148.010μs  | 13,771.349μs  | 13,981.083μs  | 14,271.230μs  | 374.943μs    | 2.72%  | 605.23x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,402,872b  | 4,298.550μs   | 4,763.865μs   | 4,589.042μs   | 5,725.870μs   | 416.916μs    | 8.75%  | 209.36x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 4,678,736b  | 30,335.300μs  | 32,897.550μs  | 33,364.751μs  | 34,822.400μs  | 1,411.764μs  | 4.29%  | 1,445.79x  |
| JoomlaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,402,888b  | 6,436.100μs   | 7,693.700μs   | 7,049.448μs   | 10,506.500μs  | 1,209.691μs  | 15.72% | 338.13x    |
| LeafReactStaticBench       | benchStatic | 0   | 10   | 10  | 17,027,288b | 9,771.700μs   | 10,912.610μs  | 10,306.522μs  | 14,403.800μs  | 1,344.440μs  | 12.32% | 479.59x    |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 4,403,296b  | 18.780μs      | 22.754μs      | 19.670μs      | 47.190μs      | 8.421μs      | 37.01% | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 6,755,288b  | 17,780.200μs  | 19,608.880μs  | 18,943.244μs  | 22,691.900μs  | 1,404.706μs  | 7.16%  | 861.78x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,724,376b  | 22,200.700μs  | 23,723.330μs  | 23,272.309μs  | 25,580.900μs  | 1,023.059μs  | 4.31%  | 1,042.60x  |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,510,072b  | 18,329.000μs  | 19,799.810μs  | 19,385.573μs  | 21,834.900μs  | 1,008.011μs  | 5.09%  | 870.17x    |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 4,402,888b  | 278,668.000μs | 294,515.740μs | 288,365.878μs | 351,544.700μs | 19,453.272μs | 6.61%  | 12,943.47x |
| TetoReactStaticBench       | benchStatic | 0   | 10   | 10  | 5,233,440b  | 4,675.600μs   | 5,433.200μs   | 5,129.949μs   | 6,929.700μs   | 654.188μs    | 12.04% | 238.78x    |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 4,402,888b  | 397.800μs     | 528.510μs     | 467.933μs     | 796.000μs     | 122.898μs    | 23.25% | 23.23x     |
| ZaphpaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,402,888b  | 14,429.300μs  | 15,781.300μs  | 16,399.930μs  | 17,093.300μs  | 852.563μs    | 5.40%  | 693.56x    |
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
```

As you can see almost in all cases Mezon Router is faster than Teto router.

# What's next?

More articles can be found in my [Twitter](https://twitter.com/mezonphp)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )