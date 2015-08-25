<?php

/**
 * CpidOrganisationExportTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace module\Api\src\Domain\CommandHandler\Organisation;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\CpidOrganisationExport;
use Dvsa\Olcs\Transfer\Command\Organisation\CpidOrganisationExport as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Queue as QueueRepo;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * Class CpidOrganisationExportTest
 * 
 * @package module\Api\src\Domain\CommandHandler\Organisation
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CpidOrganisationExportTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CpidOrganisationExport();
        $this->mockRepo('Queue', QueueRepo::class);

        parent::setUp();
    }

    public function initReferences()
    {
        $this->refData = [
            Queue::TYPE_CPID_EXPORT_CSV,
            Queue::STATUS_QUEUED
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'cpid' => null
        ];

        $command = Cmd::create($data);

        $this->repoMap['Queue']
            ->shouldReceive('save')
            ->once();

        $this->sut->handleCommand($command);
    }
}
