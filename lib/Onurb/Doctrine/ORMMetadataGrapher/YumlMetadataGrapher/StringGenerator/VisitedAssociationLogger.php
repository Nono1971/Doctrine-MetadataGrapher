<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

class VisitedAssociationLogger implements VisitedAssociationLoggerInterface
{
    /**
     * Temporary array where already visited collections are stored
     *
     * @var array
     */
    private $visitedAssociations = array();

    /**
     * Visit a given association and mark it as visited
     *
     * @param string      $className
     * @param string|null $association
     *
     * @return bool true if the association was visited before
     */
    public function visitAssociation($className, $association = null)
    {
        if (null === $association) {
            return $this->visitSingleSidedAssociation($className);
        }

        return $this->visitDoubleSidedAssociation($className, $association);
    }

    /**
     * @param string $className
     * @param string|null $association
     * @return bool
     */
    public function isVisitedAssociation($className, $association = null)
    {
        return null === $association ?
            isset($this->visitedAssociations[$className]) : isset($this->visitedAssociations[$className][$association]);
    }

    /**
     * @param string $className
     * @return bool
     */
    protected function visitSingleSidedAssociation($className)
    {
        if ($this->isVisitedAssociation($className)) {
            return false;
        }

        $this->visitedAssociations[$className] = array();

        return true;
    }

    /**
     * @param string $className
     * @param string $association
     * @return bool
     */
    protected function visitDoubleSidedAssociation($className, $association)
    {
        $this->visitSingleSidedAssociation($className);

        if ($this->isVisitedAssociation($className, $association)) {
            return false;
        }

        $this->visitedAssociations[$className][$association] = true;

        return true;
    }
}
