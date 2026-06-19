<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
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
