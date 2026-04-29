<?php

declare(strict_types=1);

final readonly class BenchmarkContentgraphQueryTimeDiff
{
    public function __construct(
        public ValueDiff $idQueryTime,
        public ValueDiff $childrenQueryTime,
        public ValueDiff $parentQueryTime,
        public ValueDiff $ancestorsQueryTime,
    ) {
    }
}
