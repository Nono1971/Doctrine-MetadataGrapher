<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\VisitedAssociationLogger;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\VisitedAssociationLoggerInterface;

class StringGenerator implements StringGeneratorInterface
{

    /**
     * @var array
     */
    protected $classStrings;


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
        $className    = $class->getName();
        if (!isset($this->classStrings[$className])) {
            $this->associationLogger->visitAssociation($className);

            $classText    = '[' . str_replace('\\', '.', $className);
            $fields       = array();
            $parent       = $this->classStore->getParent($class);
            $parentFields = $parent ? $parent->getFieldNames() : array();

            foreach ($class->getFieldNames() as $fieldName) {
                if (in_array($fieldName, $parentFields)) {
                    continue;
                }

                if ($class->isIdentifier($fieldName)) {
                    $fields[] = '+' . $fieldName;
                } else {
                    $fields[] = $fieldName;
                }
            }

            if (!empty($fields)) {
                $classText .= '|' . implode(';', $fields);
            }

            $classText .= ']';

            $this->classStrings[$className] = $classText;
        }

        return $this->classStrings[$className];
    }

    /**
     * @param ClassMetadata $class1
     * @param string $association
     * @return string
     */
    public function getAssociationString(ClassMetadata $class1, $association)
    {
        $targetClassName = $class1->getAssociationTargetClass($association);
        $class2          = $this->classStore->getClassByName($targetClassName);
        $isInverse       = $class1->isAssociationInverseSide($association);
        $class1Count     = $class1->isCollectionValuedAssociation($association) ? 2 : 1;

        if (null === $class2) {
            return $this->makeSingleSidedLinkString($class1, $isInverse, $association, $class1Count, $targetClassName);
        }

        $class1SideName = $association;
        $class2SideName = $this->getClassReverseAssociationName($class1, $association);
        $class2Count    = 0;
        $bidirectional  = false;

        if (null !== $class2SideName) {
            if ($isInverse) {
                $class2Count    = $class2->isCollectionValuedAssociation($class2SideName) ? 2 : 1;
                $bidirectional  = true;
            } elseif ($class2->isAssociationInverseSide($class2SideName)) {
                $class2Count    = $class2->isCollectionValuedAssociation($class2SideName) ? 2 : 1;
                $bidirectional  = true;
            }
        }

        $this->associationLogger->visitAssociation($targetClassName, $class2SideName);

        return $this->makeDoubleSidedLinkString(
            $class1,
            $class2,
            $bidirectional,
            $isInverse,
            $class2SideName,
            $class2Count,
            $class1SideName,
            $class1Count
        );
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

    /**
     * @param ClassMetadata $class1
     * @param boolean $isInverse
     * @param string $association
     * @param int $class1Count
     * @param string $targetClassName
     * @return string
     */
    private function makeSingleSidedLinkString(
        ClassMetadata $class1,
        $isInverse,
        $association,
        $class1Count,
        $targetClassName
    ) {
        return $this->getClassString($class1) . ($isInverse ? '<' : '<>') . '-' . $association . ' '
        . ($class1Count > 1 ? '*' : ($class1Count ? '1' : '')) . ($isInverse ? '<>' : '>')
        . '[' . str_replace('\\', '.', $targetClassName) . ']';
    }

    /**
     * @param ClassMetadata $class1
     * @param ClassMetadata $class2
     * @param boolean $bidirectional
     * @param boolean $isInverse
     * @param string $class2SideName
     * @param integer $class2Count
     * @param string $class1SideName
     * @param integer $class1Count
     *
     * @return string
     */
    private function makeDoubleSidedLinkString(
        ClassMetadata $class1,
        ClassMetadata $class2,
        $bidirectional,
        $isInverse,
        $class2SideName,
        $class2Count,
        $class1SideName,
        $class1Count
    ) {
        return $this->getClassString($class1) . ($bidirectional ? ($isInverse ? '<' : '<>') : '')
        . ($class2SideName ? $class2SideName . ' ' : '') . ($class2Count > 1 ? '*' : ($class2Count ? '1' : ''))
        . '-' . $class1SideName . ' ' . ($class1Count > 1 ? '*' : ($class1Count ? '1' : ''))
        . (($bidirectional && $isInverse) ? '<>' : '>') . $this->getClassString($class2);
    }
}
