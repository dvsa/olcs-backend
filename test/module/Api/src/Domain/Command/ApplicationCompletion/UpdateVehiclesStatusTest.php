<?php

/**
 * Update Vehicles Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateVehiclesStatus;
use PHPUnit_Framework_TestCase;

/**
 * Update Vehicles Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateVehiclesStatusTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = UpdateVehiclesStatus::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
