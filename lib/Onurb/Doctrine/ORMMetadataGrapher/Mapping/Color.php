<?php
namespace Onurb\Doctrine\ORMMetadataGrapher\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Color
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Color
{
    /**
     * @var string
     */
    public $value;
}
