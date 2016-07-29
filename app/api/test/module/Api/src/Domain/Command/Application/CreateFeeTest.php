<?php

/**
 * CreateFeeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateFee;
use PHPUnit_Framework_TestCase;

/**
 * CreateFeeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateFeeTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CreateFee::create(
            [
                'id' => 111,
                'foo' => 'bar',
                'feeTypeFeeType' => 'feetype',
                'optional' => true
            ]
        );

        $this->assertEquals(111, $command->getId());
        $this->assertEquals('feetype', $command->getFeeTypeFeeType());
        $this->assertTrue($command->getOptional());
    }
}
