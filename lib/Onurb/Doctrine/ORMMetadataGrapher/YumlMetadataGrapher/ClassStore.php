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

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

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
     * indexed array of ClassMetadata
     *
     * @var ClassMetadata[]
     */
    protected $metadata = array();

    protected $visitedAssociations;

    /**
     * store metadata in an associated array to get classes
     * faster into $this->getClassByName()
     *
     * @param ClassMetadata[] $metadata
     */
    public function __construct($metadata)
    {
        foreach ($metadata as $class) {
            $this->metadata[$class->getName()] = $class;
        }
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
     * Retrieve a class metadata instance by name from the given array
     *
     * @param   string      $className
     *
     * @return  ClassMetadata|null
     */
    public function getClassByName($className)
    {
        return isset($this->metadata[$className]) && !empty($this->metadata[$className]) ?
            $this->metadata[$className] : null;
    }
}
