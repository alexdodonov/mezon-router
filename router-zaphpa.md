Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$router = RouteGenerator::generateZaphpaStaticRoutes(1000);
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$router = RouteGenerator::generateZaphpaStaticRoutes(1000);
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
$router = RouteGenerator::generateZaphpaStaticRoutes(1000);
$router->route();
// and so on up to /static/999 route
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
$router = RouteGenerator::generateZaphpaNonStaticRoutes(1000);
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
$router = RouteGenerator::generateZaphpaNonStaticRoutes(1000);
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1/';
$router = RouteGenerator::generateZaphpaNonStaticRoutes(1000);
$router->route();
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$router = RouteGenerator::generateZaphpaStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
$router->route();
// and so on up to /static/999 route
```

```php
// non static routes
$router = RouteGenerator::generateZaphpaNonStaticRoutes(1000);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1/';
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1/';
$router->route();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1/';
$router->route();
// and so on up to /param/999/1 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                          | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 2,940,952b  | 42,735.700μs  | 44,148.740μs  | 43,823.046μs  | 45,839.800μs  | 890.843μs    | 2.02%  | 11.74x |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,397,712b  | 25,728.100μs  | 26,532.410μs  | 26,713.614μs  | 27,142.700μs  | 408.093μs    | 1.54%  | 7.05x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 3,852,584b  | 45,056.000μs  | 47,831.220μs  | 45,880.336μs  | 51,961.500μs  | 2,559.187μs  | 5.35%  | 12.72x |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 3,476,344b  | 127,911.800μs | 131,211.200μs | 130,371.554μs | 136,347.600μs | 2,443.760μs  | 1.86%  | 34.88x |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 2,292,096b  | 9,211.200μs   | 9,625.020μs   | 9,471.161μs   | 10,096.800μs  | 291.190μs    | 3.03%  | 2.56x  |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 33,654,328b | 29,808.400μs  | 31,162.020μs  | 30,789.082μs  | 32,980.100μs  | 983.367μs    | 3.16%  | 8.28x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 7,089,792b  | 37,428.900μs  | 39,933.580μs  | 38,535.194μs  | 43,256.900μs  | 2,055.144μs  | 5.15%  | 10.62x |
| PowerSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 3,866,936b  | 19,369.400μs  | 21,803.010μs  | 20,382.670μs  | 33,752.200μs  | 4,076.400μs  | 18.70% | 5.80x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 2,571,112b  | 213,552.900μs | 250,146.770μs | 269,120.880μs | 280,522.800μs | 23,116.598μs | 9.24%  | 66.50x |
| ToroSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 1,749,936b  | 1,694.300μs   | 3,761.660μs   | 2,360.575μs   | 6,628.600μs   | 1,878.398μs  | 49.94% | 1.00x  |
| ZaphpaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 3,441,392b  | 38,126.200μs  | 64,181.420μs  | 55,310.419μs  | 97,458.200μs  | 19,113.623μs | 29.78% | 17.06x |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 3,907,992b   | 54,855.700μs  | 64,850.750μs  | 60,320.435μs  | 94,186.400μs  | 11,398.296μs | 17.58% | 3.72x  |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 3,390,096b   | 29,949.400μs  | 34,775.460μs  | 32,276.041μs  | 46,015.800μs  | 5,177.218μs  | 14.89% | 1.99x  |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,170,184b   | 47,621.600μs  | 48,482.750μs  | 48,300.887μs  | 49,846.700μs  | 650.654μs    | 1.34%  | 2.78x  |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 3,597,064b   | 148,953.500μs | 152,604.200μs | 152,200.110μs | 158,433.600μs | 2,434.881μs  | 1.60%  | 8.75x  |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 139,513,448b | 128,445.400μs | 146,859.660μs | 135,077.748μs | 222,587.900μs | 27,770.802μs | 18.91% | 8.42x  |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 34,512,864b  | 35,955.200μs  | 37,055.130μs  | 36,607.349μs  | 38,904.300μs  | 940.858μs    | 2.54%  | 2.13x  |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 6,673,976b   | 77,843.800μs  | 79,267.680μs  | 78,578.377μs  | 81,225.500μs  | 1,123.203μs  | 1.42%  | 4.55x  |
| PowerSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 3,866,936b   | 20,282.400μs  | 23,715.390μs  | 21,660.704μs  | 40,997.500μs  | 5,939.511μs  | 25.04% | 1.36x  |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 2,671,064b   | 266,386.600μs | 347,398.400μs | 301,574.887μs | 440,961.500μs | 62,212.096μs | 17.91% | 19.92x |
| ToroSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 1,870,896b   | 10,984.400μs  | 17,435.850μs  | 15,213.384μs  | 29,775.800μs  | 5,613.780μs  | 32.20% | 1.00x  |
| ZaphpaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 3,544,800b   | 50,162.200μs  | 86,169.690μs  | 84,714.534μs  | 130,054.700μs | 25,618.593μs | 29.73% | 4.94x  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev       | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 2,414,120b | 8,009.000μs   | 8,518.490μs   | 8,405.855μs   | 9,352.000μs   | 378.483μs   | 4.44%  | 381.38x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,657,016b | 10,541.570μs  | 10,995.686μs  | 10,762.513μs  | 12,333.660μs  | 518.953μs   | 4.72%  | 492.29x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 2,898,656b | 3,578.440μs   | 3,676.794μs   | 3,664.041μs   | 3,811.110μs   | 62.987μs    | 1.71%  | 164.61x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 3,039,600b | 26,708.000μs  | 27,240.900μs  | 27,081.392μs  | 28,411.600μs  | 450.931μs   | 1.66%  | 1,219.60x  |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 1,912,928b | 19.100μs      | 22.336μs      | 20.502μs      | 34.340μs      | 4.461μs     | 19.97% | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,279,912b | 15,713.200μs  | 17,850.040μs  | 16,708.343μs  | 22,679.200μs  | 2,329.194μs | 13.05% | 799.16x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,248,656b | 18,801.800μs  | 20,019.940μs  | 19,824.268μs  | 21,243.300μs  | 711.830μs   | 3.56%  | 896.31x    |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 3,034,352b | 15,840.300μs  | 17,689.340μs  | 16,348.676μs  | 24,220.300μs  | 2,739.784μs | 15.49% | 791.97x    |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 2,610,280b | 245,022.200μs | 253,592.850μs | 248,838.780μs | 265,128.100μs | 7,135.987μs | 2.81%  | 11,353.55x |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 1,739,768b | 359.900μs     | 395.980μs     | 370.684μs     | 564.000μs     | 60.614μs    | 15.31% | 17.73x     |
| ZaphpaReactStaticBench     | benchStatic | 0   | 10   | 10  | 2,884,936b | 11,746.100μs  | 12,658.930μs  | 12,680.628μs  | 13,220.000μs  | 372.641μs   | 2.94%  | 566.75x    |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+------------+
```

## The second case + non-static routes

```
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+---------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev         | rstdev | diff    |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+---------------+--------+---------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 2,414,088b | 8,113.000μs   | 8,640.060μs   | 8,488.276μs   | 9,144.000μs   | 309.602μs     | 3.58%  | 7.94x   |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 2,661,408b | 13,084.360μs  | 13,385.467μs  | 13,300.446μs  | 13,996.920μs  | 248.691μs     | 1.86%  | 12.29x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 3,365,544b | 3,756.650μs   | 4,278.759μs   | 3,844.301μs   | 6,113.990μs   | 854.762μs     | 19.98% | 3.93x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 3,162,080b | 34,718.000μs  | 36,019.680μs  | 35,490.992μs  | 38,965.300μs  | 1,184.278μs   | 3.29%  | 33.08x  |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 3,134,208b | 1,029.700μs   | 1,088.736μs   | 1,103.231μs   | 1,133.210μs   | 33.768μs      | 3.10%  | 1.00x   |
| MiladReactParamBench      | benchParam | 0   | 10   | 10  | 5,407,408b | 20,613.300μs  | 21,961.730μs  | 21,229.817μs  | 28,489.300μs  | 2,210.792μs   | 10.07% | 20.17x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 4,629,752b | 44,592.000μs  | 57,865.700μs  | 52,179.807μs  | 105,175.900μs | 16,653.134μs  | 28.78% | 53.15x  |
| PowerReactParamBench      | benchParam | 0   | 10   | 10  | 3,034,352b | 15,865.000μs  | 35,597.940μs  | 26,385.699μs  | 91,584.400μs  | 21,756.776μs  | 61.12% | 32.70x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 2,669,432b | 253,075.200μs | 347,525.780μs | 288,684.856μs | 821,716.900μs | 164,378.252μs | 47.30% | 319.20x |
| ToroReactParamBench       | benchParam | 0   | 10   | 10  | 1,789,832b | 7,903.400μs   | 10,732.150μs  | 9,897.662μs   | 19,353.600μs  | 3,211.102μs   | 29.92% | 9.86x   |
| ZaphpaReactParamBench     | benchParam | 0   | 10   | 10  | 2,941,176b | 23,079.200μs  | 26,114.040μs  | 25,981.879μs  | 30,210.300μs  | 2,189.627μs   | 8.38%  | 23.99x  |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+---------------+--------+---------+
```

As you can see almost in all cases Mezon Router is faster that Zaphpa router.

# What's next?

More articles can be found in my [Twitter](https://twitter.com/mezonphp)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )