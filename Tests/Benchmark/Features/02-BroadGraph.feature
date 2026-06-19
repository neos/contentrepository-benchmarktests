@contentrepository @adapters=DoctrineDBAL,Postgres
Feature: Run benchmark tests on a broad graph, i.e. all children for a single parent

  Background: The stage is set
    Given using no content dimensions
    And using the following node types:
    """yaml
    'Neos.ContentRepository:Root': []
    'Neos.ContentRepository.Testing:Node':
      references:
        reference:
          constraints: []
    """
    And using identifier "t_benchmark", I define a content repository
    And I am in content repository "t_benchmark"
    And I am user identified by "initiating-user-identifier"
    And the command CreateRootWorkspace is executed with payload:
      | Key                  | Value                |
      | workspaceName        | "live"               |
      | newContentStreamId   | "cs-identifier"      |
    And I am in workspace "live" and dimension space point {}
    And the command CreateRootNodeAggregateWithNode is executed with payload:
      | Key             | Value                         |
      | nodeAggregateId | "lady-eleonode-rootford"      |
      | nodeTypeName    | "Neos.ContentRepository:Root" |

  Scenario: Create a broad graph of 11,112 nodes
    And I create descendants of node "lady-eleonode-rootford" of type "Neos.ContentRepository.Testing:Node" and depth 1 and breadth 11110 as sample firstSample

