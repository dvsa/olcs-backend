<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdatePermitFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\CreateEcmtPermitApplication as CreateEcmtPermitApplicationHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\Country as CountryRepo;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
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
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('Country', CountryRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::SOURCE_SELFSERVE,
            EcmtPermitApplication::SOURCE_INTERNAL,
            EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED,
            EcmtPermitApplication::PERMIT_TYPE
        ];

        parent::initReferences();
    }

    /**
     * @dataProvider dpTestHandleCommand
     */
    public function testHandleCommand($cmdData, $expectedSource, $expected)
    {
        $windowId = 300;
        $ecmtPermitApplicationId = 400;

        $command = CreateEcmtPermitApplicationCmd::create($cmdData);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('canMakeEcmtApplication')->once()->withNoArgs()->andReturn(true);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($cmdData['licence'])
            ->once()
            ->andReturn($licence);

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

        $window = m::mock(IrhpPermitWindow::class);
        $window->shouldReceive('getId')
            ->andReturn($windowId);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchLastOpenWindowByStockId')
            ->with(
                $command->getIrhpPermitStock()
            )
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

        if (isset($cmdData['requiredEuro5'])) {
            $this->expectedSideEffect(
                UpdatePermitFee::class,
                [],
                (new Result())->addMessage('Fee Side Effect Complete')
            );
        }

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(EcmtPermitApplication::class, $ecmtPermitApplication);
        $this->assertEquals(
            $expectedSource,
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
            $licence,
            $ecmtPermitApplication->getLicence()
        );

        $this->assertInstanceOf(\DateTime::class, $ecmtPermitApplication->getDateReceived());



        $this->assertEquals($expected, $result->toArray());
    }

    public function dpTestHandleCommand()
    {
        $ssCmdData = [
            'licence' => 100,
            'irhpPermitStock' => 2,
            'fromInternal' => 0,
        ];

        $intCmdData = [
            'licence' => 100,
            'irhpPermitStock' => 2,
            'fromInternal' => 1,
            'countryIds' => ['HU', 'AT'],
            'dateReceived' => '2018-01-01',
            'requiredEuro5' => 2,
            'requiredEuro6' => 4
        ];

        $ssExpected = [
            'id' => [
                'ecmtPermitApplication' => 400,
            ],
            'messages' => [
                'ECMT Permit Application created successfully',
                'IRHP Permit Application created',
            ]
        ];

        $intExpected = [
            'id' => [
                'ecmtPermitApplication' => 400,
            ],
            'messages' => [
                'Fee Side Effect Complete',
                'ECMT Permit Application created successfully',
                'IRHP Permit Application created',
            ]
        ];

        return [
            [$ssCmdData, EcmtPermitApplication::SOURCE_SELFSERVE, $ssExpected],
            [$intCmdData, EcmtPermitApplication::SOURCE_INTERNAL, $intExpected]
        ];
    }

    public function testHandleCommandForbidden()
    {
        $licenceId = 200;
        $licNo = 'OB1234567';
        $command = CreateEcmtPermitApplicationCmd::create(['licence' => $licenceId]);

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
