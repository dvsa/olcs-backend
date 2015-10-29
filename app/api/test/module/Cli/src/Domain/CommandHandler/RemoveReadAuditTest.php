<?php

/**
 * Remove Read Audit Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Cli\Domain\CommandHandler\RemoveReadAudit;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Remove Read Audit Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RemoveReadAuditTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new RemoveReadAudit();
        $this->mockRepo('ApplicationReadAudit', Repository\ApplicationReadAudit::class);
        $this->mockRepo('LicenceReadAudit', Repository\LicenceReadAudit::class);
        $this->mockRepo('CasesReadAudit', Repository\CasesReadAudit::class);
        $this->mockRepo('BusRegReadAudit', Repository\BusRegReadAudit::class);
        $this->mockRepo('TransportManagerReadAudit', Repository\TransportManagerReadAudit::class);
        $this->mockRepo('OrganisationReadAudit', Repository\OrganisationReadAudit::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $this->repoMap['ApplicationReadAudit']->shouldReceive('deleteOlderThan')
            ->once()->with('2015-01-01')->andReturn(10);

        $this->repoMap['LicenceReadAudit']->shouldReceive('deleteOlderThan')
            ->once()->with('2015-01-01')->andReturn(10);

        $this->repoMap['CasesReadAudit']->shouldReceive('deleteOlderThan')
            ->once()->with('2015-01-01')->andReturn(10);

        $this->repoMap['BusRegReadAudit']->shouldReceive('deleteOlderThan')
            ->once()->with('2015-01-01')->andReturn(10);

        $this->repoMap['OrganisationReadAudit']->shouldReceive('deleteOlderThan')
            ->once()->with('2015-01-01')->andReturn(10);

        $this->repoMap['TransportManagerReadAudit']->shouldReceive('deleteOlderThan')
            ->once()->with('2015-01-01')->andReturn(10);

        $response = $this->sut->handleCommand(\Dvsa\Olcs\Cli\Domain\Command\RemoveReadAudit::create([]));

        $expected = [
            'id' => '',
            'messages' => [
                '10 ApplicationReadAudit records older than 2015-01-01 removed',
                '10 LicenceReadAudit records older than 2015-01-01 removed',
                '10 CasesReadAudit records older than 2015-01-01 removed',
                '10 BusRegReadAudit records older than 2015-01-01 removed',
                '10 TransportManagerReadAudit records older than 2015-01-01 removed',
                '10 OrganisationReadAudit records older than 2015-01-01 removed',
            ]
        ];

        $this->assertEquals($expected, $response);
    }
}
