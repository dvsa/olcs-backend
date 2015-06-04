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
class CancelAllInterimFeesTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = CreateFee::create(
            [
                'id' => 121,
                'foo' => 'bar',
            ]
        );

        $this->assertEquals(121, $command->getId());
    }
}
