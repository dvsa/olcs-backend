<?php

/**
 * Cancel Licence Fees Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Licence;

use Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees;
use PHPUnit_Framework_TestCase;

/**
 * Cancel Licence Fees Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CancelLicenceFeesTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CancelLicenceFees::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111], $command->getArrayCopy());
    }
}
