<?php

/**
 * Create Miscellaneous Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\CreateMiscellaneousFee;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Transfer\Command\Fee\CreateMiscellaneousFee as CreateMiscellaneousFeeCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Miscellaneous Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CreateMiscellaneousFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateMiscellaneousFee();
        $this->mockRepo('Fee', FeeRepo::class);
        // $this->mockRepo('User', UserRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            FeeEntity::STATUS_OUTSTANDING
        ];

        $this->references = [
            FeeTypeEntity::class => [
                99 => m::mock(FeeTypeEntity::class)
                    ->shouldReceive('getIsMiscellaneous')
                    ->andReturn(true)
                    ->shouldReceive('getDescription')
                    ->andReturn('test misc fee')
                    ->getMock(),
                101 => m::mock(FeeTypeEntity::class)
                    ->shouldReceive('getIsMiscellaneous')
                    ->andReturn(false)
                    ->getMock(),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommandMisc()
    {
        $command = CreateMiscellaneousFeeCmd::create(
            [
                'amount' => '1234.56',
                'invoicedDate' => '2015-06-18',
                'feeType' => '99',
            ]
        );

        /** @var FeeEntity $savedFee */
        $savedFee = null;

        $this->repoMap['Fee']->shouldReceive('save')
            ->once()
            ->with(m::type(FeeEntity::class))
            ->andReturnUsing(
                function (FeeEntity $fee) use (&$savedFee) {
                    $fee->setId(111);
                    $savedFee = $fee;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 111,
            ],
            'messages' => [
                'Fee created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandInvalidFeeType()
    {
        $command = CreateMiscellaneousFeeCmd::create(
            [
                'amount' => '1234.56',
                'invoicedDate' => '2015-06-18',
                'feeType' => '101',
            ]
        );

        $this->setExpectedException(ValidationException::class);

        $result = $this->sut->handleCommand($command);
    }
}
