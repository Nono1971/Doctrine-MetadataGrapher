<?php
/**
 * Created by PhpStorm.
 * User: bheron
 * Date: 25/07/2016
 * Time: 01:06
 */

namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Persistence\Mapping\ClassMetadata;

class ColorManager implements ColorManagerInterface
{
    /**
     * @var StringGeneratorInterface
     */
    private $stringGenerator;

    /**
     * @var ClassStoreInterface
     */
    private $classStore;

    /**
     * @var array
     */
    private $str = array();

    /**
     * @param StringGeneratorInterface $stringGenerator
     * @param ClassStoreInterface $classStore
     */
    public function __construct(StringGeneratorInterface $stringGenerator, ClassStoreInterface $classStore)
    {
        $this->stringGenerator = $stringGenerator;
        $this->classStore = $classStore;
    }

    /**
     * @param ClassMetadata[] $metadata
     * @param array $colors color strings
     * @return string
     */
    public function getColorStrings($metadata, $colors)
    {
        $this->classStore->storeColors($colors);

        foreach ($metadata as $class) {
            $color = $this->classStore->getClassColor($class->getName());
            if (null !== $color) {
                $this->str[] = self::makeColorString($this->stringGenerator->getClassString($class), $color);
            }
        }

        return $this->str;
    }

    /**
     * @param string $classString
     * @param string $color
     * @return string
     */
    private static function makeColorString($classString, $color)
    {
        return str_replace(']', '{bg:' . $color . '}]', $classString);
    }
}
