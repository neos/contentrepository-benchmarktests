<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class BenchmarkSampleGroupList
{
    /** @var array<string,BenchmarkSampleGroup> */
    public array $items;

    public function __construct(
        BenchmarkSampleGroup ...$items
    ) {
        $indexed = [];
        foreach ($items as $item) {
            if (isset($indexed[$item->groupName])) {
                throw new \RuntimeException(sprintf('Key already exists %s', $item->groupName), 1777405179);
            }
            $indexed[$item->groupName] = $item;
        }
        ksort($indexed);
        $this->items = $indexed;
    }

    public static function diff(
        self $firstSample,
        self $secondSample,
    ): BenchmarkSampleGroupDiffList {
        if (count($firstSample->items) !== count($secondSample->items)) {
            throw new \RuntimeException(sprintf('Samples must have same length'), 1777405428);
        }

        return new BenchmarkSampleGroupDiffList(
            ...array_map(
                BenchmarkSampleGroup::diff(...),
                $firstSample->items,
                $secondSample->items
            )
        );
    }
}
