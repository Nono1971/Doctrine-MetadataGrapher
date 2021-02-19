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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use ReflectionClass;

class AnnotationParser implements AnnotationParserInterface
{
    /**
     * AnnotationReader
     */
    private $annotationReader;

    private $result;

    public function __construct()
    {
        $this->registerAnnotations();

        $this->annotationReader = new AnnotationReader();

        $this->result = array(
            'colors' => array(),
            'notes' => array()
        );
    }


    /**
     * @param ClassMetadata[] $metadata
     * @return array
     */
    public function getAnnotations($metadata)
    {
        foreach ($metadata as $class) {
            $this->setClassAnnotations($class->getName());
        }

        return $this->result;
    }

    /**
     * @param string $className
     * @return null|string
     */
    public function getClassDisplay($className)
    {
        if (class_exists($className)) {
            return $this->getClassDisplayAnnotations($className);
        }

        return null;
    }

    /**
     * @param string $className
     * @return array
     */
    public function getClassMethodsAnnotations($className)
    {
        $methods = array();

        if (class_exists($className)) {
            return $this->getMethods($className);
        }

        return $methods;
    }

    /**
     * @param string $className
     * @return array
     */
    public function getHiddenAttributes($className)
    {
        $attributes = array();

        if (class_exists($className)) {
            return $this->getAttributes($className);
        }

        return $attributes;
    }

    /**
     * @param string $className
     * @return array
     */
    private function getAttributes($className)
    {
        $attributes = array();
        $entityReflectionClass = new ReflectionClass($className);

        foreach ($entityReflectionClass->getProperties() as $attr) {
            $annotation = $this->annotationReader->getPropertyAnnotation(
                $attr,
                "Onurb\\Doctrine\\ORMMetadataGrapher\\Mapping\\HiddenColumn"
            );

            if (null !== $annotation) {
                $attributes[] = $attr->getName();
            }
        }

        return $attributes;
    }

    /**
     * @param string $className
     * @return bool
     */
    public function getClassHidesAttributes($className)
    {
        $hide = null;

        if (class_exists($className)) {
            $hide = $this->annotationReader->getClassAnnotation(
                new ReflectionClass($className),
                "Onurb\\Doctrine\\ORMMetadataGrapher\\Mapping\\HideColumns"
            );
        }

        return $hide === null ? false : true;
    }

    /**
     * @param string $className
     */
    private function setClassAnnotations($className)
    {
        if (class_exists($className)) {
            $this->setClassColor($className);
            $this->setClassNote($className);
        }
    }

    /**
     * @param string $className
     */
    private function setClassColor($className)
    {
        $color = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($className),
            "Onurb\\Doctrine\\ORMMetadataGrapher\\Mapping\\Color"
        );

        if (null !== $color) {
            $this->result['colors'][$className] = $color->value;
        }
    }

    /**
     * @param string $className
     */
    private function setClassNote($className)
    {
        $note = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($className),
            "Onurb\\Doctrine\\ORMMetadataGrapher\\Mapping\\Note"
        );

        if (null !== $note) {
            $this->result['notes'][$className] = array(
                'value' => $note->value,
                'color' => $note->color
            );
        }
    }

    /**
     * @param string $className
     * @return array
     */
    private function getMethods($className)
    {
        $methods = array();
        $entityReflectionClass = new ReflectionClass($className);

        foreach ($entityReflectionClass->getMethods() as $method) {
            $methodAnnotation = $this->annotationReader->getMethodAnnotation(
                $method,
                "Onurb\\Doctrine\\ORMMetadataGrapher\\Mapping\\IsDisplayedMethod"
            );

            if (null !== $methodAnnotation) {
                $methods[] = $method->getName();
            }
        }

        return $methods;
    }

    /**
     * @param $className
     * @return null|string
     * @throws \Exception
     */
    private function getClassDisplayAnnotations($className)
    {
        $hide = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($className),
            "Onurb\\Doctrine\\ORMMetadataGrapher\\Mapping\\HideAttributesProperties"
        );

        $show = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($className),
            "Onurb\\Doctrine\\ORMMetadataGrapher\\Mapping\\ShowAttributesProperties"
        );

        $this->checkDisplayAnnotationValidity($hide, $show);

        return !$hide && !$show ? null : ($hide ? 'hide' : 'show');
    }

    /**
     * @param Object|null $hide
     * @param Object|null $show
     * @throws \Exception
     */
    private function checkDisplayAnnotationValidity($hide, $show)
    {
        if ($hide && $show) {
            throw new \Exception('Annotations HideAttributesProperties and ShowAttributesProperties '
            . 'can\'t be used on the same class at the same time');
        }
    }

    private function registerAnnotations()
    {
        $baseDir = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Mapping" . DIRECTORY_SEPARATOR;

        AnnotationRegistry::registerFile(
            $baseDir . "Color.php"
        );
        AnnotationRegistry::registerFile(
            $baseDir . "Note.php"
        );

        AnnotationRegistry::registerFile(
            $baseDir . "IsDisplayedMethod.php"
        );

        AnnotationRegistry::registerFile(
            $baseDir . "ShowAttributesProperties.php"
        );

        AnnotationRegistry::registerFile(
            $baseDir . "HideAttributesProperties.php"
        );

        AnnotationRegistry::registerFile(
            $baseDir . "HiddenColumn.php"
        );

        AnnotationRegistry::registerFile(
            $baseDir . "HideColumns.php"
        );
    }
}
