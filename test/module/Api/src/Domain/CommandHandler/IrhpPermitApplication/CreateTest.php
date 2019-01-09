<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitApplication\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Transfer\Command\IrhpPermitApplication\Create as CreateCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Irhp Permit Application test
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplicationRepo::class);
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            IrhpPermitType::class => [
                1 => m::mock(IrhpPermitType::class),
                3 => m::mock(IrhpPermitType::class)
            ],
            Licence::class => [
                2 => m::mock(Licence::class),
            ],
            IrhpPermitWindow::class => [
                300 => m::mock(IrhpPermitWindow::class),
                400 => m::mock(IrhpPermitWindow::class),
            ],
            EcmtPermitApplication::class => [
                4 => m::mock(EcmtPermitApplication::class),
            ],
            IrhpApplication::class => [
                5 => m::mock(IrhpApplication::class),
            ],
            IrhpPermitStock::class => [
                100 => m::mock(IrhpPermitStock::class),
                200 => m::mock(IrhpPermitStock::class),
            ],
            IrhpPermitApplication::class => [
                125 => m::mock(IrhpPermitStock::class),
                150 => m::mock(IrhpPermitStock::class),
            ],
        ];

        $this->refData = [
            CreateHandler::SOURCE_SELFSERVE,
            CreateHandler::STATUS_NOT_YET_SUBMITTED,
            EcmtPermitApplication::PERMIT_TYPE
        ];

        parent::initReferences();
    }

    public function testHandleCommandTwoIrhpApplications()
    {
        $permitTypeId = 1;
        $licenceId = 2;
        $stockAId = 100;
        $stockBId = 200;
        $windowAId = 300;
        $windowBId = 400;

        $cmdData = [
            'type' => $permitTypeId,
            'licence' => $licenceId,
        ];

        $command = CreateCmd::create($cmdData);

        $irhpApplication = null;

        $this->repoMap['IrhpApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpApplication::class))
            ->once()
            ->andReturnUsing(
                function (IrhpApplication $app) use (&$irhpApplication) {
                    $irhpApplication = $app;
                    $app->setId(5);
                }
            );

        $stocks = $this->references[IrhpPermitStock::class];
        $windows = $this->references[IrhpPermitWindow::class];

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('getAllValidStockByPermitType')
            ->with($permitTypeId)
            ->andReturn($stocks);

        $stocks[$stockAId]
            ->shouldReceive('getId')
            ->andReturn($stockAId);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByStockId')
            ->with($stockAId)
            ->andReturn($windows[$windowAId]);

        $windows[$windowAId]
            ->shouldReceive('getId')
            ->andReturn($windowAId);

        $this->repoMap['IrhpPermitApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpPermitApplication::class))
            ->once()
            ->andReturnUsing(
                function (IrhpPermitApplication $app) use (&$irhpPermitApplication) {
                    $irhpPermitApplication = $app;
                    $app->setId(125);
                }
            );

        $stocks[$stockBId]
            ->shouldReceive('getId')
            ->andReturn($stockBId);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByStockId')
            ->with($stockBId)
            ->andReturn($windows[$windowBId]);

        $windows[$windowBId]
            ->shouldReceive('getId')
            ->andReturn($windowBId);

        $irhpPermitApplication = null;

        $this->repoMap['IrhpPermitApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpPermitApplication::class))
            ->once()
            ->andReturnUsing(
                function (IrhpPermitApplication $app) use (&$irhpPermitApplication) {
                    $irhpPermitApplication = $app;
                    $app->setId(150);
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'irhpApplication' => 5,
                'irhpPermitApplication125' => 125,
                'irhpPermitApplication150' => 150,
            ],
            'messages' => [
                0 => 'IRHP Application created successfully',
                1 => 'IRHP Permit Application created successfully',
                2 => 'IRHP Permit Application created successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandEcmtApplication()
    {
        $permitTypeId = 3;
        $licenceId = 2;
        $stockId = 100;
        $windowId = 300;

        $cmdData = [
            'type' => $permitTypeId,
            'licence' => $licenceId,
        ];

        $command = CreateCmd::create($cmdData);

        $ecmtPermitType = $this->references[IrhpPermitType::class][3];

        $ecmtPermitType
            ->shouldReceive('getName')
            ->andReturn('permit_ecmt');

        $ecmtPermitType
            ->shouldReceive('getId')
            ->andReturn(3);

        $ecmtPermitApplication = null;

        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('save')
            ->with(m::type(EcmtPermitApplication::class))
            ->once()
            ->andReturnUsing(
                function (EcmtPermitApplication $app) use (&$ecmtPermitApplication) {
                    $ecmtPermitApplication = $app;
                    $app->setId(4);
                }
            );

        $stocks = [
            $this->references[IrhpPermitStock::class][$stockId]
        ];
        $windows = [
            $this->references[IrhpPermitWindow::class][$windowId]
        ];

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('getAllValidStockByPermitType')
            ->with($permitTypeId)
            ->andReturn($stocks);

        $stocks[0]
            ->shouldReceive('getId')
            ->andReturn($stockId);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByStockId')
            ->with($stockId)
            ->andReturn($windows[0]);

        $windows[0]
            ->shouldReceive('getId')
            ->andReturn($windowId);

        $this->repoMap['IrhpPermitApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpPermitApplication::class))
            ->once()
            ->andReturnUsing(
                function (IrhpPermitApplication $app) use (&$irhpPermitApplication) {
                    $irhpPermitApplication = $app;
                    $app->setId(125);
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'ecmtPermitApplication' => 4,
                'irhpPermitApplication125' => 125
            ],
            'messages' => [
                0 => 'ECMT Permit Application created successfully',
                1 => 'IRHP Permit Application created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNoPermitTypeFound()
    {
        $permitTypeId = 2000;
        $licenceId = 2;

        $cmdData = [
            'type' => $permitTypeId,
            'licence' => $licenceId,
        ];

        $command = CreateCmd::create($cmdData);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Permit type not found');

        $this->sut->handleCommand($command);
    }
}
