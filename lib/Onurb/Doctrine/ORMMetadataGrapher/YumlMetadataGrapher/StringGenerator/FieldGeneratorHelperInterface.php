<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

interface FieldGeneratorHelperInterface
{
    /**
     * @param ClassMetadata $class
     * @param $fieldName
     * @return string
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function getFullField(ClassMetadata $class, $fieldName);
}
