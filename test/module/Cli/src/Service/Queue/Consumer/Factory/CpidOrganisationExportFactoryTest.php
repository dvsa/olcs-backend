<?php

/**
 * CpidOrganisationExportFactoryTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use OlcsTest\Bootstrap;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory\CpidOrganisationExportFactory;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;

/**
 * Message Consumer Manager Factory Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CpidOrganisationExportFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new CpidOrganisationExportFactory();

        $this->sm = Bootstrap::getServiceManager();

        $this->sl = $this->sm;
    }

    public function testCreateService()
    {
        // Params
        $config = [
            'file-system' => [
                'path' => '/tmp'
            ]
        ];

        $this->sm->shouldReceive('getServiceLocator')->andReturn($this->sm);

        // Mocks
        $this->sm->setService('Config', $config);
        $this->sm->setService(
            'RepositoryServiceManager',
            m::mock()
                ->shouldReceive('get')
                ->with('Organisation')
                ->andReturn(m::mock(Organisation::class))
                ->getMock()
        );
        $this->sm->setService('CommandHandlerManager', m::mock(CommandHandlerManager::class));
        $this->sm->setService('FileUploader', m::mock(FileUploaderInterface::class));

        $cpidExportFactory = $this->sut->createService($this->sm);
    }
}
