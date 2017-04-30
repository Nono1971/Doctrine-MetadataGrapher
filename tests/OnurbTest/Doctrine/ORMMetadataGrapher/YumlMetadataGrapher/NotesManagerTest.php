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
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\NotesManager;
use PHPUnit\Framework\TestCase;

/**
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class NotesManagerTest extends TestCase
{

    /**
     * @var NotesManager
     */
    private $notesManager;

    public function setUp()
    {
        parent::setUp();

        $class1 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class1->expects($this->any())->method('getName')->will($this->returnValue('Simple\\Entity'));

        $class2 = $this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata');
        $class2->expects($this->any())->method('getName')->will($this->returnValue('Other\\Entity'));

        $classStore = $this->createMock(
            'Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\ClassStoreInterface'
        );
        $classStore->expects($this->any())->method('getClassByName')->will($this->returnCallback(
            function ($arg) use ($class1, $class2) {
                if ($arg == 'Simple\\Entity') {
                    return $class1;
                } elseif ($arg == "Other\\Entity") {
                    return $class2;
                }
                return null;
            }
        ));

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


        $this->notesManager = new NotesManager($classStore, $stringGenerator);
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\NotesManager
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            "Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher\\NotesManager",
            $this->notesManager
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\NotesManager
     *
     * @dataProvider provideTestGetNotesStringsWithGoodValues
     *
     * @param string $expected
     * @param array $notes
     */
    public function testGetNotesStringsWithGoodValues($expected, $notes)
    {
        $this->assertSame($expected, $this->notesManager->getNotesStrings($notes));
    }

    /**
     * @return array
     */
    public function provideTestGetNotesStringsWithGoodValues()
    {
        return array(
            array(
                array(
                    '[Simple.Entity]-[note:test Note 1{bg:green}]'
                ),
                array(
                    'Simple\\Entity' => array(
                        'value' => 'test Note 1',
                        'color' => 'green'
                    )
                )
            ),
            array(
                array(
                    '[Simple.Entity]-[note:test Note 2{bg:yellow}]'
                ),
                array(
                    'Simple\\Entity' => array(
                        'value'   => 'test Note 2',
                    )
                )
            ),

        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\NotesManager
     *
     * @expectedException \Exception
     */
    public function testExceptionWithEmptyArrayNote()
    {
        $stringNote = array(
            array()
        );
        $this->notesManager->getNotesStrings($stringNote);
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\NotesManager
     *
     * @expectedException \Exception
     */
    public function testExceptionWithEmptyEmptyNoteDescription()
    {
        $stringNote = array(
            array('description' => '')
        );
        $this->notesManager->getNotesStrings($stringNote);
    }
}
