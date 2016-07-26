<?php

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

class StringGeneratorHelper implements StringGeneratorHelperInterface
{
    /**
     * @param string $className
     * @param array $fields
     * @param array $methods
     * @return string
     */
    public function getClassText($className, $fields, $methods = array())
    {
        $classText = '[' . str_replace('\\', '.', $className);
        $classText .= !empty($fields) ? '|' . implode(';', $fields) : '';
        $classText .= !empty($methods) ? '|' . implode('();', $methods) . '()' : '';
        $classText .= ']';

        return $classText;
    }


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
    ) {
        return $class1String . $this->getLeftArrow($isInverse, true) . '-' . $association . ' '
        . $this->getCountSide($class1Count) . $this->getRightArrow($isInverse, true)
        . '[' . str_replace('\\', '.', $targetClassName) . ']';
    }

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
    ) {
        return $class1String . $this->getLeftArrow($isInverse, $bidirectional)
        . ($class2SideName ? $class2SideName . ' ' . $this->getCountSide($class2Count) : '')
        . '-' . $class1SideName . ' ' . $this->getCountSide($class1Count)
        . $this->getRightArrow($isInverse, $bidirectional) . $class2String;
    }

    /**
     * @param bool $isInverse
     * @param bool $bidirectional
     * @return string
     */
    private function getLeftArrow($isInverse, $bidirectional)
    {
        return $bidirectional ? ($isInverse ? '<' : '<>') : '';
    }

    /**
     * @param bool $isInverse
     * @param bool $bidirectional
     * @return string
     */
    private function getRightArrow($isInverse, $bidirectional)
    {
        return ($bidirectional && $isInverse) ? '<>' : '>';
    }

    /**
     * @param int $classCount
     * @return string
     * @throws \Exception
     */
    private function getCountSide($classCount)
    {
        if ($classCount > 1) {
            return '*';
        } elseif ($classCount === 1) {
            return '1';
        }
        throw new \Exception('Impossible class count value ' . $classCount);
    }
}
