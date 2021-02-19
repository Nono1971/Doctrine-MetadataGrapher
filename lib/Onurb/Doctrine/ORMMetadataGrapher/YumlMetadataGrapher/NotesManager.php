<?php
/**
 * Created by PhpStorm.
 * User: bheron
 * Date: 25/07/2016
 * Time: 01:06
 */
namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Persistence\Mapping\ClassMetadata;

class NotesManager implements NotesManagerInterface
{
    /**
     * @var ClassStoreInterface
     */
    private $classStore;

    /**
     * @var StringGeneratorInterface
     */
    private $stringGenerator;

    /**
     * @var array
     */
    private $str = array();

    public function __construct(
        ClassStoreInterface $classStore,
        StringGeneratorInterface $stringGenerator
    ) {
        $this->classStore       = $classStore;
        $this->stringGenerator  = $stringGenerator;
    }

    /**
     * @param array $notes
     * @return array
     */
    public function getNotesStrings(array $notes)
    {
        foreach ($notes as $className => $note) {
            $this->setNoteString($className, $note);
        }

        return $this->str;
    }

    private function setNoteString($className, $note)
    {
        if ($class = $this->classStore->getClassByName($className)) {
            $this->str[] = $this->makeNoteLink($class, $note);
        }
    }

    /**
     * @param ClassMetadata $class
     * @param array $note
     * @return string
     * @throws \Exception
     */
    private function makeNoteLink(ClassMetadata $class, $note)
    {
        if (!$this->isValid($note)) {
            throw new \Exception('Invalid note. It must be an array, with  \'value\' key'
                .'and with optional \'color\' keys. And of course, description must not be empty ;) ^^');
        }

        return $this->stringGenerator->getClassString($class) . "-" . $this->makeNoteString($note);
    }

    /**
     * @param array $note
     * @return bool
     */
    private function isValid($note)
    {
        return isset($note['value']) && $note['value'] != '';
    }

    /**
     * @param array $note
     * @return string
     */
    private function makeNoteString($note)
    {
        return '[note:' . $note['value'] . $this->getNoteColor($note) . ']';
    }

    /**
     * @param $note
     * @return string
     */
    private function getNoteColor($note)
    {
        return isset($note['color']) ? '{bg:' . $note['color'] . '}' : '{bg:yellow}';
    }
}
