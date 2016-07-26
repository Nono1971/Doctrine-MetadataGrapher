<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

interface StringGeneratorHelperInterface
{
    /**
     * @param string $className
     * @param array $fields
     * @param array $methods
     * @return string
     */
    public function getClassText($className, $fields, $methods = array());


    /**
     * @param string $class1String
     * @param boolean $isInverse
     * @param string $association
     * @param int $class1Count
     * @param string $targetClassName
     * @return string
     */
    public function makeSingleSidedLinkString(
        $class1String,
        $isInverse,
        $association,
        $class1Count,
        $targetClassName
    );

    /**
     * @param string $class1String
     * @param string $class2String
     * @param boolean $bidirectional
     * @param boolean $isInverse
     * @param string $class2SideName
     * @param integer $class2Count
     * @param string $class1SideName
     * @param integer $class1Count
     *
     * @return string
     */
    public function makeDoubleSidedLinkString(
        $class1String,
        $class2String,
        $bidirectional,
        $isInverse,
        $class2SideName,
        $class2Count,
        $class1SideName,
        $class1Count
    );
}
