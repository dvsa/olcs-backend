<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Cqrs;

use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Service\Qa\Cqrs\CommandCreator;
use Dvsa\Olcs\Transfer\Command\Fee\CreateFee;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CommandCreatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CommandCreatorTest extends MockeryTestCase
{
    public function testCreate()
    {
        $parameters = [
            'licence' => 7,
            'irhpApplication' => 48,
            'invoicedDate' => '2019-05-01',
            'description' => 'Permit Fee',
            'feeType' => 11045,
            'feeStatus' => Fee::STATUS_OUTSTANDING,
            'quantity' => 12
        ];

        $commandCreator = new CommandCreator();
        $command = $commandCreator->create(CreateFee::class, $parameters);
        $this->assertInstanceOf(CreateFee::class, $command);
        $this->assertArraySubset($parameters, $command->getArrayCopy());
    }
}
