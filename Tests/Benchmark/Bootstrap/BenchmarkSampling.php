<?php

declare(strict_types=1);

use Behat\Hook\BeforeFeature;
use Behat\Step\Then;
use Behat\Step\When;
use Neos\ContentRepository\BenchmarkTests\BenchmarkSampleStaticRegistry;
use Neos\ContentRepository\BenchmarkTests\GrowthFactor;
use PHPUnit\Framework\Assert;

trait BenchmarkSampling
{
    #[When('I decide on sampling begin with result :beginSampling')]
    public function iDecideOnSamplingBegin(string $beginSampling): void
    {
        // TODO can be removed, is obsolete with resetSamplingForFeature
        if ($beginSampling === 'true') {
            BenchmarkSampleStaticRegistry::reset();
        }
    }

    #[BeforeFeature]
    public static function resetSamplingForFeature(): void
    {
        BenchmarkSampleStaticRegistry::reset();
    }

    public static function isExpectRelativeGrowthEnabled(): bool
    {
        // TODO does not work correctly on different machines yet. Expectations dont match
        return (bool)getenv('EXPECT_RELATIVE_GROWTH');
    }

    #[Then('I expect linear runtime growth between samples :firstSample and :secondSample with expected factor :expectedFactor')]
    public function iExpectLinearRuntimeGrowth(string $firstSample, string $secondSample, int $expectedFactor): void
    {
        if (!self::isExpectRelativeGrowthEnabled()) {
            return;
        }

        $expectedGrowthFactor = GrowthFactor::tryFrom($expectedFactor);
        if ($firstSample === 'null' && $secondSample === 'null' && $expectedGrowthFactor === null) {
            return;
        }
        $actualNormalizedGrowth = (
            BenchmarkSampleStaticRegistry::getSample($secondSample)->commandRuntime
            / BenchmarkSampleStaticRegistry::getSample($firstSample)->commandRuntime
        );
        Assert::assertLessThan(
            expected: $expectedGrowthFactor->getLinear(),
            actual: $actualNormalizedGrowth,
            message: 'Runtime growth appears to be non-linear, normalized growth is ' . $actualNormalizedGrowth . '.'
        );
    }

    #[Then('I expect logarithmic ID query time growth between samples :firstSample and :secondSample with expected factor :expectedFactor')]
    public function iExpectLogarithmicIDQueryGrowth(string $firstSample, string $secondSample, int $expectedFactor): void
    {
        if (!self::isExpectRelativeGrowthEnabled()) {
            return;
        }

        $expectedGrowthFactor = GrowthFactor::tryFrom($expectedFactor);
        if ($firstSample === 'null' && $secondSample === 'null' && $expectedGrowthFactor === null) {
            return;
        }
        $actualNormalizedGrowth = (
            BenchmarkSampleStaticRegistry::getSample($secondSample)->idQueryTime
            / BenchmarkSampleStaticRegistry::getSample($firstSample)->idQueryTime
        );
        Assert::assertLessThan(
            expected: $expectedGrowthFactor->getLogarithmic(),
            actual: $actualNormalizedGrowth,
            message: 'ID query time growth appears to be non-logarithmic, normalized growth is ' . $actualNormalizedGrowth . '.'
        );
    }

    #[Then('I expect logarithmic child query time growth between samples :firstSample and :secondSample with expected factor :expectedFactor')]
    public function iExpectLogarithmicChildQueryGrowth(string $firstSample, string $secondSample, int $expectedFactor): void
    {
        if (!self::isExpectRelativeGrowthEnabled()) {
            return;
        }

        $expectedGrowthFactor = GrowthFactor::tryFrom($expectedFactor);
        if ($firstSample === 'null' && $secondSample === 'null' && $expectedGrowthFactor === null) {
            return;
        }
        $actualNormalizedGrowth = (
            BenchmarkSampleStaticRegistry::getSample($secondSample)->childrenQueryTime
            / BenchmarkSampleStaticRegistry::getSample($firstSample)->childrenQueryTime
        );
        Assert::assertLessThan(
            expected: $expectedGrowthFactor->getLogarithmic(),
            actual: $actualNormalizedGrowth,
            message: 'Children query time growth appears to be non-logarithmic, normalized growth is ' . $actualNormalizedGrowth . '.'
        );
    }

    #[Then('I expect logarithmic parent query time growth between samples :firstSample and :secondSample with expected factor :expectedFactor')]
    public function iExpectLogarithmicParentQueryGrowth(string $firstSample, string $secondSample, int $expectedFactor): void
    {
        if (!self::isExpectRelativeGrowthEnabled()) {
            return;
        }

        $expectedGrowthFactor = GrowthFactor::tryFrom($expectedFactor);
        if ($firstSample === 'null' && $secondSample === 'null' && $expectedGrowthFactor === null) {
            return;
        }
        $actualNormalizedGrowth = (
            BenchmarkSampleStaticRegistry::getSample($secondSample)->parentQueryTime
            / BenchmarkSampleStaticRegistry::getSample($firstSample)->parentQueryTime
        );
        Assert::assertLessThan(
            expected: $expectedGrowthFactor->getLogarithmic(),
            actual: $actualNormalizedGrowth,
            message: 'Parent query time growth appears to be non-logarithmic, normalized growth is ' . $actualNormalizedGrowth . '.'
        );
    }

    #[Then('I expect logarithmic descendant query time growth between samples :firstSample and :secondSample with expected factor :expectedFactor')]
    public function iExpectLogarithmicDescendantQueryGrowth(string $firstSampleName, string $secondSampleName, int $expectedFactor): void
    {
        if (!self::isExpectRelativeGrowthEnabled()) {
            return;
        }

        $expectedGrowthFactor = GrowthFactor::tryFrom($expectedFactor);
        if ($firstSampleName === 'null' && $secondSampleName === 'null' && $expectedGrowthFactor === null) {
            return;
        }
        $firstSample = BenchmarkSampleStaticRegistry::getSample($firstSampleName);
        $secondSample = BenchmarkSampleStaticRegistry::getSample($secondSampleName);
        $actualGrowth = ($secondSample->descendantsQueryTime / $firstSample->descendantsQueryTime);
        $maximumExpectedGrowth = $expectedGrowthFactor->getLogarithmic($secondSample->depth - $firstSample->depth);
        Assert::assertLessThan(
            expected: $maximumExpectedGrowth,
            actual: $actualGrowth,
            message: 'Descendant query time growth appears to be non-logarithmic, normalized growth is ' . $actualGrowth . ' instead of maximum expected ' . $maximumExpectedGrowth . '.'
        );
    }

    #[Then('I expect logarithmic ancestor query time growth between samples :firstSample and :secondSample with expected factor :expectedFactor')]
    public function iExpectLogarithmicAncestorQueryGrowth(string $firstSampleName, string $secondSampleName, int $expectedFactor): void
    {
        if (!self::isExpectRelativeGrowthEnabled()) {
            return;
        }

        $expectedGrowthFactor = GrowthFactor::tryFrom($expectedFactor);
        if ($firstSampleName === 'null' && $secondSampleName === 'null' && $expectedGrowthFactor === null) {
            return;
        }
        $firstSample = BenchmarkSampleStaticRegistry::getSample($firstSampleName);
        $secondSample = BenchmarkSampleStaticRegistry::getSample($secondSampleName);
        $actualGrowth = ($secondSample->descendantsQueryTime / $firstSample->descendantsQueryTime);
        $maximumExpectedGrowth = $expectedGrowthFactor->getLogarithmic($secondSample->depth - $firstSample->depth);
        Assert::assertLessThan(
            expected: $maximumExpectedGrowth,
            actual: $actualGrowth,
            message: 'Ancestor query time growth appears to be non-logarithmic, normalized growth is ' . $actualGrowth . ' instead of maximum expected ' . $maximumExpectedGrowth . '.'
        );
    }
}
