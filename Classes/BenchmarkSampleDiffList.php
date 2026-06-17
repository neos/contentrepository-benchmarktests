<?php
declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class BenchmarkSampleDiffList implements \JsonSerializable
{
    /** @var list<BenchmarkSampleDiff> */
    public array $items;

    public function __construct(
        BenchmarkSampleDiff ...$items
    ) {
        $this->items = array_values($items);
    }

    public function jsonSerialize(): mixed
    {
        return $this->items;
    }
}
