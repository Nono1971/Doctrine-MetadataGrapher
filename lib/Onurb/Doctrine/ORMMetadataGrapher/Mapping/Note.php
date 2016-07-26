<?php
namespace Onurb\Doctrine\ORMMetadataGrapher\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Note
 * @package Onurb\Doctrine\ORMMetadataGrapher\Mapping
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Note extends Annotation
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var string|null
     */
    public $color = null;
}
