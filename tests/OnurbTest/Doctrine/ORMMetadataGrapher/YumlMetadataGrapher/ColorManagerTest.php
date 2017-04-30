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
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\ColorManager;
use PHPUnit\Framework\TestCase;

/**
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class ColorManagerTest extends TestCase
{
    public function testDrawColors()
    {
        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));

        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('Other\\Entity'));

        $colors = array(
            'Simple\\Entity'    => 'yellowgreen',
            'Other'     => 'violet',
        ) ;


        $stringGenerator = $this->createMock(
            'Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\StringGeneratorInterface'
        );

        $stringGenerator->expects($this->any())->method('getClassString')->will($this->returnCallback(
            function ($class) {
                /**
                 * @var ClassMetadata $class
                 */
                return '[' . str_replace('\\', '.', $class->getName()) . ']';
            }
        ));

        $classStore =
            $this->getMockBuilder('Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface')
                ->getMock();
        $classStore->expects($this->any())->method('getClassByName')->will($this->returnCallback(
            function ($className) use ($class1, $class2) {
                if ($className == 'Simple\\Entity') {
                    return $class1;
                } elseif ($className == 'Other\\Entity') {
                    return $class2;
                }
                return null;
            }
        ));
        $classStore->expects(($this->any()))->method('getClassColor')
            ->with($this->logicalOr("Simple\\Entity", "Other\\Entity"))
        ->will($this->returnCallback(
            function ($arg) {
                if ($arg == "Simple\\Entity") {
                    return "yellowgreen";
                } elseif ($arg == "Other\\Entity") {
                    return "violet";
                }

                return null;
            }
        ));

        $colormanager = new ColorManager($stringGenerator, $classStore);

        $return = $colormanager->getColorStrings(array($class1, $class2), $colors);

        $this->assertSame(
            array(
                '[Simple.Entity{bg:yellowgreen}]',
                '[Other.Entity{bg:violet}]',
            ),
            $return
        );
    }
}
