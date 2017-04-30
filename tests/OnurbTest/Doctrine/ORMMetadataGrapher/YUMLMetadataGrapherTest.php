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

namespace OnurbTest\Doctrine\ORMMetadataGrapher;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class YUMLMetadataGrapherTest extends TestCase
{
    /**
     * @var YUMLMetadataGrapher
     */
    protected $grapher;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->grapher = new YUMLMetadataGrapher();

        require_once('YumlMetadataGrapher/ClassStoreTest/A.php');
        require_once('YumlMetadataGrapher/ClassStoreTest/B.php');
        require_once('YumlMetadataGrapher/ClassStoreTest/C.php');
        require_once('YumlMetadataGrapher/ClassStoreTest/D.php');
        require_once('YumlMetadataGrapher/ClassStoreTest/E.php');

        require_once('YumlMetadataGrapher/AnnotationParserTest/A.php');
        require_once('YumlMetadataGrapher/AnnotationParserTest/B.php');
        require_once('YumlMetadataGrapher/AnnotationParserTest/C.php');
        require_once('YumlMetadataGrapher/AnnotationParserTest/D.php');
        require_once('YumlMetadataGrapher/AnnotationParserTest/E.php');
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawSimpleEntity()
    {
        $class = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $this->assertSame('[Simple.Entity]', $this->grapher->generateFromMetadata(array($class)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawSimpleEntityWithFields()
    {
        $class = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('a', 'b', 'c')));
        $class->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class->expects($this->any())->method('isIdentifier')->will(
            $this->returnCallback(
                function ($field) {
                    return $field === 'a';
                }
            )
        );

        $this->assertSame('[Simple.Entity|+a;b;c]', $this->grapher->generateFromMetadata(array($class)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawOneToOneUniDirectionalAssociation()
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

        $this->assertSame('[A]-b 1>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalAssociation()
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
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a 1-b 1>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawOneToOneBiDirectionalInverseAssociation()
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
            'isOwningSide' => false,
            'mappedBy'      => 'a',
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
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
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'a'
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $expected = "[A]<a 1-b 1<>[B]";
        $this->assertSame($expected, $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalAssociation()
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
            'inversedBy'    => 'a'
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
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a 1-b *>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawOneToManyBiDirectionalInverseAssociation()
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
            'inversedBy'    => 'a'
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
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'b',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a *-b 1>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalAssociation()
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
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]-b *>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawManyToManyUniDirectionalInverseAssociation()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
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
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(null));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A],[B]-a *>[A]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalAssociation()
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
            'inversedBy'    => 'a'
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
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'a',
            'inversedBy'    => null
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('b'));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>a *-b *>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawManyToManyBiDirectionalInverseAssociation()
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
            'isOwningSide' => false,
            'mappedBy'      => 'a',
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getAssociationMappedByTargetField')->will($this->returnValue('a'));
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
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'b'
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<a *-b *<>[B]', $this->grapher->generateFromMetadata(array($class1, $class2)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawManyToManyAssociationWithoutKnownInverseSide()
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
            'inversedBy'    => null
        )));
        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $this->assertSame('[A]<>-b *>[B]', $this->grapher->generateFromMetadata(array($class1)));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawInheritance()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A'
            ));

        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D'
            ));

        $this->assertSame(
            '[' . str_replace('\\', '.', $class1->getName()) . ']^[' . str_replace('\\', '.', $class2->getName()) . ']',
            $this->grapher->generateFromMetadata(array($class2, $class1))
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawInheritanceWithParentsTree()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\E'
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
            'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D'
        ));
        $classParent->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('d')));
        $classParent->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $classParent->expects($this->any())->method('isIdentifier')->will($this->returnValue(false));

        $classOlderParent = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $classOlderParent->expects($this->any())->method('getName')->will($this->returnValue(
            'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A'
        ));
        $classOlderParent->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('b','c')));
        $classOlderParent->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $classOlderParent->expects($this->any())->method('isIdentifier')->will($this->returnValue(false));

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.ClassStoreTest.D|d]'
            . '^[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.ClassStoreTest.E|+a;e;f;g]'
            . ',[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.ClassStoreTest.A|b;c]^'
            . '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.ClassStoreTest.D|d]',
            $this->grapher->generateFromMetadata(array($class1, $classParent, $classOlderParent))
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawInheritedFields()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');

        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A'
            ));

        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('inherited')));

        $class2->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D'
            ));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array('inherited', 'field2')));

        $this->assertSame(
            '['. str_replace('\\', '.', $class1->getName()) .'|inherited]^['
                . str_replace('\\', '.', $class2->getName()) . '|field2]',
            $this->grapher->generateFromMetadata(array($class2, $class1))
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawInheritedAssociations()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class3 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class4 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');

        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A'
            ));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('A'));

        $class1->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class1->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class2->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D'
            ));
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a', 'b')));
        $class2
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->will(
                $this->returnCallback(
                    function ($assoc) {
                        return strtoupper($assoc);
                    }
                )
            );
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class3->expects($this->any())->method('getName')->will($this->returnValue('A'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class4->expects($this->any())->method('getName')->will($this->returnValue('B'));
        $class4->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));
        $class4->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $childName = str_replace('\\', '.', $class2->getName());
        $parentName = str_replace('\\', '.', $class1->getName());

        $this->assertSame(
            '[' . $parentName . ']<>-a *>[A],'
            . '['. $parentName .']^[' . $childName . '],[' . $childName . ']<>-b *>[B]',
            $this->grapher->generateFromMetadata(array($class1, $class2))
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     * @dataProvider injectMultipleRelationsWithBothBiAndMonoDirectional
     *
     * @param ClassMetadata $class1
     * @param ClassMetadata $class2
     * @param ClassMetadata $class3
     * @param string $expected
     */
    public function testDrawMultipleClassRelatedBothBiAndMonoDirectional(
        ClassMetadata $class1,
        ClassMetadata $class2,
        ClassMetadata $class3,
        $expected
    ) {
        $this->assertSame(
            $expected,
            $this->grapher->generateFromMetadata(array($class1, $class2,$class3))
        );
    }

    /**
     * dataProvider to inject classes in every possible order into the test
     *     testDrawMultipleClassRelatedBothBiAndMonoDirectional
     *
     * @return array
     */
    public function injectMultipleRelationsWithBothBiAndMonoDirectional()
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
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('c')));
        $class1->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class1->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => null
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
        $class2->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('c')));
        $class2->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('C'));
        $class2->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => true,
            'mappedBy'      => null,
            'inversedBy'    => 'b'
        )));
        $class2->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $class2->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $class2->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $class3 = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
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
        $class3->expects($this->any())->method('getName')->will($this->returnValue('C'));
        $class3->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('b')));
        $class3->expects($this->any())->method('getAssociationTargetClass')->will($this->returnValue('B'));
        $class3->expects($this->any())->method('getAssociationMapping')->will($this->returnValue(array(
            'isOwningSide' => false,
            'mappedBy'      => 'c',
            'inversedBy'    => null
        )));
        $class3->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $class3->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $class3->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        return array(
            array($class1, $class2, $class3, '[A]-c 1>[C],[B]<>b *-c 1>[C]'),
            array($class1, $class3, $class2, '[A]-c 1>[C],[C]<c 1-b *<>[B]'),
            array($class2, $class1, $class3, '[B]<>b *-c 1>[C],[A]-c 1>[C]'),
            array($class2, $class3, $class1, '[B]<>b *-c 1>[C],[A]-c 1>[C]'),
            array($class3, $class1, $class2, '[C]<c 1-b *<>[B],[A]-c 1>[C]'),
            array($class3, $class2, $class1, '[C]<c 1-b *<>[B],[A]-c 1>[C]')
        );
    }

    /**
     * To mock getAssociationTargetClass method with args
     *
     * @param  string $a
     * @return string
     */
    public function getAssociationTargetClassMock($a)
    {
        return strtoupper($a);
    }

    /**
     * @return array
     */
    public function injectTwoClassesWithTwoDifferentRelationsOneToManyBidirectionnal()
    {
        $classAB = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
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
        $classAB->expects($this->any())->method('getName')->will($this->returnValue('AB'));
        $classAB->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('c','d')));
        $classAB
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->with($this->logicalOr($this->equalTo('c'), $this->equalTo('d')))
            ->will($this->returnCallback(array($this, 'getAssociationClassMock')));
        $classAB
            ->expects($this->any())
            ->method('getAssociationMapping')
            ->with($this->logicalOr(
                $this->equalTo('c'),
                $this->equalTo('d')
            ))
            ->will($this->returnCallback(array($this, 'getAssociationMappingMock')));
        $classAB->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(true));
        $classAB->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(true));
        $classAB->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        $classCD = $this->getMockBuilder('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')
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
        $classCD->expects($this->any())->method('getName')->will($this->returnValue('CD'));
        $classCD->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array('a','b')));
        $classCD
            ->expects($this->any())
            ->method('getAssociationTargetClass')
            ->with($this->logicalOr($this->equalTo('a'), $this->equalTo('b')))
            ->will($this->returnCallback(array($this, 'getAssociationClassMock')));
        $classCD
            ->expects($this->any())
            ->method('getAssociationMapping')
            ->with($this->logicalOr(
                $this->equalTo('a'),
                $this->equalTo('b')
            ))
            ->will($this->returnCallback(array($this, 'getAssociationMappingMock')));
        $classCD->expects($this->any())->method('isAssociationInverseSide')->will($this->returnValue(false));
        $classCD->expects($this->any())->method('isCollectionValuedAssociation')->will($this->returnValue(false));
        $classCD->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));

        return array(
            array($classAB, $classCD, "[AB]<a 1-c *<>[CD],[AB]<b 1-d *<>[CD]"),
            array($classCD, $classAB, "[CD]<>c *-a 1>[AB],[CD]<>d *-b 1>[AB]"),
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     * @dataProvider injectTwoClassesWithTwoDifferentRelationsOneToManyBidirectionnal
     * @param ClassMetadata $class1
     * @param ClassMetadata $class2
     */
    public function testMultipleRelationsManyToOneBeetweenTwoSameClasses(
        ClassMetadata $class1,
        ClassMetadata $class2,
        $expected
    ) {
        $this->assertSame(
            $expected,
            $this->grapher->generateFromMetadata(array($class1, $class2))
        );
    }

    /**
     * @param $a
     * @return bool|string
     */
    public function getAssociationClassMock($a)
    {
        switch ($a) {
            case 'a':
            case 'b':
                return 'AB';
            break;
            case 'c':
            case 'd':
                return 'CD';
                break;
        }
        return false;
    }

    /**
     * @param $a
     * @return array|bool
     */
    public function getAssociationMappingMock($a)
    {
        switch ($a) {
            case 'a':
                $return = array(
                    'isOwningSide' => true,
                    'mappedBy'      => null,
                    'inversedBy'    => 'c'
                );
                break;
            case 'b':
                $return = array(
                    'isOwningSide' => true,
                    'mappedBy'      => null,
                    'inversedBy'    => 'd'
                );
                break;
            case 'c':
                $return = array(
                    'isOwningSide' => false,
                    'mappedBy'      => 'a',
                    'inversedBy'    => null
                );
                break;
            case 'd':
                $return = array(
                    'isOwningSide' => false,
                    'mappedBy'      => 'b',
                    'inversedBy'    => null
                );
                break;
            default:
                $return = false;
        }
        return $return;
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawWithColorsAdded()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $colors = array(
            'Simple\\Entity' => 'violet',
        );

        $this->assertSame(
            '[Simple.Entity],[Simple.Entity{bg:violet}]',
            $this->grapher->generateFromMetadata(array($class1), false, $colors)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawWithColorsAddedOnParentNamespace()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $colors = array(
            'Simple' => 'violet',
        );

        $this->assertSame(
            '[Simple.Entity],[Simple.Entity{bg:violet}]',
            $this->grapher->generateFromMetadata(array($class1), false, $colors)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawColorPriority()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $colors = array(
            'Simple' => 'violet',
            'Simple\\Entity' => 'green'
        );

        $this->assertSame(
            '[Simple.Entity],[Simple.Entity{bg:green}]',
            $this->grapher->generateFromMetadata(array($class1), false, $colors)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawWithNotesAdded()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $notes = array(
            "Simple\\Entity" => array(
                'value'   => 'description TEST',
            )
        );

        $this->assertSame(
            '[Simple.Entity],[Simple.Entity]-[note:description TEST{bg:yellow}]',
            $this->grapher->generateFromMetadata(array($class1), false, array(), $notes)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawWithColoredNotesAdded()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $notes = array(
            "Simple\\Entity" => array(
                'value'   => 'description TEST',
                'color'         => 'blue'
            )
        );

        $this->assertSame(
            '[Simple.Entity],[Simple.Entity]-[note:description TEST{bg:blue}]',
            $this->grapher->generateFromMetadata(array($class1), false, array(), $notes)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawWithBothColorsAndColoredNotesAdded()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $colors = array(
            "Simple\\Entity" => "green",
        );

        $notes = array(
            "Simple\\Entity" => array(
                'value'   => 'description TEST',
                'color'         => 'blue'
            )
        );

        $this->assertSame(
            '[Simple.Entity],[Simple.Entity{bg:green}],[Simple.Entity]-[note:description TEST{bg:blue}]',
            $this->grapher->generateFromMetadata(array($class1), false, $colors, $notes)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher
     */
    public function testDrawWithAnnotationsColorsAndNotes()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\A'
            ));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.A],'
                . '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.A{bg:blue}],'
                . '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.A]-'
                . '[note:My first annotation note{bg:yellowgreen}]',
            $this->grapher->generateFromMetadata(array($class1))
        );
    }

    public function testDrawWithMethods()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\B'
            ));
        $class1->expects($this->any())->method('getFieldNames')->will($this->returnValue(array()));
        $class1->expects($this->any())->method('getAssociationNames')->will($this->returnValue(array()));

        $this->assertSame(
            '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.B|'
                . 'methodAnnotated();otherMethodAnnotated()],'
                . '[OnurbTest.Doctrine.ORMMetadataGrapher.YumlMetadataGrapher.AnnotationParserTest.B|'
                . 'methodAnnotated();otherMethodAnnotated(){bg:blue}]',
            $this->grapher->generateFromMetadata(array($class1))
        );
    }
}
