<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests;

/** In microseconds */
final readonly class BenchmarkContentgraphQueryTime
{
    public function __construct(
        public int $idQueryTime,
        public int $childrenQueryTime,
        public int $parentQueryTime,
        public int $ancestorsQueryTime,
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            idQueryTime: $array['idQueryTime'],
            childrenQueryTime: $array['childrenQueryTime'],
            parentQueryTime: $array['parentQueryTime'],
            ancestorsQueryTime: $array['ancestorsQueryTime'],
        );
    }

    public static function diff(
        self $firstSample,
        self $secondSample,
    ): BenchmarkContentgraphQueryTimeDiff {
        return new BenchmarkContentgraphQueryTimeDiff(
            idQueryTime: ValueDiff::calculate($firstSample->idQueryTime, $secondSample->idQueryTime),
            childrenQueryTime: ValueDiff::calculate($firstSample->childrenQueryTime, $secondSample->childrenQueryTime),
            parentQueryTime: ValueDiff::calculate($firstSample->parentQueryTime, $secondSample->parentQueryTime),
            ancestorsQueryTime: ValueDiff::calculate($firstSample->ancestorsQueryTime, $secondSample->ancestorsQueryTime),
        );
    }
}
