<?php

declare(strict_types=1);

final readonly class BenchmarkSample
{
    public function __construct(
        /** Depth of the created graph, for estimating recursive CTEs */
        public int $depth,
        /** Breath of the created graph */
        // public int $breath,
        /** Command runtime in milliseconds */
        public int $commandRuntime,
        /** ID query time in microseconds */
        public int $idQueryTime,
        /** Child query time in microseconds */
        public int $childrenQueryTime,
        /** Parent query time in microseconds */
        public int $parentQueryTime,
        /** Descendants query time in microseconds */
        public int $descendantsQueryTime,
        /** Ancestors query time in microseconds */
        public int $ancestorsQueryTime,
    ) {
    }

    /** @param array<int|string,mixed> $array */
    public static function fromArray(array $array): self
    {
        return new self(
            depth: $array['depth'],
            // breath: $array['breath'],
            commandRuntime: $array['commandRuntime'],
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
    ): BenchmarkSampleDiff {
        return new BenchmarkSampleDiff(
            commandRuntime: ValueDiff::calculate($firstSample->commandRuntime, $secondSample->commandRuntime),
            idQueryTime: ValueDiff::calculate($firstSample->idQueryTime, $secondSample->idQueryTime),
            childrenQueryTime: ValueDiff::calculate($firstSample->childrenQueryTime, $secondSample->childrenQueryTime),
            parentQueryTime: ValueDiff::calculate($firstSample->parentQueryTime, $secondSample->parentQueryTime),
            descendantsQueryTime: ValueDiff::calculate($firstSample->descendantsQueryTime, $secondSample->descendantsQueryTime),
            ancestorsQueryTime: ValueDiff::calculate($firstSample->ancestorsQueryTime, $secondSample->ancestorsQueryTime),
        );
    }
}
