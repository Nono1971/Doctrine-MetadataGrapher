<?php
namespace Onurb\Doctrine\ORMMetadataGrapher\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class ShowAttributesProperties
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class HiddenColumn
{
    /**
     * @var boolean
     */
    public $value = true;
}
