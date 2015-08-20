<?php

/**
 * CpidOrganisationExportTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Mockery as m;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\LockHandler;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\File\FileUploaderInterface;

/**
 * Class CpidOrganisationExportTest
 * @package Dvsa\OlcsTest\Cli\Service\Queue\Consumer
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CpidOrganisationExportTest extends m\Adapter\Phpunit\MockeryTestCase
{
    protected $queueEntity = null;

    public function setUp()
    {
        $queueEntity = new Queue(new RefData(Queue::TYPE_CPID_EXPORT_CSV));
        $queueEntity->setStatus(Queue::STATUS_QUEUED);

        $this->queueEntity = $queueEntity;
    }

    /**
     * @dataProvider testProcessMessageProvider
     * @param $response
     */
    public function testProcessMessageSuccess($response, $message)
    {
        $path = '/tmp';
        $organisation = m::mock(Organisation::class)
            ->shouldReceive('fetchAllByStatusForCpidExport')
            ->with(null)
            ->andReturn(
                m::mock(IterableResult::class)
                    ->makePartial()
                    ->shouldReceive('next')
                    ->twice()
                    ->andReturn([1 => [1, 2, 3]], false)
                    ->getMock()
            )
            ->getMock();
        $fileUploader = m::mock(FileUploaderInterface::class)
            ->shouldReceive('setFile')
            ->andReturnSelf()
            ->shouldReceive('upload')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getIdentifier')
                    ->getMock()
            )
            ->getMock();
        $commandHandlerManager = m::mock(CommandHandlerManager::class)
            ->shouldReceive('handleCommand')
            ->andReturn($response)
            ->getMock();
        $fileSystem = m::mock(Filesystem::class)->makePartial();
        $lockHandler = m::mock(LockHandler::class)->makePartial();

        $cpidOrganisationExport = new CpidOrganisationExport(
            $path,
            $organisation,
            $commandHandlerManager,
            $fileUploader,
            $fileSystem,
            $lockHandler
        );

        $this->queueEntity->setStatus(Queue::STATUS_QUEUED);
        $this->queueEntity->setOptions(json_encode(['status' => null]));

        $this->assertEquals(
            $cpidOrganisationExport->processMessage($this->queueEntity),
            $message
        );
    }

    public function testProcessMessageProvider()
    {
        return [
            [
                true,
                'Successfully processed message:  {"status":null} Organisation list exported.'
            ],
            [
                false,
                'Failed to process message:  {"status":null} Unable to export list.'
            ]
        ];
    }
}
