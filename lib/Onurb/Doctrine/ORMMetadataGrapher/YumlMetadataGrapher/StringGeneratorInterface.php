<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

interface StringGeneratorInterface
{

    /**
     * @return array
     */
    public function getVisitedAssociations();

    /**
     * Build the string representing the single graph item
     *
     * @param ClassMetadata $class
     *
     * @return string
     */
    public function getClassString(ClassMetadata $class);

    /**
     * @param ClassMetadata $class1
     * @param string $association
     * @return string
     */
    public function getAssociationString(ClassMetadata $class1, $association);

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
