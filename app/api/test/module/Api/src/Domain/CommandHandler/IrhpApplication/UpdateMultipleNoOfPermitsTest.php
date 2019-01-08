<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\UpdateMultipleNoOfPermits;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class UpdateMultipleNoOfPermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplicationRepo::class);
        $this->sut = new UpdateMultipleNoOfPermits();
     
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $commandId = 44;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('resetCheckAnswersAndDeclaration')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($commandId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('saveOnFlush')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2019->shouldReceive('updatePermitsRequired')
            ->with(4)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2019)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationEs2020 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationEs2020->shouldReceive('updatePermitsRequired')
            ->with(0)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationEs2020)
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationFr2019 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationFr2019->shouldReceive('updatePermitsRequired')
            ->with(6)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationFr2019)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('flushAll')
            ->once()
            ->ordered()
            ->globally();

        $irhpPermitApplicationFr2020 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplicationFr2020->shouldReceive('updatePermitsRequired')
            ->never();

        $this->repoMap['IrhpPermitApplication']->shouldReceive('saveOnFlush')
            ->with($irhpPermitApplicationFr2020)
            ->never();

        $queryResponse = [
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2019,
                'validTo' => '2019-12-31',
                'countryId' => 'ES',
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationEs2020,
                'validTo' => '2020-12-31',
                'countryId' => 'ES',
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationFr2019,
                'validTo' => '2019-08-15',
                'countryId' => 'FR',
            ],
            [
                'irhpPermitApplication' => $irhpPermitApplicationFr2020,
                'validTo' => '2020-10-01',
                'countryId' => 'FR',
            ],
        ];

        $this->repoMap['IrhpPermitApplication']->shouldReceive('getByIrhpApplicationWithStockInfo')
            ->andReturn($queryResponse);


        $commandPermitsRequired = [
            'ES' => [
                2019 => 4,
                2020 => 0
            ],
            'FR' => [
                2019 => 6,
                2020 => -4
            ],
            'GR' => [
                2020 => 7
            ],
            'randominput' => 'bob',
            'junk' => [
                'bar',
                'bar2'
            ]
        ];

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($commandId);
        $command->shouldReceive('getPermitsRequired')
            ->andReturn($commandPermitsRequired);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($commandId, $result->getId('irhpApplication'));
        $this->assertEquals(
            [
                'Updated 3 of 4 required permit counts for IRHP application'
            ],
            $result->getMessages()
        );
    }
}
