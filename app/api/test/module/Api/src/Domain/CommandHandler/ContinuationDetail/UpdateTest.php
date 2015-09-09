<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\Update as CommandHandler;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Update as UpdateCommand;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;

/**
 * UpdateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['STATUS'];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 154,
            'version' => 43,
            'status' => 'STATUS',
            'received' => 'Y',
            'totAuthVehicles' => '213',
            'totAuthSmallVehicles' => 200,
            'totAuthMediumVehicles' => 10,
            'totAuthLargeVehicles' => 3,
            'totPsvDiscs' => '12',
            'totCommunityLicences' => '43',
        ];
        $command = UpdateCommand::create($data);

        $continuationDetail = new ContinuationDetailEntity();
        $continuationDetail->setId(154);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchById')->with(154, Query::HYDRATE_OBJECT, 43)->once()
            ->andReturn($continuationDetail);

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once()->andReturnUsing(
            function (ContinuationDetailEntity $saveContinuationDetail) use ($data) {
                $this->assertSame($this->refData[$data['status']], $saveContinuationDetail->getStatus());
                $this->assertSame($data['received'], $saveContinuationDetail->getReceived());
                $this->assertSame($data['totAuthVehicles'], $saveContinuationDetail->getTotAuthVehicles());
                $this->assertSame($data['totAuthSmallVehicles'], $saveContinuationDetail->getTotAuthSmallVehicles());
                $this->assertSame($data['totAuthMediumVehicles'], $saveContinuationDetail->getTotAuthMediumVehicles());
                $this->assertSame($data['totAuthLargeVehicles'], $saveContinuationDetail->getTotAuthLargeVehicles());
                $this->assertSame($data['totPsvDiscs'], $saveContinuationDetail->getTotPsvDiscs());
                $this->assertSame($data['totCommunityLicences'], $saveContinuationDetail->getTotCommunityLicences());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['ContinuationDetail updated'], $result->getMessages());
        $this->assertEquals(['continuationDetail' => 154], $result->getIds());
    }
}
