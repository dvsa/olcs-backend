<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdatePermitFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\CreateFullPermitApplication;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
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
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::SOURCE_INTERNAL,
            EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED,
            EcmtPermitApplication::PERMIT_TYPE
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceId = 100;
        $windowId = 300;
        $ecmtPermitApplicationId = 400;
        $totalPermitsRequired = 500;
        $dateReceived = '2018-11-10';

        $cmdData = [
            'licence' => $licenceId,
            'countryIds' => [],
            'requiredEuro5' => 200,
            'requiredEuro6' => 300,
            'dateReceived' => $dateReceived,
            'year' => 3030
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

        $window = m::mock(IrhpPermitWindow::class);
        $window->shouldReceive('getId')
            ->andReturn($windowId);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByIrhpPermitType')
            ->with(
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
                m::type(DateTime::class),
                Query::HYDRATE_OBJECT,
                $command->getYear()
            )
            ->once()
            ->andReturn($window);

        $this->expectedSideEffect(
            UpdatePermitFee::class,
            [
                'ecmtPermitApplicationId' => $ecmtPermitApplicationId,
                'licenceId' => $licenceId,
                'permitsRequired' => $totalPermitsRequired,
                'permitType' =>  EcmtPermitApplication::PERMIT_TYPE,
                'receivedDate' =>  new DateTime($dateReceived)
            ],
            (new Result())->addMessage('Permit Fee updated')
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
            IrhpInterface::SOURCE_INTERNAL,
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
                'Permit Fee updated',
                'ECMT Permit Application created successfully',
                'IRHP Permit Application created',
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
