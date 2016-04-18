<?php

/**
 * Snapshot
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\Command\TransportManagerApplication\Snapshot;
use PHPUnit_Framework_TestCase;

/**
 * Snapshot
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class SnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group test123
     */
    public function testStructure()
    {
        $command = Snapshot::create(
            [
                'user' => 1,
            ]
        );

        $this->assertEquals(1, $command->getUser());
    }
}
