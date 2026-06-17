<?php
declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class BenchmarkSampleGroupDiffList implements \JsonSerializable
{
    /** @var list<BenchmarkSampleGroupDiff> */
    public array $items;

    public function __construct(
        BenchmarkSampleGroupDiff ...$items
    ) {
        $this->items = $items;
    }

    public function jsonSerialize(): mixed
    {
        return $this->items;
    }
}
