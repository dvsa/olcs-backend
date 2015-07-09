<?php

/**
 * Create Application Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee;
use PHPUnit_Framework_TestCase;

/**
 * Create Application Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateApplicationFeeTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CreateApplicationFee::create(['id' => 111, 'foo' => 'bar']);

        $this->assertEquals(111, $command->getId());
        $this->assertEquals(['id' => 111, 'feeTypeFeeType' => null], $command->getArrayCopy());
    }
}
