<?php

/**
 * Dispatch Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use PHPUnit_Framework_TestCase;

/**
 * Create Document Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DispatchDocumentTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = DispatchDocument::create(
            [
                'user' => 1
            ]
        );

        $this->assertEquals(1, $command->getUser());
    }
}
