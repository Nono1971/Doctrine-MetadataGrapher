<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\StringGeneratorHelper;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\StringGeneratorHelperInterface;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\VisitedAssociationLogger;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\VisitedAssociationLoggerInterface;

class StringGenerator implements StringGeneratorInterface
{

    /**
     * @var array
     */
    protected $classStrings;

    /**
     * @var StringGeneratorHelperInterface
     */
    protected $stringHelper;


    /**
     * @var ClassStoreInterface
     */
    protected $classStore;

    /**
     * @var VisitedAssociationLoggerInterface
     */
    protected $associationLogger;

    /**
     * @param ClassStoreInterface $classStore
     */
    public function __construct(ClassStoreInterface $classStore)
    {
        $this->classStore = $classStore;
        $this->associationLogger = new VisitedAssociationLogger();
        $this->stringHelper = new StringGeneratorHelper();
    }

    /**
     * @return VisitedAssociationLoggerInterface
     */
    public function getAssociationLogger()
    {
        return $this->associationLogger;
    }

    /**
     * Build the string representing the single graph item
     *
     * @param ClassMetadata $class
     *
     * @return string
     */
    public function getClassString(ClassMetadata $class)
    {
        $className = $class->getName();

        if (!isset($this->classStrings[$className])) {
            $this->associationLogger->visitAssociation($className);

            $parentFields = $this->getParentFields($class);
            $fields       = $this->getClassFields($class, $parentFields);

            $this->classStrings[$className] = $this->stringHelper->getClassText($className, $fields);
        }

        return $this->classStrings[$className];
    }

    /**
     * Recursive function to get all fields in inheritance
     *
     * @param ClassMetadata $class
     * @param array $fields
     * @return array
     */
    public function getParentFields(ClassMetadata $class, $fields = array())
    {
        if ($parent = $this->classStore->getParent($class)) {
            $parentFields = $parent->getFieldNames();
            foreach ($parentFields as $field) {
                if (!in_array($field, $fields)) {
                    $fields[] = $field;
                }
            }
            $fields = $this->getParentFields($parent, $fields);
        }

        return $fields;
    }

    /**
     * @param ClassMetadata $class1
     * @param string $association
     * @return string
     */
    public function getAssociationString(ClassMetadata $class1, $association)
    {
        $targetClassName  = $class1->getAssociationTargetClass($association);
        $class2           = $this->classStore->getClassByName($targetClassName);
        $isInverse        = $class1->isAssociationInverseSide($association);
        $associationCount = $this->getClassCount($class1, $association);

        if (null === $class2) {
            return $this->stringHelper->makeSingleSidedLinkString(
                $this->getClassString($class1),
                $isInverse,
                $association,
                $associationCount,
                $targetClassName
            );
        }

        $reverseAssociationName = $this->getClassReverseAssociationName($class1, $association);

        $reverseAssociationCount = 0;
        $bidirectional = $this->isBidirectional(
            $reverseAssociationName,
            $isInverse,
            $class2
        );

        if ($bidirectional) {
            $reverseAssociationCount = $this->getClassCount($class2, $reverseAssociationName);
            $bidirectional = true;
        }

        $this->associationLogger->visitAssociation($targetClassName, $reverseAssociationName);

        return $this->stringHelper->makeDoubleSidedLinkString(
            $this->getClassString($class1),
            $this->getClassString($class2),
            $bidirectional,
            $isInverse,
            $reverseAssociationName,
            $reverseAssociationCount,
            $association,
            $associationCount
        );
    }

    /**
     * @param boolean $isInverse
     * @param string|null $reverseAssociationName
     * @param ClassMetadata $class2
     * @return bool
     */
    private function isBidirectional(
        $reverseAssociationName,
        $isInverse,
        ClassMetadata $class2
    ) {
        return null !== $reverseAssociationName
        && ($isInverse || $class2->isAssociationInverseSide($reverseAssociationName));
    }

    /**
     * @param ClassMetadata $class
     * @param string $association
     * @return int
     */
    private function getClassCount(ClassMetadata $class, $association)
    {
        return $class->isCollectionValuedAssociation($association) ? 2 : 1;
    }
    
    /**
     * @param ClassMetadata $class
     * @param array $parentFields
     * @return array
     */
    private function getClassFields(ClassMetadata $class, $parentFields)
    {
        $fields = array();

        foreach ($class->getFieldNames() as $fieldName) {
            if (in_array($fieldName, $parentFields)) {
                continue;
            }

            $fields[] = $class->isIdentifier($fieldName) ? '+' . $fieldName : $fieldName;
        }

        return $fields;
    }

    /**
     * Returns the $class2 association name for $class1 if reverse related (or null if not)
     *
     * @param ClassMetadata $class1
     * @param string $association
     *
     * @return string|null
     */
    private function getClassReverseAssociationName(ClassMetadata $class1, $association)
    {
        if ($class1->getAssociationMapping($association)['isOwningSide']) {
            return $class1->getAssociationMapping($association)['inversedBy'];
        }

        return $class1->getAssociationMapping($association)['mappedBy'];
    }
}
