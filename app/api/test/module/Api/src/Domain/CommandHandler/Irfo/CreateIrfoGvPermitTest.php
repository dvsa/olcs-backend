<?php

/**
 * Create Irfo Gv Permit Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\CreateIrfoGvPermit;
use Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoGvPermit as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit as IrfoGvPermitEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as FeeCreateFee;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Create Irfo Gv Permit Test
 */
class CreateIrfoGvPermitTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateIrfoGvPermit();
        $this->mockRepo('IrfoGvPermit', IrfoGvPermit::class);
        $this->mockRepo('FeeType', FeeTypeRepository::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoGvPermitEntity::STATUS_PENDING,
            'fee-type-ref-data',
            'irfo-fee-type-ref-data',
            FeeTypeEntity::FEE_TYPE_IRFOGVPERMIT
        ];

        $this->references = [
            Organisation::class => [
                11 => m::mock(Organisation::class)
            ],
            IrfoGvPermitType::class => [
                22 => m::mock(IrfoGvPermitType::class)
            ],
            FeeTypeEntity::class => [
                1 => m::mock(FeeTypeEntity::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'organisation' => 11,
            'irfoGvPermitType' => 22,
            'yearRequired' => 2014,
            'inForceDate' => '2015-01-01',
            'isFeeExempt' => 'Y',
            'noOfCopies' => 1
        ];

        /** @var IrfoGvPermitEntity $savedIrfoGvPermit */
        $savedIrfoGvPermit = new IrfoGvPermitEntity(
            $this->references[Organisation::class][11],
            $this->references[IrfoGvPermitType::class][22],
            m::mock(RefDataEntity::class)
                ->shouldReceive('getId')
                ->andReturn(IrfoGvPermitEntity::STATUS_PENDING)->getMock()
        );

        //$savedIrfoGvPermit = null;

        $command = Cmd::create($data);

        $this->repoMap['IrfoGvPermit']->shouldReceive('save')
            //->once()
            ->with(m::type(IrfoGvPermitEntity::class))
            ->andReturnUsing(
                function (IrfoGvPermitEntity $irfoGvPermit) use (&$savedIrfoGvPermit) {
                    $irfoGvPermit->setId(111);
                    $savedIrfoGvPermit = $irfoGvPermit;
                }
            );

        $this->repoMap['IrfoGvPermit']->shouldReceive('getRefDataReference')
            ->with(FeeTypeEntity::FEE_TYPE_IRFOGVPERMIT)
            ->andReturn($this->refData['fee-type-ref-data']);

        $this->references[FeeTypeEntity::class][1]->shouldReceive('getId')
            ->andReturn(1);

        $this->references[IrfoGvPermitType::class][22]->shouldReceive('getIrfoFeeType')
            ->with()
            ->andReturn($this->refData['fee-type-ref-data']);

        $this->repoMap['FeeType']->shouldReceive('fetchLatestForIrfo')
            ->andReturn($this->references[FeeTypeEntity::class][1]);

        $result1 = new Result();
        $result1->addMessage('IRFO GV Permit side affect');
        $this->expectedSideEffect(
            FeeCreateFee::class,
            [
                'irfoGvPermit' => 111,
                'invoicedDate' => date('Y-m-d'),
                'description' => ' for IRFO permit ' . 111,
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

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irfoGvPermit' => 111,
            ],
            'messages' => [
                'IRFO GV Permit created successfully',
                'IRFO GV Permit side affect'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->references[Organisation::class][11], $savedIrfoGvPermit->getOrganisation());
        $this->assertSame($this->references[IrfoGvPermitType::class][22], $savedIrfoGvPermit->getIrfoGvPermitType());
        $this->assertSame(
            $this->refData[IrfoGvPermitEntity::STATUS_PENDING],
            $savedIrfoGvPermit->getIrfoPermitStatus()
        );
        $this->assertEquals(2014, $savedIrfoGvPermit->getYearRequired());
        $this->assertEquals('2015-01-01', $savedIrfoGvPermit->getInForceDate()->format('Y-m-d'));
        $this->assertEquals('Y', $savedIrfoGvPermit->getIsFeeExempt());
        $this->assertEquals(1, $savedIrfoGvPermit->getNoOfCopies());
    }

    public function testGetIrfoFeeId()
    {
        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn('TEST12');

        $this->assertEquals('IR0TEST12', $this->sut->getIrfoFeeId($organisation));
    }
}
