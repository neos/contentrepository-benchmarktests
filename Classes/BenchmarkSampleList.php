<?php
declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

final readonly class BenchmarkSampleList
{
    /** @var list<BenchmarkSample> */
    public array $items;

    public function __construct(
        BenchmarkSample ...$items
    ) {
        $this->items = array_values($items);
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            ...array_map(
                BenchmarkSample::fromArray(...),
                $array
            ),
        );
    }

    public static function diff(
        self $firstSample,
        self $secondSample,
    ): BenchmarkSampleDiffList {
        if (count($firstSample->items) !== count($secondSample->items)) {
            throw new \RuntimeException(sprintf('Samples must have same length'), 1777405428);
        }
        return new BenchmarkSampleDiffList(
            ...array_map(
                BenchmarkSample::diff(...),
                $firstSample->items,
                $secondSample->items,
            )
        );
    }
}
