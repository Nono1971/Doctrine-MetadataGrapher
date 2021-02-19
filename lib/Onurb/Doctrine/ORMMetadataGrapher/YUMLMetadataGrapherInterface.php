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

namespace Onurb\Doctrine\ORMMetadataGrapher;

use Doctrine\Persistence\Mapping\ClassMetadata;

/**
 * Interface of utility to generate yUML compatible strings from metadata graphs
 *
 * @license MIT
 * @link    http://www.doctrine-project.org/
 * @author  Marco Pivetta   <ocramius@gmail.com>
 * @author  Bruno Heron     <<herobrun@gmail.com>
 */
interface YUMLMetadataGrapherInterface
{
    /**
     * Generate a yUML compatible `dsl_text` to describe a given array of entities
     *
     * @param  $metadata ClassMetadata[]
     * @param boolean $displayTypes
     * @param array $colors
     * @param array $notes
     * @return string
     */
    public function generateFromMetadata(array $metadata, $displayTypes, $colors = array(), $notes = array());
}
