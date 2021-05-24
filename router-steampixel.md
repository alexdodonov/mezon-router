Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
RouteGenerator::generateSteampixelStaticRoutes(1000);
\Steampixel\Route::run('/static/0');

RouteGenerator::generateSteampixelStaticRoutes(1000);
\Steampixel\Route::run('/static/99');

RouteGenerator::generateSteampixelStaticRoutes(1000);
\Steampixel\Route::run('/static/199');
// and so on up to /static/999 route
```

```php
RouteGenerator::generateSteampixelNonStaticRoutes(1000);
\Steampixel\Route::run('/param/0/1');

RouteGenerator::generateSteampixelNonStaticRoutes(1000);
\Steampixel\Route::run('/param/99/1');

RouteGenerator::generateSteampixelNonStaticRoutes(1000);
\Steampixel\Route::run('/param/199/1');
// and so on up to /param/999/1 route
```

# The second case
```php
RouteGenerator::generateSteampixelNonStaticRoutes(1000);
\Steampixel\Route::run('/static/0');

\Steampixel\Route::run('/static/99');

\Steampixel\Route::run('/static/199');
// and so on up to /static/999 route
```

```php
RouteGenerator::generateSteampixelNonStaticRoutes(1000);
\Steampixel\Route::run('/param/0/1');

\Steampixel\Route::run('/param/99/1');

\Steampixel\Route::run('/param/199/1');
// and so on up to /param/999/1 route
```

# OK What do we have?

## The first case + static routes
```
+------------------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+--------+
| benchmark                          | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev       | rstdev | diff   |
+------------------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+--------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 2,721,192b | 41,307.500μs  | 45,096.590μs  | 42,128.908μs  | 61,295.100μs  | 6,320.995μs | 14.02% | 4.85x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,177,992b | 24,590.200μs  | 27,811.340μs  | 27,001.933μs  | 36,327.300μs  | 3,083.099μs | 11.09% | 2.99x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,780,328b | 43,449.100μs  | 43,772.210μs  | 43,661.100μs  | 44,222.900μs  | 235.195μs   | 0.54%  | 4.71x  |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 2,219,792b | 9,089.700μs   | 9,299.940μs   | 9,279.696μs   | 9,561.000μs   | 142.680μs   | 1.53%  | 1.00x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 6,947,552b | 34,829.400μs  | 35,706.170μs  | 35,652.755μs  | 36,724.600μs  | 474.942μs   | 1.33%  | 3.84x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,498,808b | 194,219.800μs | 196,558.930μs | 196,331.508μs | 200,720.300μs | 1,645.088μs | 0.84%  | 21.14x |
+------------------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+--------+
```

## The first case + non-static routes
```
+---------------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+--------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best         | mean         | mode         | worst        | stdev       | rstdev | diff   |
+---------------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+--------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 2,194,328b | 7,760.800μs  | 8,367.330μs  | 7,971.543μs  | 10,153.900μs | 778.770μs   | 9.31%  | 7.57x  |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 2,441,688b | 13,716.880μs | 15,382.193μs | 15,478.981μs | 17,333.080μs | 991.996μs   | 6.45%  | 13.91x |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 3,294,616b | 3,582.400μs  | 4,080.933μs  | 4,197.178μs  | 4,528.640μs  | 325.111μs   | 7.97%  | 3.69x  |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 3,061,904b | 1,019.620μs  | 1,105.630μs  | 1,059.608μs  | 1,265.810μs  | 76.777μs    | 6.94%  | 1.00x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 4,487,512b | 43,167.500μs | 45,641.740μs | 45,183.376μs | 51,542.400μs | 2,227.952μs | 4.88%  | 41.28x |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 2,256,032b | 17,826.400μs | 18,485.750μs | 18,245.010μs | 20,641.000μs | 777.997μs   | 4.21%  | 16.72x |
+---------------------------+------------+-----+------+-----+------------+--------------+--------------+--------------+--------------+-------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev       | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 2,194,360b | 7,992.400μs   | 8,278.480μs   | 8,161.593μs   | 9,130.000μs   | 317.905μs   | 3.84%  | 416.02x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,437,296b | 10,202.950μs  | 10,691.660μs  | 10,478.296μs  | 12,547.980μs  | 638.242μs   | 5.97%  | 537.30x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,826,400b | 3,585.590μs   | 3,725.367μs   | 3,616.349μs   | 4,167.740μs   | 189.042μs   | 5.07%  | 187.21x    |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 1,840,624b | 18.730μs      | 19.899μs      | 19.241μs      | 22.630μs      | 1.208μs     | 6.07%  | 1.00x      |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,106,416b | 18,972.800μs  | 19,696.340μs  | 19,639.540μs  | 20,493.800μs  | 425.149μs   | 2.16%  | 989.82x    |
| SteampixelReactParamBench  | benchStatic | 0   | 10   | 10  | 2,602,624b | 235,412.100μs | 239,436.830μs | 237,784.100μs | 252,016.100μs | 4,582.392μs | 1.91%  | 221.61x   |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
```

## The second case + non-static routes
```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 3,688,232b   | 51,588.400μs  | 52,322.470μs  | 52,054.399μs  | 53,208.300μs  | 535.329μs    | 1.02%  | 1.77x |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 3,170,376b   | 28,365.100μs  | 29,573.940μs  | 28,775.061μs  | 36,744.700μs  | 2,410.586μs  | 8.15%  | 1.00x |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,099,256b   | 45,535.800μs  | 46,021.020μs  | 45,784.508μs  | 46,996.600μs  | 432.767μs    | 0.94%  | 1.56x |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 139,441,144b | 122,050.900μs | 126,141.770μs | 124,773.830μs | 135,054.800μs | 3,791.321μs  | 3.01%  | 4.27x |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 6,531,736b   | 73,582.600μs  | 74,616.210μs  | 74,750.904μs  | 76,412.000μs  | 873.393μs    | 1.17%  | 2.52x |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 2,598,760b   | 219,314.600μs | 232,375.440μs | 226,123.319μs | 268,318.200μs | 14,198.405μs | 6.11%  | 7.86x |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
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
