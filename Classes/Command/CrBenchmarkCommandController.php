<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests\Command;

use Neos\ContentRepository\BenchmarkTests\BenchmarkSampleGroup;
use Neos\ContentRepository\BenchmarkTests\BenchmarkSampleGroupList;
use Neos\ContentRepository\BenchmarkTests\BenchmarkSampleList;
use Neos\Flow\Cli\CommandController;

class CrBenchmarkCommandController extends CommandController
{
    public function compareCommand(
        string $firstBenchmarkDirectoryName,
        string $secondBenchmarkDirectoryName,
    ) {
        $firstBenchmark = $this->parseBenchmark(
            FLOW_PATH_DATA . $firstBenchmarkDirectoryName
        );

        $secondBenchmark = $this->parseBenchmark(
            FLOW_PATH_DATA . $secondBenchmarkDirectoryName
        );

        $diff = BenchmarkSampleGroupList::diff(
            $firstBenchmark,
            $secondBenchmark
        );

        $stringDiff = json_encode(
            $diff,
            JSON_PRETTY_PRINT
        );

        // Haha regex:O This will be added to the value objects later
        $hackyColored = preg_replace(
            [
                '/"relativeDifference": [2-9]\d*\.\d+/',
                '/"relativeDifference": 1\.\d+/',
                '/"relativeDifference": 0\.\d+/'
            ],
            [
                '<error>$0</error>',
                '<comment>$0</comment>',
                '<success>$0</success>',
            ],
            $stringDiff
        );

        $this->outputLine($hackyColored);

        echo PHP_EOL;
    }

    private function parseBenchmark(string $directoryName): BenchmarkSampleGroupList
    {
        $list = [];

        /** @var \SplFileInfo $item */
        foreach (new \DirectoryIterator($directoryName) as $item) {
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
}
