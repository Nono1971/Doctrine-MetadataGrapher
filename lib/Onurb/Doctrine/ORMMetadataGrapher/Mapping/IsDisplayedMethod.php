<?php
namespace Onurb\Doctrine\ORMMetadataGrapher\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class IsDisplayedMethod
 *
 * @Annotation
 * @Target("METHOD")
 */
final class IsDisplayedMethod
{
    /**
     * @var boolean
     */
    public $value = true;
}
