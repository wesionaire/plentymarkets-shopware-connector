<?php

namespace SystemConnector\DefinitionProvider;

use SystemConnector\ValueObject\Definition\Definition;
use Traversable;

class DefinitionProvider implements DefinitionProviderInterface
{
    /**
     * @var Definition[]
     */
    private $connectorDefinitions;

    /**
     * @var Definition[]
     */
    private $mappingDefinitions;

    /**
     * @var Definition[]
     */
    private $cleanupDefinitions;

    /**
     * @param Definition[]|Traversable $connectorDefinitions
     * @param Definition[]|Traversable $mappingDefinitions
     * @param Definition[]|Traversable $cleanupDefinitions
     */
    public function __construct(Traversable $connectorDefinitions, Traversable $mappingDefinitions, Traversable $cleanupDefinitions)
    {
        $connectorDefinitions = iterator_to_array($connectorDefinitions);
        $mappingDefinitions = iterator_to_array($mappingDefinitions);
        $cleanupDefinitions = iterator_to_array($cleanupDefinitions);

        $this->connectorDefinitions = $this->filterActiveDefinitions($this->sortDefinitions($connectorDefinitions));
        $this->mappingDefinitions = $this->filterActiveDefinitions($this->sortDefinitions($mappingDefinitions));
        $this->cleanupDefinitions = $this->filterActiveDefinitions($this->sortDefinitions($cleanupDefinitions));
    }

    /**
     * @param null|string $objectType
     *
     * @return Definition[]
     */
    public function getConnectorDefinitions($objectType = null)
    {
        return $this->filterMatchingDefinitions($this->connectorDefinitions, $objectType);
    }

    /**
     * @param null|string $objectType
     *
     * @return Definition[]
     */
    public function getMappingDefinitions($objectType = null)
    {
        return $this->filterMatchingDefinitions($this->mappingDefinitions, $objectType);
    }

    /**
     * @return Definition[]
     */
    public function getCleanupDefinitions()
    {
        return $this->cleanupDefinitions;
    }

    /**
     * @param Definition[] $definitions
     *
     * @return Definition[]
     */
    private function filterActiveDefinitions(array $definitions)
    {
        return array_filter($definitions, function (Definition $definition) {
            if (!$definition->isActive()) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param Definition[] $definitions
     * @param null|string  $objectType
     *
     * @return Definition[]
     */
    private function filterMatchingDefinitions(array $definitions, $objectType)
    {
        return array_filter($definitions, function (Definition $definition) use ($objectType) {
            return strtolower($definition->getObjectType()) === strtolower($objectType) || null === $objectType;
        });
    }

    /**
     * @param Definition[] $definitions
     *
     * @return Definition[]
     */
    private function sortDefinitions(array $definitions)
    {
        usort($definitions, function (Definition $definitionLeft, Definition $definitionRight) {
            if ($definitionLeft->getPriority() === $definitionRight->getPriority()) {
                return 0;
            }

            return ($definitionLeft->getPriority() > $definitionRight->getPriority()) ? -1 : 1;
        });

        return $definitions;
    }
}
