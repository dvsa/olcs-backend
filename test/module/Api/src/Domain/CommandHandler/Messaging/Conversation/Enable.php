<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Messaging\Conversation;

use Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Conversation\Enable as EnableCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\Messaging\Conversation\Enable as EnableCommand;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class Enable extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new EnableCommandHandler();
        $this->mockRepo(OrganisationRepo::class, OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleCommand(): void
    {
        $mockCommand = EnableCommand::create(['organisation' => 1]);

        $mockOrganisation = m::mock(Organisation::class);
        $mockOrganisation->shouldReceive('setIsMessagingDisabled')
                         ->once()
                         ->with(false);
        $mockOrganisation->shouldReceive('getId')
                         ->once()
                         ->andReturn(1);

        $this->repoMap[OrganisationRepo::class]
            ->shouldReceive('fetchById')
            ->once()
            ->with(1)
            ->andReturn($mockOrganisation);
        $this->repoMap[OrganisationRepo::class]
            ->shouldReceive('save')
            ->once()
            ->with($mockOrganisation);

        $result = $this->sut->handleCommand($mockCommand);

        $this->assertEquals(1, $result->getId('organisation'));
    }
}
