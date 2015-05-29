<?php

/**
 * Create Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Fee;

use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Entity\Fee\Fee;

/**
 * Create Fee Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateFeeTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $data = [
            'foo' => 'bar',
            'application' => 111,
            'licence' => 222,
            'task' => 333,
            'amount' => '5.50',
            'invoicedDate' => '2015-01-01',
            'feeType' => 444,
            'description' => 'Some fee'
        ];

        $command = CreateFee::create($data);

        $this->assertEquals(111, $command->getApplication());
        $this->assertEquals(222, $command->getLicence());
        $this->assertEquals(333, $command->getTask());
        $this->assertEquals('5.50', $command->getAmount());
        $this->assertEquals('2015-01-01', $command->getInvoicedDate());
        $this->assertEquals(444, $command->getFeeType());
        $this->assertEquals('Some fee', $command->getDescription());
        $this->assertEquals(Fee::STATUS_OUTSTANDING, $command->getFeeStatus());

        $this->assertEquals(
            [
                'application' => 111,
                'licence' => 222,
                'task' => 333,
                'amount' => '5.50',
                'invoicedDate' => '2015-01-01',
                'feeType' => 444,
                'description' => 'Some fee',
                'feeStatus' => Fee::STATUS_OUTSTANDING
            ],
            $command->getArrayCopy()
        );

        $command->exchangeArray(['feeStatus' => 'SomeOtherStatus']);

        $this->assertEquals('SomeOtherStatus', $command->getFeeStatus());
    }
}
