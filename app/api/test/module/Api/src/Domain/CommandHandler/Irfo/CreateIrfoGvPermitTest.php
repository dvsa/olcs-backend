<?php

/**
 * Create Irfo Gv Permit Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\CreateIrfoGvPermit;
use Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoGvPermit as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit as IrfoGvPermitEntity;

/**
 * Create Irfo Gv Permit Test
 */
class CreateIrfoGvPermitTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateIrfoGvPermit();
        $this->mockRepo('IrfoGvPermit', IrfoGvPermit::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoGvPermitEntity::STATUS_PENDING
        ];

        $this->references = [
            Organisation::class => [
                11 => m::mock(Organisation::class)
            ],
            IrfoGvPermitType::class => [
                22 => m::mock(IrfoGvPermitType::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'organisation' => 11,
            'irfoGvPermitType' => 22,
            'irfoPermitStatus' => IrfoGvPermitEntity::STATUS_PENDING,
            'yearRequired' => 2014,
            'inForceDate' => '2015-01-01',
            'isFeeExempt' => 'Y',
            'noOfCopies' => 1
        ];

        /** @var IrfoGvPermitEntity $savedIrfoGvPermit */
        $savedIrfoGvPermit = null;

        $command = Cmd::create($data);

        $this->repoMap['IrfoGvPermit']->shouldReceive('save')
            ->once()
            ->with(m::type(IrfoGvPermitEntity::class))
            ->andReturnUsing(
                function (IrfoGvPermitEntity $irfoGvPermit) use (&$savedIrfoGvPermit) {
                    $irfoGvPermit->setId(111);
                    $savedIrfoGvPermit = $irfoGvPermit;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irfoGvPermit' => 111,
            ],
            'messages' => [
                'IRFO GV Permit created successfully'
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
}
