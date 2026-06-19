<?php

declare(strict_types=1);

namespace Neos\ContentRepository\BenchmarkTests\Testing\Behat;

use Behat\Behat\Context\Context as BehatContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Neos\Behat\FlowBootstrapTrait;
use Neos\ContentRepository\Core\ContentRepository;
use Neos\ContentRepository\Core\DimensionSpace\DimensionSpacePoint;
use Neos\ContentRepository\Core\Factory\ContentRepositoryServiceFactoryInterface;
use Neos\ContentRepository\Core\Factory\ContentRepositoryServiceInterface;
use Neos\ContentRepository\Core\SharedModel\ContentRepository\ContentRepositoryId;
use Neos\ContentRepository\Core\SharedModel\Workspace\WorkspaceName;
use Neos\ContentRepository\TestSuite\Behavior\Features\Bootstrap\CRBehavioralTestsSubjectProvider;
use Neos\ContentRepository\TestSuite\Behavior\Features\Bootstrap\CRTestSuiteTrait;
use Neos\ContentRepository\TestSuite\Fakes\FakeContentDimensionSourceFactory;
use Neos\ContentRepository\TestSuite\Fakes\FakeNodeTypeManagerFactory;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;

final class BenchmarkFeatureContext implements BehatContext
{
    use FlowBootstrapTrait;
    use CRTestSuiteTrait;
    use CRBehavioralTestsSubjectProvider;
    use BulkNodeOperations;
    use BenchmarkSampling;

    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    public function __construct()
    {
        self::bootstrapFlow();

        $this->contentRepositoryRegistry = $this->getObject(ContentRepositoryRegistry::class);
    }

    public function afterScenarioEnsureIntegrityViolationDetectionWasRun(): void
    {
        // TODO check reports cyclic graph for 03-DeepGraph.feature which cannot be true?!
        // Also enabling these causes some performance overhead and the detection checks are not cheap on large datasets - and dont have to be?
    }

    /**
     * @BeforeScenario
     */
    public function resetContentRepositoryComponents(BeforeScenarioScope $scope): void
    {
        FakeContentDimensionSourceFactory::reset();
        FakeNodeTypeManagerFactory::reset();
    }

    // TODO move to neos core
    final protected function requireCurrentContentRepository(): ContentRepository
    {
        return $this->currentContentRepository ?? throw new \RuntimeException(<<<MESSAGE
        No current content repository defined. Please use the step
        
        And I am in content repository "default"
        MESSAGE, 1781889678);
    }

    // TODO move to neos core
    final protected function requireCurrentWorkspaceName(): WorkspaceName
    {
        return $this->currentWorkspaceName ?? throw new \RuntimeException(<<<MESSAGE
        No current workspace defined. Please use the step
        
        And I am in workspace "live"
        
        Or
        
        And I am in workspace "live" and dimension space point {"language": "de"}
        MESSAGE, 1781889678);
    }

    // TODO move to neos core
    final protected function requireCurrentDimensionSpacePoint(): DimensionSpacePoint
    {
        return $this->currentDimensionSpacePoint ?? throw new \RuntimeException(<<<MESSAGE
        No current dimension space point defined. Please use the step
        
        And I am in dimension space point {"language": "de"}
        
        Or
        
        And I am in workspace "live" and dimension space point {"language": "de"}
        MESSAGE, 1781889678);
    }

    /**
     * Access content repository services.
     *
     * @template T of ContentRepositoryServiceInterface
     * @param ContentRepositoryServiceFactoryInterface<T> $factory
     * @return T
     */
    protected function getContentRepositoryService(
        ContentRepositoryServiceFactoryInterface $factory
    ): ContentRepositoryServiceInterface {
        return $this->contentRepositoryRegistry->buildService(
            $this->requireCurrentContentRepository()->id,
            $factory
        );
    }

    protected function createContentRepository(
        ContentRepositoryId $contentRepositoryId
    ): ContentRepository {
        $this->contentRepositoryRegistry->resetFactoryInstance($contentRepositoryId);
        $contentRepository = $this->contentRepositoryRegistry->get($contentRepositoryId);
        FakeContentDimensionSourceFactory::reset();
        FakeNodeTypeManagerFactory::reset();

        return $contentRepository;
    }
}
