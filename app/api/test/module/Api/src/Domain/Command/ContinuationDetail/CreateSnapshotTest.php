<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\CreateSnapshot;
use PHPUnit_Framework_TestCase;

/**
 * Process Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateSnapshotTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CreateSnapshot::create(
            [
                'id' => 1,
                'user' => 2
            ]
        );

        $this->assertEquals(1, $command->getId());
        $this->assertEquals(2, $command->getUser());
    }
}
