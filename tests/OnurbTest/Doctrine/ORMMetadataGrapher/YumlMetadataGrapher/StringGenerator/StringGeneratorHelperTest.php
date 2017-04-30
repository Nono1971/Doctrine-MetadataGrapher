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

use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\StringGeneratorHelper;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\StringGeneratorHelperInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class StringGeneratorHelperTest extends TestCase
{

    /**
     * @var StringGeneratorHelperInterface
     */
    protected $stringHelper;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->stringHelper = new StringGeneratorHelper();
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\StringGeneratorHelper
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            'Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\StringGeneratorHelperInterface',
            $this->stringHelper
        );
    }

    /**
     * @return array
     */
    public function provideTestMakeSimpleLinkString()
    {
        return array(
            array('[A]', false, 'b', 1, 'B', '[A]<>-b 1>[B]'),
            array('[A]', false, 'b', 2, 'B', '[A]<>-b *>[B]'),
            array('[A]', true, 'b', 1, 'B', '[A]<-b 1<>[B]'),
            array('[A]', true, 'b', 2, 'B', '[A]<-b *<>[B]'),
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\StringGeneratorHelper
     * @param string $classString
     * @param boolean $isInverse
     * @param string $association
     * @param int $classCount
     * @param string $Class2Name
     * @param string $expected
     *
     * @dataProvider  provideTestMakeSimpleLinkString
     */
    public function testMakeSingleSidedLinkString(
        $classString,
        $isInverse,
        $association,
        $classCount,
        $Class2Name,
        $expected
    ) {
        $this->assertSame(
            $expected,
            $this->stringHelper->makeSingleSidedLinkString(
                $classString,
                $isInverse,
                $association,
                $classCount,
                $Class2Name
            )
        );
    }

    /**
     * @return array
     */
    public function provideTestMakeDoubleSidedLinkString()
    {
        return array(
            array('[A]', '[B]', false, true,'a', 1, 'b', 1, '[A]a 1-b 1>[B]'),
            array('[A]', '[B]', false, true,'a', 2, 'b', 1, '[A]a *-b 1>[B]'),
            array('[A]', '[B]', false, true,'a', 1, 'b', 2, '[A]a 1-b *>[B]'),
            array('[A]', '[B]', false, true,'a', 2, 'b', 2, '[A]a *-b *>[B]'),
            array('[A]', '[B]', true, true,'a', 1, 'b', 1, '[A]<a 1-b 1<>[B]'),
            array('[A]', '[B]', false, false,null, 0, 'b', 1, '[A]-b 1>[B]'),
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\StringGeneratorHelper
     * @dataProvider provideTestMakeDoubleSidedLinkString
     *
     * @param $class1String
     * @param $class2String
     * @param $bidirectional
     * @param $isInverse
     * @param $class2SideName
     * @param $class2Count
     * @param $class1SideName
     * @param $class1Count
     * @param $expected
     */
    public function testMakeDoubleSidedLinkString(
        $class1String,
        $class2String,
        $bidirectional,
        $isInverse,
        $class2SideName,
        $class2Count,
        $class1SideName,
        $class1Count,
        $expected
    ) {
        $this->assertSame(
            $expected,
            $this->stringHelper->makeDoubleSidedLinkString(
                $class1String,
                $class2String,
                $bidirectional,
                $isInverse,
                $class2SideName,
                $class2Count,
                $class1SideName,
                $class1Count
            )
        );
    }

    /**
     * @return array
     */
    public function provideTestGetClassText()
    {
        return array(
            array(
                'A',
                array('a', 'b', 'c'),
                '[A|a;b;c]'
            ),
            array(
                'A',
                array('+a', 'b', 'c'),
                '[A|+a;b;c]'
            ),
            array(
                'A',
                array(),
                '[A]'
            ),
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\StringGeneratorHelper
     * @dataProvider provideTestGetClassText
     *
     * @param string $className
     * @param array $fields
     * @param string $expected
     */
    public function testGetClassText($className, array $fields, $expected)
    {
        $this->assertSame($expected, $this->stringHelper->getClassText($className, $fields));
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\StringGeneratorHelper
     * @expectedException \Exception
     */
    public function testExceptionWhenImpossibleClassCountValue()
    {
        $this->stringHelper->makeDoubleSidedLinkString(
            '[A]',
            '[B]',
            true,
            false,
            'a',
            1,
            'b',
            0
        );
    }
}
