<?php
/**
 * Created by PhpStorm.
 * User: bheron
 * Date: 25/07/2016
 * Time: 01:06
 */
namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Persistence\Mapping\ClassMetadata;

interface NotesManagerInterface
{
    public function __construct(ClassStoreInterface $classStore, StringGeneratorInterface $stringGenerator);

    /**
     * @param array $notes
     * @return array
     */
    public function getNotesStrings(array $notes);
}
