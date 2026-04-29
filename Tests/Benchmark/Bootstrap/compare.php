<?php

require_once __DIR__ . '/BenchmarkSampleList.php';
require_once __DIR__ . '/BenchmarkSample.php';
require_once __DIR__ . '/BenchmarkSampleDiff.php';
require_once __DIR__ . '/BenchmarkSampleDiffList.php';
require_once __DIR__ . '/BenchmarkSampleGroup.php';
require_once __DIR__ . '/BenchmarkSampleGroupDiff.php';
require_once __DIR__ . '/BenchmarkSampleGroupDiffList.php';
require_once __DIR__ . '/BenchmarkSampleGroupList.php';
require_once __DIR__ . '/ValueDiff.php';

const FLOW_PATH_DATA = '/Users/marchenryschultz/Code/core/neos-manufacture-90/Data/';

compare(
    $argv[1],
    $argv[2],
);

function parseBenchmark(string $directoryName): BenchmarkSampleGroupList
{
    $list = [];

    /** @var SplFileInfo $item */
    foreach (new DirectoryIterator($directoryName) as $item) {
        if ($item->getExtension() === 'json') {
            $list[] = new BenchmarkSampleGroup(
                $item->getBasename('.json'),
                BenchmarkSampleList::fromArray(
                    json_decode(
                        file_get_contents($item->getPathname()) ?: throw new \RuntimeException(sprintf('Failed to read benchmark %s', $item->getPathname()), 1777402645),
                        true,
                        flags: JSON_THROW_ON_ERROR
                    )
                )
            );
        }
    }

    return new BenchmarkSampleGroupList(...$list);
}

function compare(
    string $firstBenchmarkDirectoryName,
    string $secondBenchmarkDirectoryName,
) {
    $firstBenchmark = parseBenchmark(
        FLOW_PATH_DATA . $firstBenchmarkDirectoryName
    );

    $secondBenchmark = parseBenchmark(
        FLOW_PATH_DATA . $secondBenchmarkDirectoryName
    );

    $diff = BenchmarkSampleGroupList::diff(
        $firstBenchmark,
        $secondBenchmark
    );

    echo json_encode(
        $diff,
        JSON_PRETTY_PRINT
    );
}
