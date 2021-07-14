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
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                          | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,408,104b  | 46,263.800μs  | 51,703.840μs  | 49,024.786μs  | 67,011.800μs  | 6,033.007μs  | 11.67% | 25.84x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 4,864,944b  | 27,984.400μs  | 32,832.530μs  | 31,046.204μs  | 41,708.800μs  | 3,948.300μs  | 12.03% | 16.41x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 5,319,312b  | 50,815.700μs  | 63,740.940μs  | 58,189.928μs  | 90,068.100μs  | 12,507.405μs | 19.62% | 31.86x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 5,106,824b  | 130,406.700μs | 147,356.060μs | 138,872.194μs | 194,022.400μs | 18,238.639μs | 12.38% | 73.64x  |
| JoomlaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b  | 32,933.200μs  | 36,518.220μs  | 34,200.002μs  | 43,374.100μs  | 3,757.862μs  | 10.29% | 18.25x  |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 4,398,688b  | 9,856.500μs   | 11,273.610μs  | 10,861.656μs  | 13,499.300μs  | 1,020.466μs  | 9.05%  | 5.63x   |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 35,121,048b | 31,010.700μs  | 34,699.640μs  | 33,634.138μs  | 40,940.900μs  | 2,883.701μs  | 8.31%  | 17.34x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 8,556,856b  | 38,148.000μs  | 55,252.570μs  | 54,955.493μs  | 78,660.400μs  | 10,637.292μs | 19.25% | 27.61x  |
| PowerSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 5,334,000b  | 20,386.000μs  | 22,615.280μs  | 21,962.845μs  | 28,363.900μs  | 2,066.683μs  | 9.14%  | 11.30x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 4,398,312b  | 225,785.000μs | 257,492.320μs | 235,846.394μs | 320,995.600μs | 31,431.725μs | 12.21% | 128.69x |
| TetoSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 6,751,672b  | 43,040.000μs  | 44,205.360μs  | 44,030.530μs  | 46,223.400μs  | 863.046μs    | 1.95%  | 22.09x  |
| ToroSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 4,398,280b  | 1,502.900μs   | 2,000.900μs   | 1,766.741μs   | 3,577.100μs   | 591.889μs    | 29.58% | 1.00x   |
| ZaphpaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,908,472b  | 34,735.000μs  | 36,053.040μs  | 35,353.195μs  | 37,412.100μs  | 976.786μs    | 2.71%  | 18.02x  |
+------------------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,375,144b   | 57,118.300μs  | 65,236.340μs  | 63,200.919μs  | 81,786.700μs  | 6,909.803μs  | 10.59% | 5.45x  |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,857,328b   | 30,215.500μs  | 34,929.980μs  | 32,554.285μs  | 48,053.100μs  | 5,109.931μs  | 14.63% | 2.92x  |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 5,520,992b   | 47,633.600μs  | 63,909.850μs  | 67,293.036μs  | 76,668.200μs  | 8,692.429μs  | 13.60% | 5.34x  |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 5,227,544b   | 164,730.900μs | 186,204.170μs | 179,938.840μs | 219,846.200μs | 16,701.000μs | 8.97%  | 15.57x |
| JoomlaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,082,720b   | 44,238.600μs  | 74,410.630μs  | 83,965.128μs  | 107,676.900μs | 20,635.791μs | 27.73% | 6.22x  |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 141,046,792b | 130,214.200μs | 189,127.420μs | 165,447.327μs | 254,380.600μs | 39,733.098μs | 21.01% | 15.81x |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 35,979,584b  | 38,371.200μs  | 51,855.490μs  | 44,430.696μs  | 72,397.600μs  | 11,992.065μs | 23.13% | 4.34x  |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 8,141,040b   | 88,584.000μs  | 109,251.990μs | 95,887.571μs  | 163,226.500μs | 23,913.368μs | 21.89% | 9.13x  |
| PowerSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 5,334,000b   | 21,425.300μs  | 26,456.170μs  | 23,748.030μs  | 37,042.600μs  | 4,814.631μs  | 18.20% | 2.21x  |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 4,398,312b   | 250,291.300μs | 325,211.140μs | 277,104.621μs | 454,792.900μs | 72,031.456μs | 22.15% | 27.19x |
| TetoSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 6,712,752b   | 56,235.000μs  | 64,812.260μs  | 59,051.437μs  | 83,389.700μs  | 9,301.687μs  | 14.35% | 5.42x  |
| ToroSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 4,398,280b   | 9,412.000μs   | 11,961.960μs  | 10,611.622μs  | 18,045.900μs  | 2,629.254μs  | 21.98% | 1.00x  |
| ZaphpaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,011,880b   | 60,837.400μs  | 68,357.080μs  | 64,699.323μs  | 78,148.300μs  | 5,857.735μs  | 8.57%  | 5.71x  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev        | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b | 12,178.000μs  | 15,954.090μs  | 13,571.801μs  | 28,365.100μs  | 4,990.991μs  | 31.28% | 476.98x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,398,280b | 11,619.110μs  | 12,976.309μs  | 12,421.136μs  | 17,473.900μs  | 1,598.094μs  | 12.32% | 387.95x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,398,264b | 3,686.900μs   | 3,886.220μs   | 3,766.509μs   | 4,883.380μs   | 344.424μs    | 8.86%  | 116.19x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 4,670,080b | 27,943.300μs  | 30,675.540μs  | 28,699.233μs  | 35,826.600μs  | 2,815.953μs  | 9.18%  | 917.11x    |
| JoomlaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b | 6,245.700μs   | 8,544.020μs   | 7,131.923μs   | 17,567.200μs  | 3,315.623μs  | 38.81% | 255.44x    |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 4,398,688b | 18.620μs      | 33.448μs      | 22.209μs      | 120.520μs     | 30.231μs     | 90.38% | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 6,746,632b | 16,234.200μs  | 27,776.760μs  | 20,544.455μs  | 48,624.500μs  | 11,709.752μs | 42.16% | 830.45x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,715,720b | 23,666.600μs  | 37,234.220μs  | 30,358.725μs  | 76,277.000μs  | 15,607.914μs | 41.92% | 1,113.20x  |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,501,416b | 19,924.400μs  | 29,438.480μs  | 25,666.477μs  | 48,975.900μs  | 8,266.134μs  | 28.08% | 880.13x    |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 4,398,280b | 267,198.300μs | 348,297.560μs | 329,357.277μs | 495,710.800μs | 65,334.639μs | 18.76% | 10,413.11x |
| TetoReactStaticBench       | benchStatic | 0   | 10   | 10  | 5,224,784b | 3,874.400μs   | 4,295.020μs   | 4,195.760μs   | 4,907.200μs   | 313.886μs    | 7.31%  | 128.41x    |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 4,398,280b | 402.300μs     | 487.260μs     | 436.972μs     | 730.400μs     | 98.491μs     | 20.21% | 14.57x     |
| ZaphpaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,398,280b | 12,219.500μs  | 13,638.070μs  | 13,716.009μs  | 14,803.000μs  | 687.941μs    | 5.04%  | 407.74x    |
+----------------------------+-------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
```

## The second case + non-static routes

```
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak   | best          | mean          | mode          | worst         | stdev       | rstdev | diff    |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 4,398,280b | 8,357.600μs   | 9,547.790μs   | 9,321.814μs   | 11,495.900μs  | 925.960μs   | 9.70%  | 8.17x   |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 4,398,280b | 14,138.610μs  | 14,502.916μs  | 14,265.437μs  | 15,461.230μs  | 438.294μs   | 3.02%  | 12.40x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 4,716,352b | 3,741.820μs   | 3,947.225μs   | 3,871.694μs   | 4,167.240μs   | 137.226μs   | 3.48%  | 3.38x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 4,792,560b | 35,821.100μs  | 36,958.460μs  | 36,855.247μs  | 38,450.200μs  | 662.865μs   | 1.79%  | 31.61x  |
| JoomlaReactParamBench     | benchParam | 0   | 10   | 10  | 4,398,280b | 3,790.800μs   | 4,424.510μs   | 4,182.264μs   | 6,689.900μs   | 782.446μs   | 17.68% | 3.78x   |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 4,666,912b | 1,112.080μs   | 1,169.179μs   | 1,145.887μs   | 1,253.680μs   | 43.814μs    | 3.75%  | 1.00x   |
| MiladReactParamBench      | benchParam | 0   | 10   | 10  | 6,874,128b | 21,378.400μs  | 22,626.550μs  | 22,076.610μs  | 25,256.500μs  | 1,153.591μs | 5.10%  | 19.35x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 6,096,816b | 46,623.700μs  | 49,031.670μs  | 48,017.979μs  | 57,418.800μs  | 3,000.798μs | 6.12%  | 41.94x  |
| PowerReactParamBench      | benchParam | 0   | 10   | 10  | 4,501,416b | 16,023.700μs  | 17,932.040μs  | 17,004.118μs  | 25,847.100μs  | 2,744.862μs | 15.31% | 15.34x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 4,398,280b | 239,416.500μs | 245,873.640μs | 242,665.174μs | 261,550.800μs | 6,616.069μs | 2.69%  | 210.30x |
| TetoReactParamBench       | benchParam | 0   | 10   | 10  | 5,205,400b | 9,496.100μs   | 11,672.850μs  | 11,352.232μs  | 14,650.900μs  | 1,414.837μs | 12.12% | 9.98x   |
| ToroReactParamBench       | benchParam | 0   | 10   | 10  | 4,398,264b | 8,425.800μs   | 9,954.940μs   | 8,960.808μs   | 13,222.100μs  | 1,582.835μs | 15.90% | 8.51x   |
| ZaphpaReactParamBench     | benchParam | 0   | 10   | 10  | 4,408,256b | 24,850.000μs  | 25,792.780μs  | 25,674.117μs  | 26,966.200μs  | 558.551μs   | 2.17%  | 22.06x  |
+---------------------------+------------+-----+------+-----+------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
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