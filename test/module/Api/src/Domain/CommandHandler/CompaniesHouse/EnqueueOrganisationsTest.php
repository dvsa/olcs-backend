<?php

/**
 * Companies House Enqueue Organisations Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\EnqueueOrganisations as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\CompaniesHouse\EnqueueOrganisations;
use Dvsa\Olcs\Api\Domain\Repository\Queue as Repo;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 *  Companies House Enqueue Organisations Command Handler Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class EnqueueOrganisationsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new EnqueueOrganisations();
        $this->mockRepo('Queue', Repo::class);

        parent::setUp();
    }

    /**
     * Test handleCommand method
     */
    public function testHandleCommand()
    {
        // expectations

        $command = Cmd::create([]);

        $this->repoMap['Queue']
            ->shouldReceive('enqueueAllOrganisations')
            ->with(Queue::TYPE_COMPANIES_HOUSE_COMPARE)
            ->once();

        $this->sut->handleCommand($command);
    }
}
