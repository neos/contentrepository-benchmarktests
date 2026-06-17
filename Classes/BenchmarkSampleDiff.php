<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

final readonly class BenchmarkSampleDiff
{
    public function __construct(
        public ValueDiff $commandRuntime,
        public BenchmarkSubgraphQueryTimeDiff $subgraphQueryTime,
        public BenchmarkContentgraphQueryTimeDiff $contentgraphQueryTime,
    ) {
    }
}
