<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\CreateEcmtPermitApplication as CreateEcmtPermitApplicationHandler;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Permits\CreateEcmtPermitApplication as CreateEcmtPermitApplicationCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Ecmt Permit Application test
 */
class CreateEcmtPermitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateEcmtPermitApplicationHandler();
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::SOURCE_SELFSERVE,
            EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED,
            EcmtPermitApplication::PERMIT_TYPE
        ];

        $this->references = [
            Licence::class => [
                100 => m::mock(Licence::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceId = 100;
        $stockId = 200;
        $windowId = 300;
        $ecmtPermitApplicationId = 400;

        $cmdData = [
            'licence' => $licenceId,
        ];

        $command = CreateEcmtPermitApplicationCmd::create($cmdData);

        $ecmtPermitApplication = null;
        $this->repoMap['EcmtPermitApplication']
            ->shouldReceive('save')
            ->with(m::type(EcmtPermitApplication::class))
            ->once()
            ->andReturnUsing(
                function (EcmtPermitApplication $app) use (&$ecmtPermitApplication, $ecmtPermitApplicationId) {
                    $ecmtPermitApplication = $app;
                    $app->setId($ecmtPermitApplicationId);
                }
            );

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('getId')
            ->andReturn($stockId);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('getNextIrhpPermitStockByPermitType')
            ->with(EcmtPermitApplication::PERMIT_TYPE, m::type(DateTime::class))
            ->once()
            ->andReturn($stock);

        $window = m::mock(IrhpPermitWindow::class);
        $window->shouldReceive('getId')
            ->andReturn($windowId);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByStockId')
            ->with($stockId)
            ->once()
            ->andReturn($window);

        $this->expectedSideEffect(
            CreateIrhpPermitApplication::class,
            [
                'window' => $windowId,
                'ecmtPermitApplication' => $ecmtPermitApplicationId,
            ],
            (new Result())->addMessage('IRHP Permit Application created')
        );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(EcmtPermitApplication::class, $ecmtPermitApplication);
        $this->assertEquals(
            EcmtPermitApplication::SOURCE_SELFSERVE,
            $ecmtPermitApplication->getSource()->getId()
        );
        $this->assertEquals(
            EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED,
            $ecmtPermitApplication->getStatus()->getId()
        );
        $this->assertEquals(
            EcmtPermitApplication::PERMIT_TYPE,
            $ecmtPermitApplication->getPermitType()->getId()
        );
        $this->assertSame(
            $this->references[Licence::class][$licenceId],
            $ecmtPermitApplication->getLicence()
        );
        $this->assertInstanceOf(\DateTime::class, $ecmtPermitApplication->getDateReceived());

        $expected = [
            'id' => [
                'ecmtPermitApplication' => $ecmtPermitApplicationId,
            ],
            'messages' => [
                'ECMT Permit Application created successfully',
                'IRHP Permit Application created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
