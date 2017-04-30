<?php
namespace OnurbTest\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class AnnotationParserTest extends TestCase
{
    /**
     * @var AnnotationParser
     */
    private $parser;

    /**
     * @var ClassMetadata
     */
    private $class1;

    /**
     * @var ClassMetadata
     */
    private $class2;

    /**
     * @var ClassMetadata
     */
    private $class3;

    /**
     * @var ClassMetadata
     */
    private $class4;

    /**
     * @var ClassMetadata
     */
    private $class5;

    /**
     * @var ClassMetadata
     */
    private $classWithDisplayError;

    /**
     * @var ClassMetadata
     */
    private $class7;

    /**
     * @var ClassMetadata
     */
    private $class8;

    /**
     * @var ClassMetadata
     */
    private $class9;

    /**
     * @var ClassMetadata
     */
    private $class10;

    public function __construct()
    {
        parent::__construct();

        require_once('AnnotationParserTest/A.php');
        require_once('AnnotationParserTest/B.php');
        require_once('AnnotationParserTest/C.php');
        require_once('AnnotationParserTest/D.php');
        require_once('AnnotationParserTest/E.php');
        require_once('AnnotationParserTest/F.php');
        require_once('AnnotationParserTest/G.php');
        require_once('AnnotationParserTest/H.php');
        require_once('AnnotationParserTest/I.php');
        require_once('AnnotationParserTest/J.php');

        $this->parser = new AnnotationParser();

        $this->class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\A'
            ));

        $this->class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class2->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\B'
            ));

        $this->class3 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class3->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\C'
            ));

        $this->class4 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class4->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\D'
            ));

        $this->class5 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class5->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\E'
            ));

        $this->classWithDisplayError = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->classWithDisplayError->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\F'
            ));

        $this->class7 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class7->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\G'
            ));

        $this->class8 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class8->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\H'
            ));

        $this->class9 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class9->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\I'
            ));
        $this->class10 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class10->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\AnnotationParserTest\\J'
            ));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testClassAAnnotations()
    {
        $this->assertSame(
            [
                'colors' => [
                    $this->class1->getName() => "blue"
                ],
                'notes' => [
                    $this->class1->getName() => [
                        'value' => 'My first annotation note',
                        'color' => 'yellowgreen'
                    ]
                ]
            ],
            $this->parser->getAnnotations(
                array(
                    $this->class1,
                )
            )
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testClassBAnnotation()
    {
        $this->assertSame(
            array(
                'colors' => array(
                    $this->class2->getName() => "blue"
                ),
                'notes' => array()
            ),
            $this->parser->getAnnotations(
                array(
                    $this->class2,
                )
            )
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testClassCAnnotation()
    {
        $this->assertSame(
            array(
                'colors' => array(
                    $this->class3->getName() => "tomato"
                ),
                'notes' => array()
            ),
            $this->parser->getAnnotations(
                array(
                    $this->class3,
                )
            )
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testClassDAnnotation()
    {
        $this->assertSame(
            array(
                'colors' => array(
                    $this->class4->getName() => "pink"
                ),
                'notes' => array()
            ),
            $this->parser->getAnnotations(
                array(
                    $this->class4,
                )
            )
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testClassEAnnotation()
    {
        $this->assertSame(
            array(
                'colors' => array(),
                'notes' => array(
                    $this->class5->getName() => array(
                        'value' => 'My REAL !!! first annotation note',
                        'color' => null
                    )
                )
            ),
            $this->parser->getAnnotations(
                array(
                    $this->class5,
                )
            )
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testGetClassMethods()
    {
        $this->assertSame(
            array(
                'methodAnnotated',
                'otherMethodAnnotated'
            ),
            $this->parser->getClassMethodsAnnotations($this->class2->getName())
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testGetClassMethodsWithNonExistingClass()
    {
        $this->assertSame(
            array(),
            $this->parser->getClassMethodsAnnotations('Class\\That\\Does\\Not\\Exist')
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testGetHiddenProperties()
    {
        $this->assertSame(
            array(
                'secret'
            ),
            $this->parser->getHiddenAttributes($this->class9->getName())
        );
    }

    public function testHasPropertiesHidden()
    {
        $this->assertFalse($this->parser->getClassHidesAttributes($this->class9->getName()));
        $this->assertTrue($this->parser->getClassHidesAttributes($this->class10->getName()));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     * @expectedException \Exception
     */
    public function testClassDisplayAnnotationsThrowsException()
    {
        $this->parser->getClassDisplay($this->classWithDisplayError->getName());
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testClassDisplayWithAnnotations()
    {
        $this->assertSame('hide', $this->parser->getClassDisplay($this->class7->getName()), true);
        $this->assertSame('show', $this->parser->getClassDisplay($this->class8->getName()));
        $this->assertNull($this->parser->getClassDisplay($this->class1->getName()));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testGetClassDisplayReturnsNullIfClasssDoesntExist()
    {
        /**
         * @var ClassMetadata $class
         */
        $class = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'A'
            ));
        $this->assertNull($this->parser->getClassDisplay($class->getName()));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\AnnotationParser
     */
    public function testGetHiddenAttributeReturnsEmptyArrayIfClasssDoesntExist()
    {
        /**
         * @var ClassMetadata $class
         */
        $class = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'A'
            ));
        $this->assertSame(array(), $this->parser->getHiddenAttributes($class->getName()));
    }
}
