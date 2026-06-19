<?php

declare(strict_types=1);

use Behat\Behat\Hook\Scope\AfterFeatureScope;
use Behat\Hook\AfterFeature;
use Behat\Hook\AfterSuite;
use Behat\Step\When;
use Neos\ContentRepository\BenchmarkTests\BenchmarkContentGraphQueryTime;
use Neos\ContentRepository\BenchmarkTests\BenchmarkSample;
use Neos\ContentRepository\BenchmarkTests\BenchmarkSampleStaticRegistry;
use Neos\ContentRepository\BenchmarkTests\BenchmarkSubgraphQueryTime;
use Neos\ContentRepository\Core\DimensionSpace\OriginDimensionSpacePoint;
use Neos\ContentRepository\Core\Feature\NodeCreation\Command\CreateNodeAggregateWithNode;
use Neos\ContentRepository\Core\Feature\NodeReferencing\Command\SetNodeReferences;
use Neos\ContentRepository\Core\Feature\NodeReferencing\Dto\NodeReferencesForName;
use Neos\ContentRepository\Core\Feature\NodeReferencing\Dto\NodeReferencesToWrite;
use Neos\ContentRepository\Core\Feature\NodeReferencing\Dto\NodeReferenceToWrite;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindAncestorNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindBackReferencesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindDescendantNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindReferencesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindSubtreeFilter;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\ContentRepository\Core\SharedModel\Node\ReferenceName;
use Neos\Utility\Files;

trait BulkNodeOperations
{
    #[When('I create descendants of node :parentNodeAggregateId of type :nodeTypeName and depth :depth and breadth :breadth as sample :sampleName')]
    public function createDescendants(string $parentNodeAggregateId, string $nodeTypeName, int $depth, int $breadth, string $sampleName): void
    {
        $nodeNumber = 0;
        $now = microtime(true);
        // create an additional node as deterministic and viable reference target
        $this->currentContentRepository->handle(
            CreateNodeAggregateWithNode::create(
                workspaceName: $this->currentWorkspaceName,
                nodeAggregateId: NodeAggregateId::fromString($parentNodeAggregateId . '-' . $nodeNumber),
                nodeTypeName: NodeTypeName::fromString($nodeTypeName),
                originDimensionSpacePoint: OriginDimensionSpacePoint::fromDimensionSpacePoint($this->currentDimensionSpacePoint),
                parentNodeAggregateId: NodeAggregateId::fromString($parentNodeAggregateId),
            )
        );
        $nodeNumber++;
        $this->createDescendantNodes(
            baseNodeAggregateId: NodeAggregateId::fromString($parentNodeAggregateId),
            parentNodeAggregateId: NodeAggregateId::fromString($parentNodeAggregateId . '-0'),
            nodeTypeName: NodeTypeName::fromString($nodeTypeName),
            depth: $depth,
            breadth: $breadth,
            currentDepth: 1,
            nodeNumber: $nodeNumber,
        );
        $commandRuntime = (int)((microtime(true) - $now) * 1000);

        // ToDo include default subtree tag filtering to align to real world
        $subgraphQueryTime = new BenchmarkSubgraphQueryTime(
            idQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findNodeById(NodeAggregateId::fromString($parentNodeAggregateId))
            ),
            childrenQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findChildNodes(NodeAggregateId::fromString($parentNodeAggregateId), FindChildNodesFilter::create())
            ),
            parentQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findParentNode(NodeAggregateId::fromString($parentNodeAggregateId . '-1'))
            ),
            descendantsQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findDescendantNodes(NodeAggregateId::fromString($parentNodeAggregateId), FindDescendantNodesFilter::create())
            ),
            subtreeQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findSubtree(NodeAggregateId::fromString($parentNodeAggregateId), FindSubtreeFilter::create())
            ),
            ancestorsQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findAncestorNodes(NodeAggregateId::fromString($parentNodeAggregateId . '-' . $nodeNumber), FindAncestorNodesFilter::create())
            ),
            referenceQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findReferences(
                    nodeAggregateId: NodeAggregateId::fromString($parentNodeAggregateId . '-' . $nodeNumber),
                    filter: FindReferencesFilter::create(referenceName: ReferenceName::fromString('reference'))
                ),
            ),
            backReferenceQueryTime: self::measureAverageInMicroseconds(
                fn () => $this->getCurrentSubgraph()->findBackReferences(
                    nodeAggregateId: NodeAggregateId::fromString($parentNodeAggregateId),
                    filter: FindBackReferencesFilter::create(referenceName: ReferenceName::fromString('reference'))
                ),
            )
        );

        $contentGraph = $this->currentContentRepository->getContentGraph($this->currentWorkspaceName);
        $contentGraphQueryTime = new BenchmarkContentGraphQueryTime(
            idQueryTime: self::measureAverageInMicroseconds(
                fn () => $contentGraph->findNodeAggregateById(NodeAggregateId::fromString($parentNodeAggregateId))
            ),
            childrenQueryTime: self::measureAverageInMicroseconds(
                fn () => $contentGraph->findChildNodeAggregates(NodeAggregateId::fromString($parentNodeAggregateId))
            ),
            parentQueryTime: self::measureAverageInMicroseconds(
                fn () => $contentGraph->findParentNodeAggregates(NodeAggregateId::fromString($parentNodeAggregateId . '-1'))
            ),
            ancestorsQueryTime: self::measureAverageInMicroseconds(
                fn () => $contentGraph->findAncestorNodeAggregateIds(NodeAggregateId::fromString($parentNodeAggregateId . '-' . $nodeNumber))
            )
        );

        BenchmarkSampleStaticRegistry::addSample(
            sampleName: $sampleName,
            sample: new BenchmarkSample(
                name: $sampleName,
                depth: $depth,
                breadth: $breadth,
                commandRuntime: $commandRuntime,
                subgraphQueryTime: $subgraphQueryTime,
                contentGraphQueryTime: $contentGraphQueryTime,
            )
        );
    }

    public static function measureAverageInMicroseconds(\Closure $fn): int
    {
        $ITERATIONS = 6;
        $now = microtime(true);
        for ($i = 0; $i < $ITERATIONS; $i++) {
            $fn();
        }
        $time = (int)(((microtime(true) - $now) / $ITERATIONS) * 1000000);
        return $time;
    }

    #[AfterFeature]
    public static function writeAbsoluteTime(AfterFeatureScope $ctx)
    {
        $featureName = pathinfo($ctx->getFeature()->getFile(), PATHINFO_FILENAME);

        Files::createDirectoryRecursively($dir = FLOW_PATH_DATA . 'Benchmark-' . getmypid());

        file_put_contents($dir . '/' . $featureName . '.json', json_encode(BenchmarkSampleStaticRegistry::getSamples(), JSON_PRETTY_PRINT));
    }

    #[AfterSuite]
    public static function printWhereBenchmarksWereWritten()
    {
        fputs(STDOUT, sprintf(
            "\n\n\nAbsolute times written to '%s'\nUse 'flow crbenchmark:compare Benchmark-Base %s' to compare two sets.\n",
            FLOW_PATH_DATA . 'Benchmark-' . getmypid(),
            'Benchmark-' . getmypid()
        ));
    }

    private function createDescendantNodes(NodeAggregateId $baseNodeAggregateId, NodeAggregateId $parentNodeAggregateId, NodeTypeName $nodeTypeName, int $depth, int $breadth, int $currentDepth, int &$nodeNumber): void
    {
        // TODO Using non uuid node aggregate id might be harder for the database to optimise? -> use pure UUIDs in testcase?
        for ($i = 1; $i <= $breadth; $i++) {
            $nodeAggregateId = NodeAggregateId::fromString($baseNodeAggregateId . '-' . $nodeNumber);
            $this->currentContentRepository->handle(
                CreateNodeAggregateWithNode::create(
                    workspaceName: $this->currentWorkspaceName,
                    nodeAggregateId: $nodeAggregateId,
                    nodeTypeName: $nodeTypeName,
                    originDimensionSpacePoint: OriginDimensionSpacePoint::fromDimensionSpacePoint($this->currentDimensionSpacePoint),
                    parentNodeAggregateId: $parentNodeAggregateId,
                )
            );
            $this->currentContentRepository->handle(
                SetNodeReferences::create(
                    workspaceName: $this->currentWorkspaceName,
                    sourceNodeAggregateId: $nodeAggregateId,
                    sourceOriginDimensionSpacePoint: OriginDimensionSpacePoint::fromDimensionSpacePoint($this->currentDimensionSpacePoint),
                    references: NodeReferencesToWrite::create(
                        NodeReferencesForName::fromReferences(
                            name: ReferenceName::fromString('reference'),
                            references: [
                                NodeReferenceToWrite::fromTarget(NodeAggregateId::fromString($baseNodeAggregateId->value . '-0')),
                            ],
                        )
                    ),
                ),
            );
            $nodeNumber++;
            if ($currentDepth < $depth) {
                $this->createDescendantNodes(
                    baseNodeAggregateId: $baseNodeAggregateId,
                    parentNodeAggregateId: $nodeAggregateId,
                    nodeTypeName: $nodeTypeName,
                    depth: $depth,
                    breadth: $breadth,
                    currentDepth: $currentDepth + 1,
                    nodeNumber: $nodeNumber,
                );
            }
        }
    }
}
