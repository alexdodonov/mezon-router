Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$router = RouteGenerator::generateMiladRahimiStaticRoutes(1000);
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$router = RouteGenerator::generateMiladRahimiStaticRoutes(1000);
$router->dispatch();
// and so on up to /static/999 route
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
$router = RouteGenerator::generateMiladRahimiNonStaticRoutes(1000);
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
$router = RouteGenerator::generateMiladRahimiNonStaticRoutes(1000);
$router->dispatch();
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$router = RouteGenerator::generateMiladRahimiStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$router->dispatch();
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generateMiladRahimiNonStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
$router->dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
$router->dispatch();
// and so on up to /param/999/1 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                          | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 2,741,016b  | 47,112.700μs  | 62,695.970μs  | 59,485.341μs  | 79,206.200μs  | 9,666.062μs  | 15.42% | 6.15x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,197,816b  | 26,208.800μs  | 27,503.620μs  | 26,830.335μs  | 29,353.400μs  | 1,109.627μs  | 4.03%  | 2.70x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,800,152b  | 44,694.700μs  | 60,916.820μs  | 52,866.997μs  | 89,120.700μs  | 14,039.273μs | 23.05% | 5.98x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 3,424,056b  | 126,917.000μs | 139,632.130μs | 132,531.452μs | 159,519.100μs | 11,014.604μs | 7.89%  | 13.70x |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 2,239,616b  | 9,264.400μs   | 10,190.590μs  | 9,738.974μs   | 11,989.200μs  | 837.591μs    | 8.22%  | 1.00x  |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 33,601,920b | 29,279.800μs  | 31,601.350μs  | 31,489.528μs  | 35,416.600μs  | 1,715.305μs  | 5.43%  | 3.10x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 6,969,928b  | 37,848.600μs  | 64,492.240μs  | 46,630.792μs  | 147,304.700μs | 35,174.742μs | 54.54% | 6.33x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,518,632b  | 230,658.400μs | 293,791.750μs | 262,408.102μs | 419,310.700μs | 58,407.785μs | 19.88% | 28.83x |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 3,708,056b   | 54,737.000μs  | 71,144.790μs  | 61,905.063μs  | 148,657.000μs | 26,780.990μs | 37.64% | 1.83x |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 3,190,200b   | 32,130.400μs  | 50,958.200μs  | 40,602.868μs  | 93,108.300μs  | 20,634.926μs | 40.49% | 1.31x |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,117,752b   | 47,046.500μs  | 67,732.570μs  | 51,314.008μs  | 141,859.400μs | 31,665.712μs | 46.75% | 1.74x |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 3,544,776b   | 145,808.600μs | 148,708.560μs | 147,438.784μs | 158,824.600μs | 3,634.124μs  | 2.44%  | 3.82x |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 139,460,968b | 124,914.100μs | 140,483.640μs | 127,179.713μs | 161,093.100μs | 14,608.708μs | 10.40% | 3.61x |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 34,460,456b  | 37,966.300μs  | 38,921.900μs  | 38,906.617μs  | 40,150.400μs  | 716.326μs    | 1.84%  | 1.00x |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 6,554,112b   | 74,651.100μs  | 82,464.890μs  | 83,458.613μs  | 88,289.400μs  | 4,019.246μs  | 4.87%  | 2.12x |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 2,618,584b   | 234,231.600μs | 267,985.740μs | 263,439.623μs | 346,451.900μs | 31,573.175μs | 11.78% | 6.89x |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+-------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev       | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 2,214,184b | 8,156.100μs   | 9,396.030μs   | 8,816.173μs   | 13,873.000μs  | 1,608.807μs | 17.12% | 433.58x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,457,120b | 11,503.870μs  | 13,462.960μs  | 12,640.212μs  | 16,076.080μs  | 1,499.053μs | 11.13% | 621.24x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,846,224b | 4,067.170μs   | 4,562.523μs   | 4,705.931μs   | 5,144.420μs   | 380.744μs   | 8.35%  | 210.54x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 2,987,312b | 25,897.300μs  | 33,881.720μs  | 31,778.473μs  | 45,286.200μs  | 5,814.007μs | 17.16% | 1,563.46x  |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 1,860,448b | 19.400μs      | 21.671μs      | 20.171μs      | 26.830μs      | 2.598μs     | 11.99% | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,227,504b | 17,020.200μs  | 19,993.190μs  | 18,440.477μs  | 24,995.600μs  | 2,812.314μs | 14.07% | 922.58x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,128,792b | 19,366.900μs  | 26,279.440μs  | 22,529.184μs  | 40,629.100μs  | 6,498.058μs | 24.73% | 1,212.65x  |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 2,557,800b | 258,076.500μs | 272,176.120μs | 279,266.418μs | 286,874.500μs | 9,702.268μs | 3.56%  | 12,559.46x |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
```

## The second case + non-static routes

```
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 2,214,152b | 11,304.700μs  | 18,718.880μs  | 18,607.391μs  | 26,231.400μs  | 4,362.939μs  | 23.31% | 15.60x  |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 2,461,512b | 14,052.420μs  | 15,622.854μs  | 14,790.301μs  | 23,029.970μs  | 2,523.494μs  | 16.15% | 13.02x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 3,313,112b | 3,834.310μs   | 4,210.104μs   | 4,062.671μs   | 4,783.030μs   | 276.453μs    | 6.57%  | 3.51x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 3,109,792b | 35,823.700μs  | 37,256.740μs  | 36,620.287μs  | 39,133.100μs  | 1,054.223μs  | 2.83%  | 31.04x  |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 3,081,728b | 1,106.930μs   | 1,200.311μs   | 1,154.644μs   | 1,614.890μs   | 141.342μs    | 11.78% | 1.00x   |
| MiladReactParamBench      | benchParam | 0   | 10   | 10  | 5,355,000b | 20,670.100μs  | 29,461.560μs  | 23,488.165μs  | 80,671.400μs  | 17,366.238μs | 58.95% | 24.54x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 4,509,888b | 45,862.100μs  | 49,709.130μs  | 48,459.177μs  | 59,404.000μs  | 3,780.013μs  | 7.60%  | 41.41x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 2,616,952b | 244,598.100μs | 269,046.140μs | 256,454.606μs | 324,317.500μs | 23,733.551μs | 8.82%  | 224.15x |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
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