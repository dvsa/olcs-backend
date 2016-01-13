<?php

/**
 * Update Addresses Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateAddressesStatus;
use PHPUnit_Framework_TestCase;

/**
 * Update Addresses Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateAddressesStatusTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = UpdateAddressesStatus::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
