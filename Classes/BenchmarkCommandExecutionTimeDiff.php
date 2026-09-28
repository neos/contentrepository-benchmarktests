<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class BenchmarkCommandExecutionTimeDiff
{
    public function __construct(
        public ValueDiff $moveNodeRuntime,
    ) {
    }
}
