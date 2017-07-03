<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\TransportManagerLicence;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerLicence\Delete as DeleteHandler;
use Dvsa\Olcs\Transfer\Command\TransportManagerLicence\Delete as DeleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TmlRepo;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as TmlEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Mockery as m;

/**
 * DeleteTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class DeleteTest extends CommandHandlerTestCase
{
    /**
     * set up
     */
    public function setUp()
    {
        $this->sut = new DeleteHandler();
        $this->mockRepo('TransportManagerLicence', TmlRepo::class);

        parent::setUp();
    }

    /**
     * test handleCommand
     */
    public function testHandleCommand()
    {
        $licenceId1 = 111;
        $licenceId2 = 222;
        $tmId1 = 333;
        $tmId2 = 444;
        $tmlId1 = 555;
        $tmlId2 = 666;

        $command = DeleteCmd::create(['ids' => [$tmlId1, $tmlId2]]);

        $licenceEntity1 = m::mock(LicenceEntity::class);
        $licenceEntity1->shouldReceive('getTmLicences->count')->once()->withNoArgs()->andReturn(2);
        $licenceEntity1->shouldReceive('getId')->once()->withNoArgs()->andReturn($licenceId1);

        $licenceEntity2 = m::mock(LicenceEntity::class);
        $licenceEntity2->shouldReceive('getTmLicences->count')->once()->withNoArgs()->andReturn(1);
        $licenceEntity2->shouldReceive('getId')->once()->withNoArgs()->andReturn($licenceId2);

        $tmlEntity1 = m::mock(TmlEntity::class);
        $tmlEntity1->shouldReceive('getLicence')->once()->withNoArgs()->andReturn($licenceEntity1);
        $tmlEntity1->shouldReceive('getTransportManager->getId')->once()->withNoArgs()->andReturn($tmId1);

        $tmlEntity2 = m::mock(TmlEntity::class);
        $tmlEntity2->shouldReceive('getLicence')->once()->withNoArgs()->andReturn($licenceEntity2);
        $tmlEntity2->shouldReceive('getTransportManager->getId')->once()->withNoArgs()->andReturn($tmId2);

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchById')
            ->with($tmlId1)
            ->once()
            ->andReturn($tmlEntity1);
        $this->repoMap['TransportManagerLicence']->shouldReceive('delete')->with($tmlEntity1)->once();

        $this->repoMap['TransportManagerLicence']
            ->shouldReceive('fetchById')
            ->with($tmlId2)
            ->once()
            ->andReturn($tmlEntity2);
        $this->repoMap['TransportManagerLicence']->shouldReceive('delete')->with($tmlEntity2)->once();

        //tm 1 wasn't the last on the licence
        $task1Params = $this->createTaskParams(TmlEntity::DESC_TM_REMOVED, $licenceId1, $tmId1, 'N');
        $this->expectedSideEffect(CreateTaskCmd::class, $task1Params, new Result());

        //tm 2 was the last tm on the licence
        $task2Params = $this->createTaskParams(TmlEntity::DESC_TM_REMOVED_LAST, $licenceId2, $tmId2, 'Y');
        $this->expectedSideEffect(CreateTaskCmd::class, $task2Params, new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'Transport manager licence ' . $tmlId1 . ' deleted',
                'Transport manager licence ' . $tmlId2 . ' deleted'
            ],
            $result->getMessages()
        );
    }

    /**
     * creates params for task side effect
     *
     * @param $desc
     * @param $licenceId
     * @param $tmId
     * @param $urgent
     *
     * @return array
     */
    private function createTaskParams($desc, $licenceId, $tmId, $urgent)
    {
        return [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => SubCategory::TM_SUB_CATEGORY_TM1_REMOVAL,
            'description' => $desc,
            'licence' => $licenceId,
            'transportManager' => $tmId,
            'urgent' => $urgent
        ];
    }
}
