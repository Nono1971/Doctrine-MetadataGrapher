<?php
namespace OnurbTest\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher;

use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\ClassStore;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class ClassStoreTest extends TestCase
{
    /**
     * @var ClassStore
     */
    protected $classStore;

    protected $class1;
    protected $class2;
    protected $class3;
    protected $class4;
    protected $class5;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        require_once('ClassStoreTest/A.php');
        require_once('ClassStoreTest/B.php');
        require_once('ClassStoreTest/C.php');
        require_once('ClassStoreTest/D.php');
        require_once('ClassStoreTest/E.php');
        
        $this->class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A'
            ));

        $this->class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class2->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B'
            ));

        $this->class3 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class3->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C'
            ));

        $this->class4 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class4->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D'
            ));

        $this->class5 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class5->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\E'
            ));

        $this->classStore = new ClassStore(array(
            $this->class1,
            $this->class3,
            $this->class5,
            $this->class4,
            $this->class2
        ));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\ClassStore
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            'Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface',
            $this->classStore
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\ClassStore
     */
    public function testGetParent()
    {
        $this->assertNull($this->classStore->getParent($this->class1));
        $this->assertNull($this->classStore->getParent($this->class2));
        $this->assertNull($this->classStore->getParent($this->class3));

        $this->assertInstanceOf(
            'Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata',
            $this->classStore->getParent($this->class4)
        );
        $this->assertInstanceOf(
            'Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata',
            $this->classStore->getParent($this->class5)
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\ClassStore
     */
    public function testGetClassByNameWithGoodValue()
    {
        $this->assertSame(
            $this->class1,
            $this->classStore->getClassByName(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A'
            )
        );
        $this->assertSame(
            $this->class2,
            $this->classStore->getClassByName(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B'
            )
        );
        $this->assertSame(
            $this->class3,
            $this->classStore->getClassByName(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C'
            )
        );
        $this->assertSame(
            $this->class4,
            $this->classStore->getClassByName(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D'
            )
        );
        $this->assertSame(
            $this->class5,
            $this->classStore->getClassByName(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\E'
            )
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\ClassStore
     */
    public function testGetClassByNameWithWrongValue()
    {
        $this->assertNull($this->classStore->getClassByName('MyGreatUnknownClass'));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\ClassStore
     */
    public function testGetIndexedClasses()
    {

        $expected = array(
            "OnurbTest" => array(
                "__class" => null,
                "__color" => null,
                "Doctrine" => array(
                    "__class" => null,
                    "__color" => null,
                    "ORMMetadataGrapher" => array(
                        "__class" => null,
                        "__color" => null,
                        "YumlMetadataGrapher" => array(
                            "__class" => null,
                            "__color" => null,
                            "ClassStoreTest" => array(
                                "__class" => null,
                                "__color" => null,
                                "A" => array(
                                    "__class" => $this->class1,
                                    "__color" => null,
                                ),
                                "C" => array(
                                    "__class" => $this->class3,
                                    "__color" => null,
                                ),
                                "E" => array(
                                    "__class" => $this->class5,
                                    "__color" => null,
                                ),
                                "D" => array(
                                    "__class" => $this->class4,
                                    "__color" => null,
                                ),
                                "B" => array(
                                    "__class" => $this->class2,
                                    "__color" => null,
                                ),

                            )
                        )
                    )
                )
            )
        );

        $this->assertSame($expected, $this->classStore->getIndexedClasses());

        $expected["OnurbTest"]["Doctrine"]["ORMMetadataGrapher"]
            ["YumlMetadataGrapher"]["__color"] = "violet";

        $expected["OnurbTest"]["Doctrine"]["ORMMetadataGrapher"]
            ["YumlMetadataGrapher"]["ClassStoreTest"]["A"]["__color"] = "skyblue";

        $expected["OnurbTest"]["Doctrine"]["ORMMetadataGrapher"]
            ["YumlMetadataGrapher"]["ClassStoreTest"]["B"]["__color"] = "blue";

        $expected["OnurbTest"]["Doctrine"]["ORMMetadataGrapher"]
            ["YumlMetadataGrapher"]["ClassStoreTest"]["C"]["__color"] = "yellow";



        $this->classStore->storeColors(array(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A" => "skyblue",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B" => "blue",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C" => "yellow",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher" => "violet",
        ));

        $this->assertSame($expected, $this->classStore->getIndexedClasses());
    }

    public function testGetClassColor()
    {
        $this->classStore->storeColors(array(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A" => "skyblue",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B" => "blue",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C" => "yellow",
        ));

        $this->assertSame('skyblue', $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A"
        ));

        $this->assertSame('blue', $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B"
        ));

        $this->assertSame('yellow', $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C"
        ));

        $this->assertNull($this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D"
        ));

        $this->assertNull($this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\E"
        ));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\ClassStore
     */
    public function testGetClassColorWithNamespaceColor()
    {
        $this->classStore->storeColors(array(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A" => "skyblue",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B" => "blue",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C" => "yellow",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher" => "yellowgreen",
            "OnurbTest\\Doctrine\\ORMMetadataGrapher" => "purple",
        ));

        $this->assertSame('skyblue', $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A"
        ));

        $this->assertSame('blue', $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B"
        ));

        $this->assertSame('yellow', $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C"
        ));

        $this->assertSame("yellowgreen", $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D"
        ));

        $this->assertSame("yellowgreen", $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\E"
        ));

        $this->assertSame("purple", $this->classStore->getClassColor(
            "OnurbTest\\Doctrine\\ORMMetadataGrapher"
        ));

        $this->assertNull($this->classStore->getClassColor(
            "OnurbTest\\Doctrine"
        ));
    }
}
