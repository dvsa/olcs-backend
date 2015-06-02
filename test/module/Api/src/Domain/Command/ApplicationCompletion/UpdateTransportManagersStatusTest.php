<?php

/**
 * Update Transport Managers Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateTransportManagersStatus;
use PHPUnit_Framework_TestCase;

/**
 * Update Transport Managers Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTransportManagersStatusTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = UpdateTransportManagersStatus::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
