<?php

declare(strict_types=1);

final readonly class BenchmarkSample
{
    public function __construct(
        public string $name,
        /** Depth of the created graph, for estimating recursive CTEs */
        public int $depth,
        /** Breath of the created graph */
        public int $breath,
        /** Command runtime in milliseconds */
        public int $commandRuntime,
        public BenchmarkSubgraphQueryTime $subgraphQueryTime,
        public BenchmarkContentgraphQueryTime $contentgraphQueryTime,
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            name: $array['name'],
            depth: $array['depth'],
            breath: $array['breath'],
            commandRuntime: $array['commandRuntime'],
            subgraphQueryTime: BenchmarkSubgraphQueryTime::fromArray($array['subgraphQueryTime']),
            contentgraphQueryTime: BenchmarkContentgraphQueryTime::fromArray($array['contentgraphQueryTime']),
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
            contentgraphQueryTime: BenchmarkContentgraphQueryTime::diff(
                $firstSample->contentgraphQueryTime,
                $secondSample->contentgraphQueryTime,
            ),
        );
    }
}
