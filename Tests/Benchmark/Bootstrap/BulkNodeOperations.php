<?php

declare(strict_types=1);

use Behat\Behat\Hook\Scope\AfterFeatureScope;
use Behat\Hook\AfterFeature;
use Behat\Hook\AfterScenario;
use Behat\Step\When;
use Neos\ContentRepository\Core\DimensionSpace\OriginDimensionSpacePoint;
use Neos\ContentRepository\Core\Feature\NodeCreation\Command\CreateNodeAggregateWithNode;
use Neos\ContentRepository\Core\NodeType\NodeTypeName;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindAncestorNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindChildNodesFilter;
use Neos\ContentRepository\Core\Projection\ContentGraph\Filter\FindDescendantNodesFilter;
use Neos\ContentRepository\Core\SharedModel\Node\NodeAggregateId;
use Neos\Utility\Files;

trait BulkNodeOperations
{
    #[When('I create descendants of node :parentNodeAggregateId of type :nodeTypeName and depth :depth and breadth :breadth as sample :sampleName')]
    public function createDescendants(string $parentNodeAggregateId, string $nodeTypeName, int $depth, int $breadth, string $sampleName): void
    {
        $nodeNumber = 1;
        $now = microtime(true);
        $this->createDescendantNodes(
            parentNodeAggregateId: NodeAggregateId::fromString($parentNodeAggregateId),
            nodeTypeName: NodeTypeName::fromString($nodeTypeName),
            depth: $depth,
            breadth: $breadth,
            currentDepth: 1,
            nodeNumber: $nodeNumber,
        );
        $commandRuntime = (int)((microtime(true) - $now) * 1000);

        $now = microtime(true);
        $this->getCurrentSubgraph()->findNodeById(NodeAggregateId::fromString($parentNodeAggregateId));
        $idQueryTime = (int)((microtime(true) - $now) * 1000000);

        $now = microtime(true);
        $this->getCurrentSubgraph()->findChildNodes(NodeAggregateId::fromString($parentNodeAggregateId), FindChildNodesFilter::create());
        $childrenQueryTime = (int)((microtime(true) - $now) * 1000000);

        $now = microtime(true);
        $this->getCurrentSubgraph()->findParentNode(NodeAggregateId::fromString($parentNodeAggregateId . '-1'));
        $parentQueryTime = (int)((microtime(true) - $now) * 1000000);

        $now = microtime(true);
        $this->getCurrentSubgraph()->findDescendantNodes(NodeAggregateId::fromString($parentNodeAggregateId), FindDescendantNodesFilter::create());
        $descendantsQueryTime = (int)((microtime(true) - $now) * 1000000);

        $now = microtime(true);
        $this->getCurrentSubgraph()->findAncestorNodes(NodeAggregateId::fromString($parentNodeAggregateId . '-' . $nodeNumber), FindAncestorNodesFilter::create());
        $ancestorsQueryTime = (int)((microtime(true) - $now) * 1000000);

        BenchmarkSamples::addSample(
            sampleName: $sampleName,
            sample: new BenchmarkSample(
                depth: $depth,
                // breath: $breadth,
                commandRuntime: $commandRuntime,
                idQueryTime: $idQueryTime,
                childrenQueryTime: $childrenQueryTime,
                parentQueryTime: $parentQueryTime,
                descendantsQueryTime: $descendantsQueryTime,
                ancestorsQueryTime: $ancestorsQueryTime,
            )
        );
    }

    #[AfterFeature]
    public static function writeAbsoluteTime(AfterFeatureScope $ctx)
    {
        $featureName = pathinfo($ctx->getFeature()->getFile(), PATHINFO_FILENAME);

        Files::createDirectoryRecursively($dir = FLOW_PATH_DATA . 'Benchmark-' . getmypid());

        file_put_contents($dir . '/' . $featureName . '.json', json_encode(BenchmarkSamples::getSamples(), JSON_PRETTY_PRINT));
    }

    private function createDescendantNodes(NodeAggregateId $parentNodeAggregateId, NodeTypeName $nodeTypeName, int $depth, int $breadth, int $currentDepth, int &$nodeNumber): void
    {
        for ($i = 1; $i <= $breadth; $i++) {
            $nodeAggregateId = NodeAggregateId::fromString($parentNodeAggregateId . '-' . $nodeNumber);
            $this->currentContentRepository->handle(
                CreateNodeAggregateWithNode::create(
                    workspaceName: $this->currentWorkspaceName,
                    nodeAggregateId: $nodeAggregateId,
                    nodeTypeName: $nodeTypeName,
                    originDimensionSpacePoint: OriginDimensionSpacePoint::fromDimensionSpacePoint($this->currentDimensionSpacePoint),
                    parentNodeAggregateId: $parentNodeAggregateId,
                )
            );
            $nodeNumber++;
            if ($currentDepth < $depth) {
                $this->createDescendantNodes(
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
