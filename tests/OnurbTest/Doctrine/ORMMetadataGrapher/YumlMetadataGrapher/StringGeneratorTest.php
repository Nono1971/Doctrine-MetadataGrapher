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
namespace OnurbTest\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\ClassStore;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class StringGeneratorTest extends TestCase
{
    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testInstance()
    {
        $classStore = $this->createMock('Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\ClassStoreInterface');
        $stringGenerator = new StringGenerator($classStore);

        $this->assertInstanceOf(
            'Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\StringGeneratorInterface',
            $stringGenerator
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetAssociationLogger()
    {
        $classStore = $this->createMock('Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\ClassStoreInterface');
        $stringGenerator = new StringGenerator($classStore);

        $this->assertInstanceOf(
            'Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher'
            . '\\StringGenerator\\VisitedAssociationLoggerInterface',
            $stringGenerator->getAssociationLogger()
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetClassString()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('a', 'b', 'c')));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')
            ->will($this->returnValue(null));
        
        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[Simple.Entity|+a;b;c]', $stringGenerator->getClassString($class1));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetClassStringWithParentFieldMatching()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Extended\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')
            ->will($this->returnValue(array('a', 'b', 'c', 'd', 'e', 'f', 'g')));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

        $classParent = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $classParent->expects($this->any())->method('getName')->will($this->returnValue('Parent\\Entity'));
        $classParent->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('d')));
        $classParent->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $classParent->expects($this->any())->method('isIdentifier')->will($this->returnValue(false));

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')
            ->with($this->logicalOr($class1, $classParent))
            ->will($this->returnCallback(
                function ($class) use ($class1, $classParent) {
                    if ($class == $class1) {
                        return $classParent;
                    }
                    return null;
                }
            ));


        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[Extended.Entity|+a;b;c;e;f;g]', $stringGenerator->getClassString($class1));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testClassStringWithOlderParentFieldsMatching()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YUMLMetadataGrapher\\ClassStoreTest\\E'
            ));
        $class1->expects($this->any())->method('getFieldNames')
            ->will($this->returnValue(array('a', 'b', 'c', 'd', 'e', 'f', 'g')));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

        $classParent = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $classParent->expects($this->any())->method('getName')->will($this->returnValue(
            'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YUMLMetadataGrapher\\ClassStoreTest\\D'
        ));
        $classParent->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('d')));
        $classParent->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $classParent->expects($this->any())->method('isIdentifier')->will($this->returnValue(false));

        $classOlderParent = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $classOlderParent->expects($this->any())->method('getName')->will($this->returnValue(
            'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YUMLMetadataGrapher\\ClassStoreTest\\A'
        ));
        $classOlderParent->expects($this->any())->method('getFieldNames')
            ->will($this->returnValue(array('b', 'c')));
        $classOlderParent->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $classOlderParent->expects($this->any())->method('isIdentifier')->will($this->returnValue(false));

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')
            ->with($this->logicalOr($class1, $classParent, $classOlderParent))
            ->will($this->returnCallback(
                function ($class) use ($class1, $classParent, $classOlderParent) {
                    if ($class == $class1) {
                        return $classParent;
                    } elseif ($class == $classParent) {
                        return $classOlderParent;
                    }
                    return null;
                }
            ));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YUMLMetadataGrapher.ClassStoreTest.E|+a;e;f;g]',
            $stringGenerator->getClassString($class1)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetAssociationString()
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
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>null
        )));

        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classStore = new classStore(array($class1, $class2));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[A]-b 1>[B]', $stringGenerator->getAssociationString($class1, 'b'));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetAssociationStringWithUnknownTargetClass()
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
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b', 'c')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>null
        )));

        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classStore = new classStore(array($class1, $class2));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[A]-b 1>[B]', $stringGenerator->getAssociationString($class1, 'b'));
        $this->assertSame('[A]<>-c 1>[C]', $stringGenerator->getAssociationString($class1, 'c'));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetAssociationStringWithUnknownTargetClassInverseSide()
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
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => null,
            'inversedBy'    =>null
        )));

        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));


        $class2 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
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
            ))->getMock();

        $class2->expects($this->any())->method('getName')->will($this->returnValue('C'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('d')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>null
        )));

        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classStore = new classStore(array($class1, $class2));
        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[A]<-b 1<>[B]', $stringGenerator->getAssociationString($class1, 'b'));
        $this->assertSame('[C]<>-d *>[D]', $stringGenerator->getAssociationString($class2, 'd'));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetAssociationMappingWithBidirectionnalOneToOneRelation()
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
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>'a'
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
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
            ))->getMock();

        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classStore = new classStore(array($class1, $class2));
        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[A]<>a 1-b 1>[B]', $stringGenerator->getAssociationString($class1, 'b'));
        $this->assertSame('[B]<b 1-a 1<>[A]', $stringGenerator->getAssociationString($class2, 'a'));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetAssociationMappingWithBidirectionnalManyToOneRelation()
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
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>'a'
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
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
            ))->getMock();

        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classStore = new classStore(array($class1, $class2));
        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[A]<>a 1-b *>[B]', $stringGenerator->getAssociationString($class1, 'b'));
        $this->assertSame('[B]<b *-a 1<>[A]', $stringGenerator->getAssociationString($class2, 'a'));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetAssociationMappingWithBidirectionnalManyToManyRelation()
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
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    =>'a'
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
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
            ))->getMock();

        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnCallback(
            function ($target) {
                return strtoupper($target);
            }
        ));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classStore = new classStore(array($class1, $class2));
        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame('[A]<>a *-b *>[B]', $stringGenerator->getAssociationString($class1, 'b'));
        $this->assertSame('[B]<b *-a *<>[A]', $stringGenerator->getAssociationString($class2, 'a'));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetClassStringWithFullAttributes()
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
                'getFieldMapping',
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getFieldNames')
            ->will($this->returnValue(array('id','name','description')));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'id';
                }
            )
        );

        $class1->expects($this->any())->method('getFieldMapping')->will(
            $this->returnCallback(
                function ($field) {
                    if ($field === 'id') {
                        return array(
                            'type' => 'integer'
                        );
                    } elseif ($field === 'name') {
                        return array(
                            'type' => 'string',
                            'length' => 45,
                            'unique' => true

                        );
                    } elseif ($field === 'description') {
                        return array(
                            'type' => 'string',
                            'length' => 255
                        );
                    }
                }
            )
        );

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')
            ->will($this->returnValue(null));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame(
            '[Simple.Entity|+id : integer;* name : string (45);description : string (255)]',
            $stringGenerator->setShowFieldsDescription(true)->getClassString($class1)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testGetClassStringWithDecimalFullAttributes()
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
                'getFieldMapping',
            ))->getMock();

        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getFieldNames')
            ->will($this->returnValue(array('id','price','otherPrice')));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'id';
                }
            )
        );

        $class1->expects($this->any())->method('getFieldMapping')->will(
            $this->returnCallback(
                function ($field) {
                    if ($field === 'id') {
                        return array(
                            'type' => 'integer'
                        );
                    } elseif ($field === 'price') {
                        return array(
                            'type' => 'decimal',

                        );
                    } elseif ($field === 'otherPrice') {
                        return array(
                            'type' => 'decimal',
                            'precision' => 10,
                            'scale' => 3
                        );
                    }
                }
            )
        );

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')
            ->will($this->returnValue(null));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame(
            '[Simple.Entity|+id : integer;price : decimal;otherPrice : decimal (10 - 3)]',
            $stringGenerator->setShowFieldsDescription(true)->getClassString($class1)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testClassStringWithShowFieldsBloquedByAnnotation()
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
                'getFieldMapping',
            ))->getMock();

        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\G'
            ));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('a', 'b', 'c')));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

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
                            'length' => 45,
                            'unique' => true

                        );
                    } elseif ($field === 'c') {
                        return array(
                            'type' => 'string',
                            'length' => 255
                        );
                    }
                    return false;
                }
            )
        );

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')->will($this->returnValue(null));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.G|+a;b;c]',
            $stringGenerator->setShowFieldsDescription(true)->getClassString($class1)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testClassStringWithShowFieldsForcedByAnnotation()
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
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\H'
            ));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('a', 'b', 'c')));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

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
                            'length' => 45,
                            'unique' => true

                        );
                    } elseif ($field === 'c') {
                        return array(
                            'type' => 'string',
                            'length' => 255
                        );
                    }
                    return false;
                }
            )
        );

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')->will($this->returnValue(null));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.H|'
                . '+a : integer;* b : string (45);c : string (255)]',
            $stringGenerator->getClassString($class1),
            false
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testClassStringWithFieldHiddenByAnnotation()
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
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\I'
            ));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('a', 'b', 'secret')));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

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
                            'length' => 45

                        );
                    } elseif ($field === 'secret') {
                        return array(
                            'type' => 'string',
                            'length' => 255
                        );
                    }

                    return false;
                }
            )
        );

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')->will($this->returnValue(null));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.I|'
                . '+a;b]',
            $stringGenerator->getClassString($class1),
            false
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator
     */
    public function testClassHideFieldsAnnotation()
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
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\J'
            ));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('a', 'b', 'c')));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

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
                            'length' => 45

                        );
                    } elseif ($field === 'c') {
                        return array(
                            'type' => 'string',
                            'length' => 255
                        );
                    }

                    return false;
                }
            )
        );

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();

        $classStore->expects($this->any())->method('getParent')->will($this->returnValue(null));

        $stringGenerator = new StringGenerator($classStore);

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.J]',
            $stringGenerator->getClassString($class1),
            false
        );
    }
}
