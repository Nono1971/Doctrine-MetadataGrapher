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

namespace Onurb\Doctrine\ORMMetadataGrapher;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\ClassStore;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

/**
 * Utility to generate yUML compatible strings from metadata graphs
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta   <ocramius@gmail.com>
 * @author  Bruno Heron     <<herobrun@gmail.com>
 */
class YUMLMetadataGrapher implements YUMLMetadataGrapherInterface
{
    /**
     * @var ClassStore
     */
    protected $classStore;

    /**
     * @var StringGenerator
     */
    protected $stringGenerator;

    /**
     * Generate a yUML compatible `dsl_text` to describe a given array
     * of entities
     *
     * @param  $metadata ClassMetadata[]
     *
     * @return string
     */
    public function generateFromMetadata(array $metadata)
    {
        $this->classStore = new ClassStore($metadata);
        $this->stringGenerator = new StringGenerator($this->classStore);

//        $this->storeClasses($metadata);
        $str                       = array();

        foreach ($metadata as $class) {
            if ($parent = $this->classStore->getParent($class)) {
                $str[] = $this->stringGenerator->getClassString($parent) . '^'
                    . $this->stringGenerator->getClassString($class);
            }

            $associations = $class->getAssociationNames();

            if (empty($associations) && !isset($this->stringGenerator->getVisitedAssociations()[$class->getName()])) {
                $str[] = $this->stringGenerator->getClassString($class);

                continue;
            }

            foreach ($associations as $associationName) {
                if ($parent && in_array($associationName, $parent->getAssociationNames())) {
                    continue;
                }

                if ($this->stringGenerator->visitAssociation($class->getName(), $associationName)) {
                    $str[] = $this->stringGenerator->getAssociationString($class, $associationName);
                }
            }
        }
        return implode(',', $str);
    }
}
