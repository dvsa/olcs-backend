<?php

/**
 * CancelFeeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;

/**
 * CancelFeeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CancelFeeTest extends \PHPUnit\Framework\TestCase
{
    public function testStructure()
    {
        $command = CancelFee::create(
            [
                'id' => 82,
                'foo' => 'bar',
            ]
        );

        $this->assertEquals(82, $command->getId());
    }
}
