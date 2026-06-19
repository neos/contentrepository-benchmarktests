<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class BenchmarkSample
{
    public function __construct(
        public string $name,
        /** Depth of the created graph, for estimating recursive CTEs */
        public int $depth,
        /** Breadth of the created graph */
        public int $breadth,
        /** Command runtime in milliseconds */
        public int $commandRuntime,
        public BenchmarkSubgraphQueryTime $subgraphQueryTime,
        public BenchmarkContentGraphQueryTime $contentGraphQueryTime,
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            name: $array['name'],
            depth: $array['depth'],
            breadth: $array['breadth'],
            commandRuntime: $array['commandRuntime'],
            subgraphQueryTime: BenchmarkSubgraphQueryTime::fromArray($array['subgraphQueryTime']),
            contentGraphQueryTime: BenchmarkContentGraphQueryTime::fromArray($array['contentGraphQueryTime']),
        );
    }

    public static function diff(
        self $firstSample,
        self $secondSample,
    ): BenchmarkSampleDiff {
        if ($firstSample->name !== $secondSample->name) {
            throw new \RuntimeException(sprintf('Samples must have the same name'), 1777406270);
        }

        return new BenchmarkSampleDiff(
            commandRuntime: ValueDiff::calculate($firstSample->commandRuntime, $secondSample->commandRuntime),
            subgraphQueryTime: BenchmarkSubgraphQueryTime::diff(
                $firstSample->subgraphQueryTime,
                $secondSample->subgraphQueryTime,
            ),
            contentgraphQueryTime: BenchmarkContentGraphQueryTime::diff(
                $firstSample->contentGraphQueryTime,
                $secondSample->contentGraphQueryTime,
            ),
        );
    }
}
