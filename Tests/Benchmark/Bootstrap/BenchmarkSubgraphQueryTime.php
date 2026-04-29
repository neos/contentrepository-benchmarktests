<?php

declare(strict_types=1);

/** In microseconds */
final readonly class BenchmarkSubgraphQueryTime
{
    public function __construct(
        public int $idQueryTime,
        public int $childrenQueryTime,
        public int $parentQueryTime,
        public int $descendantsQueryTime,
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
            descendantsQueryTime: $array['descendantsQueryTime'],
            ancestorsQueryTime: $array['ancestorsQueryTime'],
        );
    }

    public static function diff(
        self $firstSample,
        self $secondSample,
    ): BenchmarkSubgraphQueryTimeDiff {
        return new BenchmarkSubgraphQueryTimeDiff(
            idQueryTime: ValueDiff::calculate($firstSample->idQueryTime, $secondSample->idQueryTime),
            childrenQueryTime: ValueDiff::calculate($firstSample->childrenQueryTime, $secondSample->childrenQueryTime),
            parentQueryTime: ValueDiff::calculate($firstSample->parentQueryTime, $secondSample->parentQueryTime),
            descendantsQueryTime: ValueDiff::calculate($firstSample->descendantsQueryTime, $secondSample->descendantsQueryTime),
            ancestorsQueryTime: ValueDiff::calculate($firstSample->ancestorsQueryTime, $secondSample->ancestorsQueryTime),
        );
    }
}
