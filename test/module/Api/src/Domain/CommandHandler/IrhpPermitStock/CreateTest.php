<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\Command\IrhpPermitSector\Create as CreateSectorQuotasCmd;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitJurisdiction\Create as CreateJurisdictionQuotasCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitStock\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as PermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;

/**
 * Create IRHP Permit Type Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            PermitStockEntity::STATUS_SCORING_NEVER_RUN,
        ];
        $this->references = [
            IrhpPermitType::class => [
                2 => m::mock(IrhpPermitType::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'permitType' => '2',
            'validFrom' => '2019-01-01',
            'validTo' => '2019-02-01',
            'initialStock' => '1500'
        ];

        $command = CreateCmd::create($cmdData);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitStockEntity::class))
            ->andReturnUsing(
                function (PermitStockEntity $permitStock) use (&$savedPermitStock) {
                    $permitStock->setId(1);
                    $savedPermitStock = $permitStock;
                }
            );

        $this->expectedSideEffect(CreateSectorQuotasCmd::class, ['id' => 1], new Result());
        $this->expectedSideEffect(CreateJurisdictionQuotasCmd::class, ['id' => 1], new Result());

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['IrhpPermitStock' => 1],
            'messages' => ["IRHP Permit Stock '1' created"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
