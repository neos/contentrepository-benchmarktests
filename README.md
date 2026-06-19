
# Neos.ContentRepository.BenchmarkTests

## Overview

There are two kinds of benchmark tests provided:

### Relative tests

First relative ones, which prove something runs in the expected order of magnitude (e.g. `O(logn)` for `findNodeById`) and are designed to run in the regular CI

### Absolute tests

And there are absolute ones to compare two different things, e.g. different versions of the same adapter or two adapters and are supposed to live outside the distribution to do exactly that

## Install

```sh
# not on composer yet
git clone -C DistributionPackages git@github.com:neos/contentrepository-benchmarktests.git Neos.ContentRepository.BenchmarkTests
composer require neos/contentrepository-benchmarktests:@dev
```

## Run

```sh
cd DistributionPackages/Neos.ContentRepository.BenchmarkTests

composer run test:benchmark

# or
../../bin/behat -f progress --strict --no-interaction -c Tests/Benchmark/behat.yml.dist

# relative tests are WIP, to enable them use
export EXPECT_RELATIVE_GROWTH=1
```

Absolute timings will be written to the `Data/Benchmark-{pid}` directory

## Diff two benchmarks

```sh
flow crbenchmark:compare Benchmark-Before Benchmark-After
```
