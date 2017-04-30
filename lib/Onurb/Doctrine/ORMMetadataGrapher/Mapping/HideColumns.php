<?php
namespace Onurb\Doctrine\ORMMetadataGrapher\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class ShowAttributesProperties
 *
 * @Annotation
 * @Target("CLASS")
 */
final class HideColumns
{
    /**
     * @var boolean
     */
    public $value = true;
}
