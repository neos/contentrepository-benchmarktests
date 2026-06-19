<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests\Testing\Behat;

use Neos\ContentRepository\BenchmarkTests\BenchmarkSample;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final class BenchmarkSampleStaticRegistry
{
    /**
     * @var array<string,BenchmarkSample>
     */
    private static array $samples;

    public static function reset(): void
    {
        self::$samples = [];
    }

    public static function addSample(string $sampleName, BenchmarkSample $sample): void
    {
        self::$samples[$sampleName] = $sample;
    }

    public static function getSample(string $sampleName): BenchmarkSample
    {
        return self::$samples[$sampleName] ?? throw new \RuntimeException(sprintf('Sample %s does not exist', $sampleName), 1781889148);
    }

    /**
     * @return array<string,BenchmarkSample>
     */
    public static function getSamples(): array
    {
        return self::$samples;
    }
}
