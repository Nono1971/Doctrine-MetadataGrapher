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

use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\ClassStore;
use PHPUnit_Framework_TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class ClassStoreTest extends PHPUnit_Framework_TestCase
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

        $this->class1 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class1->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\A'
            ));

        $this->class2 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class2->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\B'
            ));

        $this->class3 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class3->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\C'
            ));

        $this->class4 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $this->class4->expects($this->any())->method('getName')
            ->will($this->returnValue(
                'OnurbTest\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreTest\\D'
            ));

        $this->class5 = $this->getMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
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

    public function testInstance()
    {
        $this->assertInstanceOf(
            'Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStore',
            $this->classStore
        );
    }

    public function testGetParent()
    {
        $this->assertNull($this->classStore->getParent($this->class1));
        $this->assertNull($this->classStore->getParent($this->class2));
        $this->assertNull($this->classStore->getParent($this->class3));
        $this->assertInstanceOf(
            'Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata',
            $this->classStore->getParent($this->class4)
        );
        $this->assertNull($this->classStore->getParent($this->class5));
    }

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

    public function testGetClassByNameWithWrongValue()
    {
        $this->assertNull($this->classStore->getClassByName('MaSuperClassePasCree'));
    }
}
