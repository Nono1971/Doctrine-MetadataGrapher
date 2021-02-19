<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class FieldGeneratorHelper implements FieldGeneratorHelperInterface
{
    /**
     * @param ClassMetadata $class
     * @param $fieldName
     * @return string
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function getFullField(ClassMetadata $class, $fieldName)
    {
        /**
         * @var ClassMetadataInfo $class
         */
        $fullField = $class->getFieldMapping($fieldName);

        return $this->getUnique($fullField) . $fieldName . ' : ' . $fullField['type']
        . $this->getFieldLength($fullField)
        . $this->getNumericFieldPrecision($fullField);
    }

    /**
     * @param array $fullField
     * @return string
     */
    private function getFieldLength($fullField)
    {
        return $fullField['type'] === 'string' && isset($fullField['length']) ? ' (' . $fullField['length'] . ')' : '';
    }

    /**
     * @param array $fullField
     * @return string
     */
    private function getNumericFieldPrecision($fullField)
    {
        if ($fullField['type'] === 'decimal') {
            return $this->getDecimalPrecision($fullField);
        }

        return '';
    }

    /**
     * @param array $fullField
     * @return string
     */
    private function getDecimalPrecision($fullField)
    {
        if (isset($fullField['precision']) && isset($fullField['scale'])) {
            return ' (' . $fullField['precision'] . ' - ' . $fullField['scale'] . ')';
        }

        return '';
    }

    /**
     * @param array $fullField
     * @return string
     */
    private function getUnique($fullField)
    {
        return isset($fullField['unique']) && $fullField['unique'] ? '* ' : '';
    }
}
