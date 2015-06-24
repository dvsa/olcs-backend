<?php

/**
 * Generate and Upload Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndUploadDocument;
use PHPUnit_Framework_TestCase;

/**
 * Generate and Upload Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GenerateAndUploadDocumentTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = GenerateAndUploadDocument::create(
            [
                'template' => 'template',
                'data' => 'data',
                'folder' => 'folder',
                'fileName' => 'fileName'
            ]
        );

        $this->assertEquals('template', $command->getTemplate());
        $this->assertEquals('data', $command->getData());
        $this->assertEquals('folder', $command->getFolder());
        $this->assertEquals('fileName', $command->getFileName());
    }
}
