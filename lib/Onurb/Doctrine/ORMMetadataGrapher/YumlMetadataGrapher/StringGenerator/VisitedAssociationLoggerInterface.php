<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

interface VisitedAssociationLoggerInterface
{

    /**
     * @return array
     */
    public function getVisitedAssociations();

    /**
     * Visit a given association and mark it as visited
     *
     * @param string      $className
     * @param string|null $association
     *
     * @return bool true if the association was visited before
     */
    public function visitAssociation($className, $association = null);
}
