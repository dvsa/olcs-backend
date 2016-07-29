<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee;
use PHPUnit_Framework_TestCase;

/**
 * CreateApplicationFee test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateApplicationFeeTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CreateApplicationFee::create(
            [
                'feeTypeFeeType' => 'foo',
                'description' => 'bar',
                'optional' => true,
            ]
        );

        $this->assertEquals('foo', $command->getFeeTypeFeeType());
        $this->assertEquals('bar', $command->getDescription());
        $this->assertTrue($command->getOptional());
    }
}
