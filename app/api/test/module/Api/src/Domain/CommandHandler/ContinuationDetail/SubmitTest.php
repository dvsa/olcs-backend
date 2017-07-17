<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ContinuationDetail;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail\Submit as CommandHandler;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\Submit as UpdateCommand;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;

/**
 * SubmitTest
 */
class SubmitTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            RefData::SIG_PHYSICAL_SIGNATURE,
            RefData::SIG_DIGITAL_SIGNATURE,
        ];

        parent::initReferences();
    }

    public function testHandleCommandPhysicalSignature()
    {
        $data = [
            'id' => 154,
            'version' => 7,
        ];
        $command = UpdateCommand::create($data);

        $continuationDetail = new ContinuationDetailEntity();
        $continuationDetail->setId(154);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchById')->with(154, Query::HYDRATE_OBJECT, 7)->once()
            ->andReturn($continuationDetail);

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($this->refData[RefData::SIG_PHYSICAL_SIGNATURE], $continuationDetail->getSignatureType());
        $this->assertTrue($continuationDetail->getIsDigital());

        $this->assertEquals(['ContinuationDetail submitted'], $result->getMessages());
        $this->assertEquals(['continuationDetail' => 154], $result->getIds());
    }

    public function testHandleCommandDigitalSignature()
    {
        $data = [
            'id' => 154,
            'version' => 7,
        ];
        $command = UpdateCommand::create($data);

        $continuationDetail = new ContinuationDetailEntity();
        $continuationDetail->setId(154);
        $continuationDetail->setSignatureType($this->refData[RefData::SIG_DIGITAL_SIGNATURE]);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchById')->with(154, Query::HYDRATE_OBJECT, 7)->once()
            ->andReturn($continuationDetail);

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($this->refData[RefData::SIG_DIGITAL_SIGNATURE], $continuationDetail->getSignatureType());
        $this->assertTrue($continuationDetail->getIsDigital());

        $this->assertEquals(['ContinuationDetail submitted'], $result->getMessages());
        $this->assertEquals(['continuationDetail' => 154], $result->getIds());
    }
}
