<?php
declare(strict_types=1);

final readonly class BenchmarkSampleGroupDiff
{
    public function __construct(
        public string $groupName,
        public BenchmarkSampleDiffList $benchmarkSampleDiffs
    ) {
    }
}
