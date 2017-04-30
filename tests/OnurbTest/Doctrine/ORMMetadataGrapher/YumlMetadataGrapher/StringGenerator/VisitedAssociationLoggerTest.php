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

use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\VisitedAssociationLogger;
use Onurb\Doctrine\ORMMetadataGrapher\YumlMetadataGrapher\StringGenerator\VisitedAssociationLoggerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the metadata to string converter
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Bruno Heron <herobrun@gmail.com>
 */
class VisitedAssociationLoggerTest extends TestCase
{

    /**
     * @var VisitedAssociationLoggerInterface
     */
    protected $logger;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->logger = new VisitedAssociationLogger();
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\VisitedAssociationLogger
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            'Onurb\\Doctrine\\ORMMetadataGrapher\\YumlMetadataGrapher'
            . '\\StringGenerator\\VisitedAssociationLoggerInterface',
            $this->logger
        );
    }

    /**
     * @covers \Onurb\Doctrine\ORMMetadataGrapher\YUMLMetadataGrapher\StringGenerator\VisitedAssociationLogger
     */
    public function testVisitAssociation()
    {
        $this->assertTrue($this->logger->visitAssociation('A'));
        $this->assertFalse($this->logger->visitAssociation('A'));
        $this->assertTrue($this->logger->visitAssociation('B', 'a'));
        $this->assertFalse($this->logger->visitAssociation('B', 'a'));
        $this->assertTrue($this->logger->visitAssociation('B', 'c'));
        $this->assertTrue($this->logger->visitAssociation('B', 'd'));
        $this->assertTrue($this->logger->visitAssociation('C', 'b'));
        $this->assertTrue($this->logger->visitAssociation('D', 'b'));
        $this->assertFalse($this->logger->visitAssociation('C', 'b'));

        $this->assertTrue($this->logger->isVisitedAssociation('A'));
        $this->assertTrue($this->logger->isVisitedAssociation('B'));
        $this->assertTrue($this->logger->isVisitedAssociation('B', 'a'));
        $this->assertTrue($this->logger->isVisitedAssociation('C'));
        $this->assertTrue($this->logger->isVisitedAssociation('D'));
        $this->assertFalse($this->logger->isVisitedAssociation('E'));
        $this->assertFalse($this->logger->isVisitedAssociation('B', 'e'));
    }
}
