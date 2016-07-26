<?php
namespace Onurb\Doctrine\ORMMetadataGrapher\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class HideAttributesProperties
 *
 * @Annotation
 * @Target("CLASS")
 */
final class HideAttributesProperties
{
    /**
     * @var boolean
     */
    public $value = true;
}
