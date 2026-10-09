<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

/** In microseconds */
#[Flow\Proxy(false)]
final readonly class BenchmarkCommandExecutionTime
{
    public function __construct(
        public int $moveNodeRuntime,
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            moveNodeRuntime: $array['moveNodeRuntime'],
        );
    }

    public static function diff(
        self $firstSample,
        self $secondSample,
    ): BenchmarkCommandExecutionTimeDiff {
        return new BenchmarkCommandExecutionTimeDiff(
            moveNodeRuntime: ValueDiff::calculate($firstSample->moveNodeRuntime, $secondSample->moveNodeRuntime),
        );
    }
}
