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
namespace OnurbTest\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;

use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\FieldGeneratorHelper;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\FieldGeneratorHelperInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class FieldGeneratorHelperTest extends TestCase
{

    /**
     * @var FieldGeneratorHelperInterface
     */
    protected $fieldHelper;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fieldHelper = new FieldGeneratorHelper();
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\StringGeneratorHelper
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            'Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\FieldGeneratorHelperInterface',
            $this->fieldHelper
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\FieldGeneratorHelper
     */
    public function testUniqueField()
    {
        $class1 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
            ->setMethods(array(
                'getName',
                'getIdentifier',
                'getReflectionClass',
                'isIdentifier',
                'hasField',
                'hasAssociation',
                'isSingleValuedAssociation',
                'isCollectionValuedAssociation',
                'getFieldNames',
                'getIdentifierFieldNames',
                'getAssociationNames',
                'getTypeOfField',
                'getAssociationTargetClass',
                'isAssociationInverseSide',
                'getAssociationMappedByTargetField',
                'getIdentifierValues',
                'getAssociationMapping',
                'getFieldMapping',
            ))->getMock();

        $class1->expects($this->any())->method('getFieldMapping')->will(
            $this->returnCallback(
                function ($field) {
                    if ($field === 'a') {
                        return array(
                            'type' => 'integer'
                        );
                    } elseif ($field === 'b') {
                        return array(
                            'type' => 'string',
                            'unique' => true
                        );
                    }

                    return false;
                }
            )
        );

        $this->assertSame(
            'a : integer',
            $this->fieldHelper->getFullField($class1, 'a')
        );

        $this->assertSame(
            '* b : string',
            $this->fieldHelper->getFullField($class1, 'b')
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\FieldGeneratorHelper
     */
    public function testStringField()
    {
        $class1 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
            ->setMethods(array(
                'getName',
                'getIdentifier',
                'getReflectionClass',
                'isIdentifier',
                'hasField',
                'hasAssociation',
                'isSingleValuedAssociation',
                'isCollectionValuedAssociation',
                'getFieldNames',
                'getIdentifierFieldNames',
                'getAssociationNames',
                'getTypeOfField',
                'getAssociationTargetClass',
                'isAssociationInverseSide',
                'getAssociationMappedByTargetField',
                'getIdentifierValues',
                'getAssociationMapping',
                'getFieldMapping',
            ))->getMock();

        $class1->expects($this->any())->method('getFieldMapping')->will(
            $this->returnCallback(
                function ($field) {
                    if ($field === 'a') {
                        return array(
                            'type' => 'integer'
                        );
                    } elseif ($field === 'b') {
                        return array(
                            'type' => 'string',
                        );
                    } elseif ($field ==='c') {
                        return array(
                            'type' => 'string',
                            'length' => '45'
                        );
                    }
                }
            )
        );

        $this->assertSame(
            'b : string',
            $this->fieldHelper->getFullField($class1, 'b')
        );

        $this->assertSame(
            'c : string (45)',
            $this->fieldHelper->getFullField($class1, 'c')
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\FieldGeneratorHelper
     */
    public function testDecimalField()
    {
        $class1 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
            ->setMethods(array(
                'getName',
                'getIdentifier',
                'getReflectionClass',
                'isIdentifier',
                'hasField',
                'hasAssociation',
                'isSingleValuedAssociation',
                'isCollectionValuedAssociation',
                'getFieldNames',
                'getIdentifierFieldNames',
                'getAssociationNames',
                'getTypeOfField',
                'getAssociationTargetClass',
                'isAssociationInverseSide',
                'getAssociationMappedByTargetField',
                'getIdentifierValues',
                'getAssociationMapping',
                'getFieldMapping',
            ))->getMock();

        $class1->expects($this->any())->method('getFieldMapping')->will(
            $this->returnCallback(
                function ($field) {
                    if ($field === 'a') {
                        return array(
                            'type' => 'integer'
                        );
                    } elseif ($field === 'b') {
                        return array(
                            'type' => 'decimal',
                        );
                    } elseif ($field ==='c') {
                        return array(
                            'type' => 'decimal',
                            'precision' => 10,
                            'scale' => 3,
                        );
                    }
                }
            )
        );

        $this->assertSame(
            'b : decimal',
            $this->fieldHelper->getFullField($class1, 'b')
        );

        $this->assertSame(
            'c : decimal (10 - 3)',
            $this->fieldHelper->getFullField($class1, 'c')
        );
    }
}
