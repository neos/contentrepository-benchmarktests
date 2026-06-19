<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class ValueDiff
{
    public function __construct(
        public int $first,
        public int $second,
        public int $absoluteDifference,
        public float $relativeDifference,
    ) {
    }

    public static function calculate(
        int $firstValue,
        int $secondValue,
    ): self {
        return new self(
            first: $firstValue,
            second: $secondValue,
            absoluteDifference: $secondValue - $firstValue,
            relativeDifference: $secondValue / $firstValue
        );
    }
}
