<?php
declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

final readonly class BenchmarkSampleGroupDiff
{
    public function __construct(
        public string $groupName,
        public BenchmarkSampleDiffList $benchmarkSampleDiffs
    ) {
    }
}
