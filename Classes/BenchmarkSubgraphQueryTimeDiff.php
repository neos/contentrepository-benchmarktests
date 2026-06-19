<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

final readonly class BenchmarkSubgraphQueryTimeDiff
{
    public function __construct(
        public ValueDiff $idQueryTime,
        public ValueDiff $childrenQueryTime,
        public ValueDiff $parentQueryTime,
        public ValueDiff $descendantsQueryTime,
        public ValueDiff $subtreeQueryTime,
        public ValueDiff $ancestorsQueryTime,
        public ValueDiff $referenceQueryTime,
        public ValueDiff $backReferenceQueryTime,
    ) {
    }
}
