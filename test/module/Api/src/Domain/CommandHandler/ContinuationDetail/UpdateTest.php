<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\Update as CommandHandler;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Update as Command;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;

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
            'totPsvDiscs' => '12',
            'totCommunityLicences' => '43',
        ];
        $command = Command::create($data);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setId(154);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchById')->with(154, Query::HYDRATE_OBJECT, 43)->once()
            ->andReturn($continuationDetail);

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once()->andReturnUsing(
            function (ContinuationDetail $saveContinuationDetail) use ($data) {
                $this->assertSame($this->refData[$data['status']], $saveContinuationDetail->getStatus());
                $this->assertSame($data['received'], $saveContinuationDetail->getReceived());
                $this->assertSame($data['totAuthVehicles'], $saveContinuationDetail->getTotAuthVehicles());
                $this->assertSame($data['totPsvDiscs'], $saveContinuationDetail->getTotPsvDiscs());
                $this->assertSame($data['totCommunityLicences'], $saveContinuationDetail->getTotCommunityLicences());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['ContinuationDetail updated'], $result->getMessages());
        $this->assertEquals(['continuationDetail' => 154], $result->getIds());
    }
}
