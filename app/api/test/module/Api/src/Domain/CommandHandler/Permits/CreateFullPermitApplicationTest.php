<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdatePermitFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\CreateFullPermitApplication;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Permits\CreateFullPermitApplication as CreateFullPermitApplicationCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Full Permit Application test
 */
class CreateFullPermitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateFullPermitApplication();
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::SOURCE_INTERNAL,
            EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED,
            EcmtPermitApplication::PERMIT_TYPE
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceId = 100;
        $stockId = 200;
        $windowId = 300;
        $ecmtPermitApplicationId = 400;
        $permitsRequired = 500;
        $dateReceived = '2018-11-10';

        $cmdData = [
            'licence' => $licenceId,
            'countryIds' => [],
            'permitsRequired' => $permitsRequired,
            'dateReceived' => $dateReceived
        ];

        $command = CreateFullPermitApplicationCmd::create($cmdData);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('canMakeEcmtApplication')->once()->withNoArgs()->andReturn(true);

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

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->once()
            ->with($licenceId)
            ->andReturn($licence);

        $stock = m::mock(IrhpPermitStock::class);
        $stock->shouldReceive('getId')
            ->andReturn($stockId);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('getNextIrhpPermitStockByPermitType')
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
            UpdatePermitFee::class,
            [
                'ecmtPermitApplicationId' => $ecmtPermitApplicationId,
                'licenceId' => $licenceId,
                'permitsRequired' => $permitsRequired,
                'permitType' =>  EcmtPermitApplication::PERMIT_TYPE,
                'receivedDate' =>  new DateTime($dateReceived)
            ],
            (new Result())->addMessage('IRHP Permit Application created')
        );

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
            EcmtPermitApplication::SOURCE_INTERNAL,
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
        $this->assertEquals(
            $licence,
            $ecmtPermitApplication->getLicence()
        );
        $this->assertInstanceOf(\DateTime::class, $ecmtPermitApplication->getDateReceived());

        $expected = [
            'id' => [
                'ecmtPermitApplication' => $ecmtPermitApplicationId,
            ],
            'messages' => [
                'ECMT Permit Application created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandForbidden()
    {
        $licenceId = 200;
        $licNo = 'OB1234567';
        $command = CreateFullPermitApplicationCmd::create(['licence' => $licenceId]);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('canMakeEcmtApplication')->once()->withNoArgs()->andReturn(false);
        $licence->shouldReceive('getId')->once()->withNoArgs()->andReturn($licenceId);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($licNo);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->once()
            ->andReturn($licence);

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Licence ID ' . $licenceId . ' with number ' . $licNo . ' is unable to make an ECMT application');

        $this->sut->handleCommand($command);
    }
}
