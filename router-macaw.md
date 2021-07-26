Hi all! Today we have one more benchmark.

As usual we have two cases:

1. http server accepts request, launches php script, which handles this request, and then all script data is removed from memory. All following requests are processed in the same way. In this case very critical to launch script as soon as possible and we do not have time for long pre-compilations and preparations. Because all of it will be lost after the script will finish working;
2. php script is launching, initiating all internal components (and router is one of them) and then starting processing requests. This case can be organized via for example react-php. It differs from the previous case because we can spend reasonable time to pre-compile routes for faster processing.

# The first case
```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
RouteGenerator::generateMacawStaticRoutes(1000);
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
RouteGenerator::generateMacawStaticRoutes(1000);
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
RouteGenerator::generateMacawStaticRoutes(1000);
Macaw::dispatch();
// and so on up to /static/999 route
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1';
RouteGenerator::generateMacawNonStaticRoutes(1000);
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1';
RouteGenerator::generateMacawNonStaticRoutes(1000);
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1';
RouteGenerator::generateMacawNonStaticRoutes(1000);
Macaw::dispatch();
// and so on up to /param/999/1 route
```

# The second case
```php
// static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/0';
RouteGenerator::generateMacawStaticRoutes(1000);
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/99';
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/static/199';
Macaw::dispatch();
// and so on up to /static/999 route
```

```php
// non static routes
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/0/1';
RouteGenerator::generateMacawNonStaticRoutes(1000);
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/99/1';
Macaw::dispatch();

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/param/199/1';
Macaw::dispatch();
// and so on up to /param/1/999 route
```

# OK What do we have?

## The first case + static routes

```
+------------------------------------+-------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| benchmark                          | subject     | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev       | rstdev | diff    |
+------------------------------------+-------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
| CoffeeSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,421,792b   | 42,887.400μs  | 44,110.520μs  | 44,006.318μs  | 45,896.700μs  | 731.456μs   | 1.66%  | 26.88x  |
| DVKSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 4,878,600b   | 26,610.800μs  | 27,311.260μs  | 27,157.306μs  | 28,591.400μs  | 545.456μs   | 2.00%  | 16.64x  |
| HoaSingleRequestStaticBench        | benchStatic | 0   | 10   | 10  | 5,398,504b   | 46,112.900μs  | 47,123.440μs  | 46,722.304μs  | 48,868.700μs  | 786.022μs   | 1.67%  | 28.71x  |
| IBSingleRequestStaticBench         | benchStatic | 0   | 10   | 10  | 5,120,480b   | 130,339.600μs | 133,999.070μs | 131,788.961μs | 139,483.100μs | 3,237.588μs | 2.42%  | 81.65x  |
| JoomlaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,404,816b   | 29,574.900μs  | 31,202.060μs  | 31,060.434μs  | 34,093.400μs  | 1,140.019μs | 3.65%  | 19.01x  |
| LeafSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 152,268,656b | 31,250.000μs  | 32,078.900μs  | 31,882.215μs  | 33,418.200μs  | 590.913μs   | 1.84%  | 19.55x  |
| MacawSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 4,404,816b   | 15,953.600μs  | 17,173.270μs  | 17,431.719μs  | 17,989.500μs  | 634.877μs   | 3.70%  | 10.46x  |
| MezonSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 4,405,224b   | 9,269.200μs   | 10,218.480μs  | 10,358.104μs  | 10,789.500μs  | 429.245μs   | 4.20%  | 6.23x   |
| MiladSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 35,134,736b  | 32,030.300μs  | 32,779.910μs  | 32,257.182μs  | 33,930.900μs  | 718.028μs   | 2.19%  | 19.97x  |
| PeceeSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 8,570,512b   | 39,446.800μs  | 41,928.180μs  | 40,962.929μs  | 50,059.700μs  | 2,936.226μs | 7.00%  | 25.55x  |
| PowerSingleRequestStaticBench      | benchStatic | 0   | 10   | 10  | 5,347,688b   | 21,038.100μs  | 22,333.040μs  | 22,047.192μs  | 23,902.800μs  | 838.572μs   | 3.75%  | 13.61x  |
| SteampixelSingleRequestStaticBench | benchStatic | 0   | 10   | 10  | 4,404,848b   | 214,769.600μs | 219,732.850μs | 218,472.236μs | 227,553.700μs | 3,569.859μs | 1.62%  | 133.90x |
| TetoSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 6,765,328b   | 42,704.200μs  | 46,040.130μs  | 43,741.671μs  | 65,754.100μs  | 6,685.943μs | 14.52% | 28.05x  |
| ToroSingleRequestStaticBench       | benchStatic | 0   | 10   | 10  | 4,404,816b   | 1,520.800μs   | 1,641.080μs   | 1,648.426μs   | 1,815.900μs   | 91.010μs    | 5.55%  | 1.00x   |
| ZaphpaSingleRequestStaticBench     | benchStatic | 0   | 10   | 10  | 4,922,128b   | 34,852.900μs  | 36,750.860μs  | 36,017.122μs  | 42,579.100μs  | 2,086.035μs | 5.68%  | 22.39x  |
+------------------------------------+-------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+-------------+--------+---------+
```

## The first case + non-static routes

```
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| benchmark                         | subject    | set | revs | its | mem_peak     | best          | mean          | mode          | worst         | stdev        | rstdev | diff   |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
| CoffeeSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,388,832b   | 57,210.900μs  | 58,142.010μs  | 57,612.744μs  | 59,372.400μs  | 752.514μs    | 1.29%  | 2.54x  |
| DVKSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 4,870,984b   | 30,440.400μs  | 33,219.850μs  | 32,300.568μs  | 39,669.000μs  | 2,454.361μs  | 7.39%  | 1.45x  |
| HoaSingleRequestParamBench        | benchParam | 0   | 10   | 10  | 5,600,184b   | 49,161.600μs  | 52,082.390μs  | 50,409.781μs  | 64,347.800μs  | 4,331.550μs  | 8.32%  | 2.28x  |
| IBSingleRequestParamBench         | benchParam | 0   | 10   | 10  | 5,241,200b   | 155,206.200μs | 168,856.750μs | 159,161.075μs | 199,139.700μs | 15,550.415μs | 9.21%  | 7.39x  |
| JoomlaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,096,376b   | 39,944.500μs  | 43,655.270μs  | 41,607.303μs  | 51,912.000μs  | 3,674.424μs  | 8.42%  | 1.91x  |
| LeafSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 152,278,112b | 34,874.500μs  | 37,790.860μs  | 37,767.886μs  | 41,445.700μs  | 1,873.793μs  | 4.96%  | 1.65x  |
| MacawSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 4,404,816b   | 27,943.800μs  | 35,659.150μs  | 31,393.071μs  | 73,138.100μs  | 12,755.662μs | 35.77% | 1.56x  |
| MezonSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 141,061,056b | 146,752.500μs | 170,194.630μs | 152,785.473μs | 260,932.100μs | 36,489.637μs | 21.44% | 7.45x  |
| MiladSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 35,993,272b  | 38,704.200μs  | 39,850.380μs  | 39,249.865μs  | 42,422.000μs  | 1,230.384μs  | 3.09%  | 1.74x  |
| PeceeSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 8,154,696b   | 80,192.000μs  | 82,885.880μs  | 81,251.688μs  | 88,274.100μs  | 2,705.337μs  | 3.26%  | 3.63x  |
| PowerSingleRequestParamBench      | benchParam | 0   | 10   | 10  | 5,347,688b   | 21,736.300μs  | 22,851.370μs  | 22,290.195μs  | 24,333.000μs  | 816.581μs    | 3.57%  | 1.00x  |
| SteampixelSingleRequestParamBench | benchParam | 0   | 10   | 10  | 4,404,848b   | 244,784.400μs | 253,750.480μs | 249,475.559μs | 278,078.600μs | 9,616.243μs  | 3.79%  | 11.10x |
| TetoSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 6,726,408b   | 60,670.300μs  | 79,920.380μs  | 73,702.178μs  | 130,037.900μs | 19,394.914μs | 24.27% | 3.50x  |
| ToroSingleRequestParamBench       | benchParam | 0   | 10   | 10  | 4,404,816b   | 9,689.800μs   | 27,495.940μs  | 17,091.499μs  | 99,743.800μs  | 26,067.520μs | 94.80% | 1.20x  |
| ZaphpaSingleRequestParamBench     | benchParam | 0   | 10   | 10  | 5,025,536b   | 48,650.000μs  | 53,104.450μs  | 50,692.984μs  | 64,711.000μs  | 4,885.802μs  | 9.20%  | 2.32x  |
+-----------------------------------+------------+-----+------+-----+--------------+---------------+---------------+---------------+---------------+--------------+--------+--------+
```

## The second case + static routes

```
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| benchmark                  | subject     | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff       |
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
| CoffeeReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,404,816b  | 9,244.100μs   | 10,291.010μs  | 9,959.738μs   | 13,219.000μs  | 1,120.150μs  | 10.88% | 533.63x    |
| DVKReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,404,816b  | 11,486.660μs  | 12,126.296μs  | 11,940.553μs  | 13,204.730μs  | 482.820μs    | 3.98%  | 628.79x    |
| HoaReactStaticBench        | benchStatic | 0   | 100  | 10  | 4,444,576b  | 3,867.240μs   | 4,144.961μs   | 4,069.371μs   | 4,454.110μs   | 181.688μs    | 4.38%  | 214.93x    |
| IBReactStaticBench         | benchStatic | 0   | 10   | 10  | 4,683,736b  | 27,970.900μs  | 30,546.820μs  | 29,286.966μs  | 39,980.000μs  | 3,427.355μs  | 11.22% | 1,583.97x  |
| JoomlaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,404,816b  | 5,581.900μs   | 6,261.770μs   | 6,333.534μs   | 6,763.700μs   | 331.498μs    | 5.29%  | 324.70x    |
| LeafReactStaticBench       | benchStatic | 0   | 10   | 10  | 17,032,288b | 8,718.700μs   | 9,429.620μs   | 9,596.009μs   | 10,077.200μs  | 418.128μs    | 4.43%  | 488.96x    |
| MacawReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,404,816b  | 3,966.500μs   | 4,547.030μs   | 4,287.913μs   | 5,229.900μs   | 440.286μs    | 9.68%  | 235.78x    |
| MezonReactStaticBench      | benchStatic | 0   | 100  | 10  | 4,405,224b  | 18.830μs      | 19.285μs      | 19.052μs      | 20.360μs      | 0.471μs      | 2.44%  | 1.00x      |
| MiladReactStaticBench      | benchStatic | 0   | 10   | 10  | 6,760,320b  | 16,863.200μs  | 17,928.550μs  | 17,538.320μs  | 20,783.500μs  | 1,113.527μs  | 6.21%  | 929.66x    |
| PeceeReactStaticBench      | benchStatic | 0   | 10   | 10  | 5,729,376b  | 20,468.700μs  | 22,616.480μs  | 21,170.085μs  | 28,094.400μs  | 2,524.951μs  | 11.16% | 1,172.75x  |
| PowerReactStaticBench      | benchStatic | 0   | 10   | 10  | 4,515,104b  | 16,747.600μs  | 17,718.270μs  | 17,725.738μs  | 19,259.300μs  | 726.676μs    | 4.10%  | 918.76x    |
| SteampixelReactStaticBench | benchStatic | 0   | 10   | 10  | 4,404,816b  | 254,838.600μs | 264,584.430μs | 259,244.541μs | 295,773.800μs | 12,104.379μs | 4.57%  | 13,719.70x |
| TetoReactStaticBench       | benchStatic | 0   | 10   | 10  | 5,238,440b  | 4,072.800μs   | 4,749.520μs   | 4,475.797μs   | 6,553.900μs   | 687.568μs    | 14.48% | 246.28x    |
| ToroReactStaticBench       | benchStatic | 0   | 10   | 10  | 4,404,816b  | 409.800μs     | 464.380μs     | 436.985μs     | 693.300μs     | 80.115μs     | 17.25% | 24.08x     |
| ZaphpaReactStaticBench     | benchStatic | 0   | 10   | 10  | 4,404,816b  | 12,586.100μs  | 14,936.320μs  | 13,641.531μs  | 18,931.100μs  | 2,113.612μs  | 14.15% | 774.50x    |
+----------------------------+-------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+------------+
```

## The second case + non-static routes

```
+---------------------------+------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| benchmark                 | subject    | set | revs | its | mem_peak    | best          | mean          | mode          | worst         | stdev        | rstdev | diff    |
+---------------------------+------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
| CoffeeReactParamBench     | benchParam | 0   | 10   | 10  | 4,404,816b  | 9,346.100μs   | 12,261.110μs  | 10,725.392μs  | 25,364.700μs  | 4,551.819μs  | 37.12% | 8.59x   |
| DVKReactParamBench        | benchParam | 0   | 100  | 10  | 4,404,816b  | 14,638.400μs  | 15,790.154μs  | 14,979.128μs  | 17,804.070μs  | 1,052.202μs  | 6.66%  | 11.06x  |
| HoaReactParamBench        | benchParam | 0   | 100  | 10  | 4,730,008b  | 3,957.450μs   | 4,058.723μs   | 4,037.900μs   | 4,255.350μs   | 75.601μs     | 1.86%  | 2.84x   |
| IBReactParamBench         | benchParam | 0   | 10   | 10  | 4,820,208b  | 37,245.200μs  | 38,581.780μs  | 38,063.843μs  | 42,749.500μs  | 1,504.175μs  | 3.90%  | 27.03x  |
| JoomlaReactParamBench     | benchParam | 0   | 10   | 10  | 4,404,816b  | 4,040.700μs   | 4,294.750μs   | 4,223.156μs   | 4,769.100μs   | 215.156μs    | 5.01%  | 3.01x   |
| LeafReactParamBench       | benchParam | 0   | 10   | 10  | 17,041,664b | 9,973.200μs   | 10,710.610μs  | 10,376.970μs  | 13,052.700μs  | 871.227μs    | 8.13%  | 7.51x   |
| MacawReactParamBench      | benchParam | 0   | 10   | 10  | 4,404,816b  | 14,705.800μs  | 15,567.440μs  | 15,621.434μs  | 16,569.900μs  | 495.179μs    | 3.18%  | 10.91x  |
| MezonReactParamBench      | benchParam | 0   | 100  | 10  | 4,681,176b  | 1,070.680μs   | 1,427.126μs   | 1,139.771μs   | 4,012.790μs   | 862.979μs    | 60.47% | 1.00x   |
| MiladReactParamBench      | benchParam | 0   | 10   | 10  | 6,887,816b  | 21,768.200μs  | 23,694.400μs  | 22,963.173μs  | 30,491.500μs  | 2,333.609μs  | 9.85%  | 16.60x  |
| PeceeReactParamBench      | benchParam | 0   | 10   | 10  | 6,110,472b  | 46,821.300μs  | 50,153.060μs  | 48,169.419μs  | 65,957.100μs  | 5,510.359μs  | 10.99% | 35.14x  |
| PowerReactParamBench      | benchParam | 0   | 10   | 10  | 4,515,104b  | 17,027.000μs  | 17,705.200μs  | 17,330.517μs  | 18,659.600μs  | 570.959μs    | 3.22%  | 12.41x  |
| SteampixelReactParamBench | benchParam | 0   | 10   | 10  | 4,404,816b  | 240,130.300μs | 245,763.930μs | 245,531.622μs | 253,088.400μs | 3,355.995μs  | 1.37%  | 172.21x |
| TetoReactParamBench       | benchParam | 0   | 10   | 10  | 5,219,056b  | 10,345.100μs  | 11,818.020μs  | 11,057.018μs  | 15,774.800μs  | 1,599.283μs  | 13.53% | 8.28x   |
| ToroReactParamBench       | benchParam | 0   | 10   | 10  | 4,404,800b  | 8,782.900μs   | 13,319.470μs  | 10,442.532μs  | 29,984.700μs  | 6,346.543μs  | 47.65% | 9.33x   |
| ZaphpaReactParamBench     | benchParam | 0   | 10   | 10  | 4,421,912b  | 25,464.700μs  | 39,406.550μs  | 29,009.220μs  | 60,979.400μs  | 13,633.090μs | 34.60% | 27.61x  |
+---------------------------+------------+-----+------+-----+-------------+---------------+---------------+---------------+---------------+--------------+--------+---------+
```

As you can see in half of all cases Mezon Router is faster than Macaw router.

# What's next?

More articles can be found in my [Twitter](https://twitter.com/mezonphp)

# What is mezon/router?

mezon/router now is:

- framework for routing with **100% code coverage**
- **10.0 points** on scrutinizer-ci.com
- router is a part of the [Mezon Project](https://github.com/alexdodonov/mezon)

Repo on github.com: https://github.com/alexdodonov/mezon-router

# It will be great if you will contribute something to this project. Documentation, sharing the project in your social media, bug fixing, refactoring, or even **[submitting issue with question or feature request](https://github.com/alexdodonov/mezon-router/issues)**. Thanks anyway )