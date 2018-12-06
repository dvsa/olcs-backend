<?php

/**
 * Update TaxiPhv Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateTaxiPhvStatus;
use PHPUnit_Framework_TestCase;

/**
 * Update TaxiPhv Status Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTaxiPhvStatusTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = UpdateTaxiPhvStatus::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
