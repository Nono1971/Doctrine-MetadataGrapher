<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\VisitedAssociationLoggerInterface;

interface StringGeneratorInterface
{
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
     * @return VisitedAssociationLoggerInterface
     */
    public function getAssociationLogger();
}
