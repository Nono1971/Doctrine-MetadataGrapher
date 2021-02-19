<?php
/**
 * Created by PhpStorm.
 * User: bheron
 * Date: 25/07/2016
 * Time: 01:06
 */
namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Persistence\Mapping\ClassMetadata;

interface ColorManagerInterface
{
    /**
     * @param StringGeneratorInterface $stringGenerator
     */
    public function __construct(StringGeneratorInterface $stringGenerator, ClassStoreInterface $classStore);

    /**
     * @param ClassMetadata[] $metadata
     * @param array $colors color strings
     */
    public function getColorStrings($metadata, $colors);
}
