<?php

/**
 * VoidAllCommunityLicencesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\VoidAllCommunityLicences as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Licence\VoidAllCommunityLicences as Command;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences as UpdateTotalCommunityLicencesCommand;

/**
 * VoidAllCommunityLicencesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 *
 */
class VoidAllCommunityLicencesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('CommunityLic', CommunityLicRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 717]);
        $this->repoMap['CommunityLic']
            ->shouldReceive('expireAllForLicence')
            ->with(717, CommunityLicEntity::STATUS_VOID)
            ->once();

        $this->expectedSideEffect(
            UpdateTotalCommunityLicencesCommand::class,
            ['id' => 717],
            new Result()
        );

        $this->sut->handleCommand($command);
    }
}
