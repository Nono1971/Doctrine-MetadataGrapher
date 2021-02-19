<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */


namespace Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Persistence\Mapping\ClassMetadata;

/**
 * Utility to generate yUML compatible strings from metadata graphs
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta   <ocramius@gmail.com>
 * @author  Bruno Heron     <<herobrun@gmail.com>
 */
class ClassStore implements ClassStoreInterface
{

    /**
     * Indexed array of ClassMetadata and options
     *
     * @var array
     */
    private $indexedClasses = array();

    /**
     * store metadata in an associated array to get classes
     * faster into $this->getClassByName()
     *
     * @param ClassMetadata[] $metadata
     */
    public function __construct($metadata)
    {
        $this->indexClasses($metadata);
    }

    /**
     * Retrieve a class metadata instance by name from the given array
     *
     * @param   string      $className
     *
     * @return  ClassMetadata|null
     */
    public function getClassByName($className)
    {
        $classMap = $this->getClassMap($this->splitClassName($className)) . "[\"__class\"]";
        $return = null;

        eval(
            "if (isset(\$this->indexedClasses$classMap)) {"
            . " \$return = \$this->indexedClasses$classMap;"
            . "}"
        );

        return $return;
    }

    /**
     * Retrieve a class metadata's parent class metadata
     *
     * @param ClassMetadata   $class
     *
     * @return ClassMetadata|null
     */
    public function getParent(ClassMetadata $class)
    {
        $className = $class->getName();
        if (!class_exists($className) || (!$parent = get_parent_class($className))) {
            return null;
        }

        return $this->getClassByName($parent);
    }

    /**
     * @return array
     */
    public function getIndexedClasses()
    {
        return $this->indexedClasses;
    }

    /**
     * @param string $className
     * @return string
     */
    public function getClassColor($className)
    {
        $splitName = $this->splitClassName($className);
        $color = null;

        do {
            $colorMap = $this->getClassMap($splitName) . "[\"__color\"]";

            eval(
                "if (isset(\$this->indexedClasses$colorMap)) {"
                . "\$color = \$this->indexedClasses$colorMap;"
                . "}"
            );

            unset($splitName[count($splitName) - 1]);
        } while (null === $color && !empty($splitName));

        return $color;
    }

    /**
     * @param array $colors
     */
    public function storeColors($colors)
    {
        foreach ($colors as $namespace => $color) {
            $this->storeColor($namespace, $color);
        }
    }

    /**
     * @param array $classSplit
     * @return string
     */
    private function getClassMap($classSplit)
    {
        return "[\"" . implode("\"][\"", $classSplit) . "\"]";
    }

    /**
     * @param ClassMetadata[] $metadata
     */
    private function indexClasses($metadata)
    {
        foreach ($metadata as $class) {
            $this->indexClass($class);
        }
    }

    /**
     * @param ClassMetadata $class
     */
    private function indexClass(ClassMetadata $class)
    {
        $this->checkIndexAlreadyExists($class->getName());

        $classMap = $this->getClassMap($this->splitClassName($class->getName())) . "[\"__class\"]";

        eval(
            "\$this->indexedClasses$classMap = \$class;"
        );
    }

    /**
     * @param string $className
     * @return array
     */
    private function splitClassName($className)
    {
        return explode('\\', $className);
    }

    /**
     * @param string $className
     */
    private function checkIndexAlreadyExists($className)
    {
        $namespaces = $this->splitClassName($className);

        $tmpArrayMap = "";

        foreach ($namespaces as $namespace) {
            $tmpArrayMap .= "[\"$namespace\"]";
            eval("if (!isset(\$this->indexedClasses$tmpArrayMap)) "
            . "{\$this->indexedClasses$tmpArrayMap = array(\"__class\" => null, \"__color\" => null);}");
        }
    }

    /**
     * @param string $namespace
     * @param string $color
     */
    private function storeColor($namespace, $color)
    {
        $this->checkIndexAlreadyExists($namespace);

        $colorMap = $this->getClassMap($this->splitClassName($namespace)) . "[\"__color\"]";

        eval(
            "\$this->indexedClasses$colorMap = \"$color\";"
        );
    }
}
