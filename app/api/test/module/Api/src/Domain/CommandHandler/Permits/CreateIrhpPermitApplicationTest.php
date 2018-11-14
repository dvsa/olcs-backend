<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication as CreateIrhpPermitApplicationCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\CreateIrhpPermitApplication as CreateIrhpPermitApplicationHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Create Irhp Permit Application test
 */
class CreateIrhpPermitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateIrhpPermitApplicationHandler();
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplicationRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            EcmtPermitApplication::class => [
                200 => m::mock(EcmtPermitApplication::class)
                    ->shouldReceive('getLicence')
                    ->andReturn(
                        m::mock(Licence::class)
                    )
                    ->getMock(),
            ],
            IrhpPermitWindow::class => [
                100 => m::mock(IrhpPermitWindow::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $windowId = 100;
        $ecmtPermitApplicationId = 200;

        $cmdData = [
            'window' => $windowId,
            'ecmtPermitApplication' => $ecmtPermitApplicationId,
        ];

        $command = CreateIrhpPermitApplicationCmd::create($cmdData);

        $irhpPermitApplication = null;
        $this->repoMap['IrhpPermitApplication']
            ->shouldReceive('save')
            ->with(m::type(IrhpPermitApplication::class))
            ->once()
            ->andReturnUsing(
                function (IrhpPermitApplication $app) use (&$irhpPermitApplication) {
                    $irhpPermitApplication = $app;
                    $app->setId(300);
                }
            );

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(IrhpPermitApplication::class, $irhpPermitApplication);
        $this->assertSame(
            $this->references[IrhpPermitWindow::class][$windowId],
            $irhpPermitApplication->getIrhpPermitWindow()
        );
        $this->assertInstanceOf(Licence::class, $irhpPermitApplication->getLicence());
        $this->assertSame(
            $this->references[EcmtPermitApplication::class][$ecmtPermitApplicationId],
            $irhpPermitApplication->getEcmtPermitApplication()
        );

        $expected = [
            'id' => [
                'irhpPermitApplication' => 300,
            ],
            'messages' => ['IRHP Permit Application created successfully']
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
