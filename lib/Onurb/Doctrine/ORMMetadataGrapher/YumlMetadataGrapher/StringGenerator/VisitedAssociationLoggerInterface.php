<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

interface VisitedAssociationLoggerInterface
{
    /**
     * Visit a given association and mark it as visited
     *
     * @param string      $className
     * @param string|null $association
     *
     * @return bool true if the association was visited before
     */
    public function visitAssociation($className, $association = null);

    /**
     * @param string $className
     * @param string|null $association
     * @return bool
     */
    public function isVisitedAssociation($className, $association = null);
}
