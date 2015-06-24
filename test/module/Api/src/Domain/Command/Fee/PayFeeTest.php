<?php

/**
 * Pay Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee;
use PHPUnit_Framework_TestCase;

/**
 * Pay Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PayFeeTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = PayFee::create(
            [
                'id' => 69,
                'foo' => 'bar',
            ]
        );

        $this->assertEquals(69, $command->getId());
    }
}
