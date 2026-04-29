<?php
declare(strict_types=1);

final readonly class BenchmarkSampleGroup
{
    public function __construct(
        public string $groupName,
        public BenchmarkSampleList $benchmarkSamples
    ) {
    }

    public static function diff(
        self $firstSample,
        self $secondSample,
    ): BenchmarkSampleGroupDiff {
        if ($firstSample->groupName !== $secondSample->groupName) {
            throw new \RuntimeException(sprintf('Samples must have the same name'), 1777406270);
        }

        return new BenchmarkSampleGroupDiff(
            groupName: $firstSample->groupName,
            benchmarkSampleDiffs: BenchmarkSampleList::diff(
                $firstSample->benchmarkSamples,
                $secondSample->benchmarkSamples,
            )
        );
    }
}
