
# Neos.ContentRepository.BenchmarkTests

## Install

```sh
git clone -C DistributionPackages git@github.com:neos/contentrepository-benchmarktests.git Neos.ContentRepository.BenchmarkTests
composer require neos/contentrepository-benchmarktests:@dev
```

## Run

```sh
cd DistributionPackages/Neos.ContentRepository.BenchmarkTests

../../bin/behat -f progress --strict --no-interaction -c Tests/Benchmark/behat.yml.dist
```

Absolute timings will be written to the `Data/Benchmark-{pid}` directory

## Diff two benchmarks

```sh
php ./scripts/compare Benchmark-Before Benchmark-After
```
