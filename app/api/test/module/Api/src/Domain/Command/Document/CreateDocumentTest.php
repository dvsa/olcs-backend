<?php

/**
 * Create Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument;
use PHPUnit_Framework_TestCase;

/**
 * Create Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateDocumentTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CreateDocument::create(
            [
                'identifier' => 1,
                'description' => 'desc',
                'filename' => 'filename',
                'licence' => 2,
                'category' => 3,
                'subCategory' => 4,
                'isExternal' => 1,
                'issuedDate' => '2015-01-01',
                'size' => 100,
                'user' => 1
            ]
        );

        $this->assertEquals(1, $command->getIdentifier());
        $this->assertEquals('desc', $command->getDescription());
        $this->assertEquals('filename', $command->getFilename());
        $this->assertEquals(2, $command->getLicence());
        $this->assertEquals(3, $command->getCategory());
        $this->assertEquals(4, $command->getSubCategory());
        $this->assertEquals(1, $command->getIsExternal());
        $this->assertEquals('2015-01-01', $command->getIssuedDate());
        $this->assertEquals(100, $command->getSize());
        $this->assertEquals(1, $command->getUser());
    }
}
