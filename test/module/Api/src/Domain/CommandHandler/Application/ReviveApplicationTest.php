<?php

/**
 * ReviveApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Licence\Grant;
use Dvsa\Olcs\Api\Domain\Command\Licence\UnderConsideration;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\ReviveApplication as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Application\ReviveApplication as Command;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\MocksAbstractCommandHandlerServicesTrait;
use Dvsa\OlcsTest\MocksServicesTrait;
use Mockery as m;

/**
 * Revive Application Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class ReviveApplicationTest extends CommandHandlerTestCase
{
    use MocksServicesTrait;
    use MocksAbstractCommandHandlerServicesTrait;

    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', ApplicationRepo::class);

        $this->mockedSmServices = [
            CacheEncryption::class => m::mock(CacheEncryption::class),
        ];

        parent::setUp();
    }

    public function testHandleCommandWithNtu()
    {
        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->once()
            ->andReturn(Application::APPLICATION_STATUS_NOT_TAKEN_UP);

        $command = Command::create(['id' => 532]);

        $application = m::mock(Application::class)->makePartial();
        $application->setId(1);

        $mockLicence = m::mock(LicenceEntity::class);
        $mockLicence->expects('getId')->andReturn(123);

        $application->setLicence($mockLicence);

        $application->shouldReceive('getStatus')
            ->andReturn(
                $status->getMock()
            );

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(532)
            ->andReturn($application);

        $this->repoMap['Application']->shouldReceive('save')
            ->once()
            ->with(m::type(Application::class));

        $grantResult = new Result();
        $this->expectedSideEffect(Grant::class, ['id' => 123], $grantResult);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Application 1 has been revived"], $result->getMessages());
    }

    public function testHandleCommandWithWithdrawn()
    {
        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->once()
            ->andReturn(Application::APPLICATION_STATUS_WITHDRAWN);

        $command = Command::create(['id' => 532]);

        $application = m::mock(Application::class)->makePartial();
        $application->setId(1);

        $mockLicence = m::mock(LicenceEntity::class);
        $mockLicence->expects('getId')->andReturn(123);
        $application->setLicence($mockLicence);

        $application->shouldReceive('getStatus')
            ->andReturn(
                $status->getMock()
            );

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(532)
            ->andReturn($application);

        $this->repoMap['Application']->shouldReceive('save')
            ->once()
            ->with(m::type(Application::class));

        $considerationResult = new Result();
        $this->expectedSideEffect(UnderConsideration::class, ['id' => 123], $considerationResult);

        $this->expectedLicenceCacheClear($mockLicence);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Application 1 has been revived"], $result->getMessages());
    }
}
