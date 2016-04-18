<?php

/**
 * CpidOrganisationExportTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\Queue\Complete;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\User;

/**
 * Class CpidOrganisationExportTest
 * @package Dvsa\OlcsTest\Cli\Service\Queue\Consumer
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CpidOrganisationExportTest extends MockeryTestCase
{
    protected $queueEntity = null;

    public function setUp()
    {
        $user = new User('pid', 'type');
        $user->setId(1);
        $queueEntity = new Queue(new RefData(Queue::TYPE_CPID_EXPORT_CSV));
        $queueEntity->setStatus(Queue::STATUS_QUEUED);
        $queueEntity->setCreatedBy($user);

        $this->queueEntity = $queueEntity;
    }

    /**
     * @dataProvider processMessageProvider
     */
    public function testProcessMessage($shouldThrowException, $message)
    {
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

        $commandHandlerManager = m::mock(CommandHandlerManager::class);

        if ($shouldThrowException) {
            $commandHandlerManager->shouldReceive('handleCommand')
                ->once()
                ->with(m::type(Upload::class))
                ->andThrow(new \Exception('AN EXCEPTION'));

            $commandHandlerManager->shouldReceive('handleCommand')
                ->once()
                ->with(m::type(Failed::class));
        } else {
            $commandHandlerManager->shouldReceive('handleCommand')
                ->once()
                ->with(m::type(Upload::class));

            $commandHandlerManager->shouldReceive('handleCommand')
                ->once()
                ->with(m::type(Complete::class));
        }

        $cpidOrganisationExport = new CpidOrganisationExport($organisation, $commandHandlerManager);

        $this->queueEntity->setStatus(Queue::STATUS_QUEUED);
        $this->queueEntity->setOptions(json_encode(['status' => null]));

        $this->assertEquals(
            $cpidOrganisationExport->processMessage($this->queueEntity),
            $message
        );
    }

    public function processMessageProvider()
    {
        return [
            [
                false,
                'Successfully processed message:  {"status":null} Organisation list exported.'
            ],
            [
                true,
                'Failed to process message:  {"status":null} Unable to export list. AN EXCEPTION'
            ]
        ];
    }
}
