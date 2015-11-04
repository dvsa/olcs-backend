<?php

/**
 * Grant IrfoPsvAuth Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Common\BusinessRule\Rule\Fee;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\GrantIrfoPsvAuth;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuthNumber;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthNumber as IrfoPsvAuthNumberEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Transfer\Command\Irfo\GrantIrfoPsvAuth as Cmd;
use Dvsa\Olcs\Transfer\Command\Irfo\UpdateIrfoPsvAuth as UpdateIrfoPsvAuthCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepository;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as FeeCreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Grant IrfoPsvAuth Test
 */
class GrantIrfoPsvAuthTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GrantIrfoPsvAuth();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuth::class);
        $this->mockRepo('IrfoPsvAuthNumber', IrfoPsvAuthNumber::class);
        $this->mockRepo('FeeType', FeeTypeRepository::class);
        $this->mockRepo('Fee', FeeRepository::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoPsvAuthEntity::STATUS_PENDING,
            IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            IrfoPsvAuthEntity::STATUS_GRANTED,
            'fee-type-ref-data',
            'irfo-fee-type-ref-data',
            FeeTypeEntity::FEE_TYPE_IRFOPSVAPP,
            FeeTypeEntity::FEE_TYPE_IRFOPSVANN,
            FeeTypeEntity::FEE_TYPE_IRFOPSVCOPY,
            FeeEntity::STATUS_PAID,
            FeeEntity::STATUS_OUTSTANDING,
        ];

        $this->references = [
            Organisation::class => [
                11 => m::mock(Organisation::class)
            ],
            Country::class => [
                'GB' => m::mock(Country::class)
            ],
            FeeTypeEntity::class => [
                1 => m::mock(FeeTypeEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandAnnualFeeExempt()
    {
        $data = [
            'id' => 42,
            'version' => 2,
            'organisation' => 11,
            'irfoPsvAuthType' => 22,
            'status' => IrfoPsvAuthEntity::STATUS_PENDING,
            'validityPeriod' => 1,
            'inForceDate' => '2015-01-01',
            'expiryDate' => '2016-01-01',
            'applicationSentDate' => '2014-01-01',
            'serviceRouteFrom' => 'From',
            'serviceRouteTo' => 'To',
            'journeyFrequency' => IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            'isFeeExemptApplication' => 'Y',
            'isFeeExemptAnnual' => 'Y',
            'exemptionDetails' => 'testing',
            'copiesRequired' => 1,
            'copiesRequiredTotal' => 1,
            'countrys' => ['GB'],
            'irfoPsvAuthNumbers' => [
                ['name' => 'test 1'],
                ['name' => ''],
            ],
        ];

        $command = Cmd::create($data);

        // handle update
        $this->expectedSideEffect(
            UpdateIrfoPsvAuthCmd::class, $command->getArrayCopy(),
            (new Result())->addMessage('IRFO PSV Auth updated successfully')
                ->addId('irfoPsvAuth', $data['id'])
        );

        $this->repoMap['FeeType']->shouldReceive('getLatestIrfoFeeType')
            ->andReturn($this->references[FeeTypeEntity::class][1]);

        $irfoPsvAuth = $this->generatePsvAuth($data);

        $feeType = new FeeTypeEntity();
        $fee = new FeeEntity($feeType, 0, $this->refData[FeeEntity::STATUS_PAID]);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->andReturn($irfoPsvAuth)
            ->shouldReceive('save');

        $this->repoMap['Fee']->shouldReceive('fetchApplicationFeeByPsvAuthId')
            ->andReturn($fee);

        $result1 = new Result();
        $result1->addMessage('IRFO PSV Auth Annual Fee created');
        $this->expectedSideEffect(
            FeeCreateFee::class,
            [
                'irfoGvPermit' => null,
                'invoicedDate' => date('Y-m-d'),
                'description' => ' for Auth ' . $data['id'],
                'feeType' => 1,
                'amount' => 0,
                'feeStatus' => FeeEntity::STATUS_PAID,
                'application' => null,
                'busReg' => null,
                'licence' => null,
                'task' => null
            ],
            $result1
        );

        $result2 = new Result();
        $result2->addMessage('IRFO PSV Auth Copies Fee created');
        $this->expectedSideEffect(
            FeeCreateFee::class,
            [
                'irfoPsvAuth' => $data['id'],
                'invoicedDate' => date('Y-m-d'),
                'description' => ' for Auth ' . $data['id'],
                'feeType' => 1,
                'amount' => 0,
                'feeStatus' => FeeEntity::STATUS_OUTSTANDING,
                'application' => null,
                'busReg' => null,
                'licence' => null,
                'task' => null
            ],
            $result2
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irfoPsvAuth' => $data['id'],
            ],
            'messages' => [
                'IRFO PSV Auth granted successfully',
                'IRFO PSV Auth Annual Fee created',
                'IRFO PSV Auth Copies Fee created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandAnnualFeeOutstanding()
    {
        $this->setExpectedException(BadRequestException::class);

        $data = [
            'id' => 42,
            'version' => 2,
            'organisation' => 11,
            'irfoPsvAuthType' => 22,
            'status' => IrfoPsvAuthEntity::STATUS_PENDING,
            'validityPeriod' => 1,
            'inForceDate' => '2015-01-01',
            'expiryDate' => '2016-01-01',
            'applicationSentDate' => '2014-01-01',
            'serviceRouteFrom' => 'From',
            'serviceRouteTo' => 'To',
            'journeyFrequency' => IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            'isFeeExemptApplication' => 'Y',
            'isFeeExemptAnnual' => 'Y',
            'exemptionDetails' => 'testing',
            'copiesRequired' => 1,
            'copiesRequiredTotal' => 1,
            'countrys' => ['GB'],
            'irfoPsvAuthNumbers' => [
                ['name' => 'test 1'],
                ['name' => ''],
            ],
        ];

        $command = Cmd::create($data);

        // handle update
        $this->expectedSideEffect(
            UpdateIrfoPsvAuthCmd::class, $command->getArrayCopy(),
            (new Result())->addMessage('IRFO PSV Auth updated successfully')
                ->addId('irfoPsvAuth', $data['id'])
        );

        $this->repoMap['FeeType']->shouldReceive('getLatestIrfoFeeType')
            ->andReturn($this->references[FeeTypeEntity::class][1]);

        $irfoPsvAuth = $this->generatePsvAuth($data);

        $feeType = new FeeTypeEntity();
        $fee = new FeeEntity($feeType, 0, $this->refData[FeeEntity::STATUS_OUTSTANDING]);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->andReturn($irfoPsvAuth)
            ->shouldReceive('save');

        $this->repoMap['Fee']->shouldReceive('fetchApplicationFeeByPsvAuthId')
            ->andReturn($fee);

        $this->sut->handleCommand($command);
    }

    private function generatePsvAuth($data)
    {
        $irfoPsvAuthNumber1 = m::mock(IrfoPsvAuthNumberEntity::class)->makePartial();
        $irfoPsvAuthNumber1->setId(101);
        $irfoPsvAuthNumber1->setName('existing number');

        $irfoPsvAuthNumber2 = m::mock(IrfoPsvAuthNumberEntity::class)->makePartial();
        $irfoPsvAuthNumber2->setId(102);
        $irfoPsvAuthNumber2->setName('deleted number');

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth = m::mock(IrfoPsvAuthEntity::class)->makePartial();
        $irfoPsvAuth->setId($data['id']);
        $irfoPsvAuth->setIrfoPsvAuthNumbers([$irfoPsvAuthNumber1, $irfoPsvAuthNumber2]);
        $irfoPsvAuth->setIsFeeExemptAnnual($data['isFeeExemptAnnual']);

        $irfoPsvAuth->setStatus($this->refData[IrfoPsvAuthEntity::STATUS_PENDING]);

        return $irfoPsvAuth;
    }
}
