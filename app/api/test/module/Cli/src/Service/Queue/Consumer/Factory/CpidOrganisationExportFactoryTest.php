<?php

/**
 * CpidOrganisationExportFactoryTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory\CpidOrganisationExportFactory;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;

/**
 * Message Consumer Manager Factory Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CpidOrganisationExportFactoryTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sut = new CpidOrganisationExportFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        $this->sm->shouldReceive('getServiceLocator')->andReturnSelf();

        // Mocks
        $this->sm->setService(
            'RepositoryServiceManager',
            m::mock()
                ->shouldReceive('get')
                ->once()
                ->with('Organisation')
                ->andReturn(m::mock(Organisation::class))
                ->getMock()
        );
        $this->sm->setService('CommandHandlerManager', m::mock(CommandHandlerManager::class));

        $cpidExportFactory = $this->sut->createService($this->sm);

        $this->assertInstanceOf(CpidOrganisationExport::class, $cpidExportFactory);
    }
}
