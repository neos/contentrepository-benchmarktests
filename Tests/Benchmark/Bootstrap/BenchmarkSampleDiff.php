<?php

declare(strict_types=1);

final readonly class BenchmarkSampleDiff
{
    public function __construct(
        public ValueDiff $commandRuntime,
        public ValueDiff $idQueryTime,
        public ValueDiff $childrenQueryTime,
        public ValueDiff $parentQueryTime,
        public ValueDiff $descendantsQueryTime,
        public ValueDiff $ancestorsQueryTime,
    ) {
    }
}
