<?php

/**
 * Grant Community Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\GrantCommunityLicence;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantCommunityLicence as GrantCommunityLicenceCmd;

/**
 * Grant Community Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantCommunityLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GrantCommunityLicence();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('CommunityLic', \Dvsa\Olcs\Api\Domain\Repository\CommunityLic::class);
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            CommunityLic::STATUS_ACTIVE,
            CommunityLic::STATUS_RETURNDED
        ];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];

        $command = GrantCommunityLicenceCmd::create($data);

        /** @var CommunityLic $pendingRecord */
        $pendingRecord = m::mock(CommunityLic::class)->makePartial();
        $pendingRecord->setId(123);

        $pendingRecords = [
            $pendingRecord
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->shouldReceive('canHaveCommunityLicences')
            ->andReturn(true);

        $licence->shouldReceive('getCommunityLics->matching')
            ->with(m::type(Criteria::class))
            ->andReturn($pendingRecords);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->repoMap['CommunityLic']->shouldReceive('save')
            ->once()
            ->with($pendingRecord);

        $data = [
            'isBatchReprint' => false,
            'licence' => 222,
            'communityLicenceIds' => [123],
            'identifier' => null
        ];
        $result1 = new Result();
        $result1->addMessage('GenerateBatch');
        $this->expectedSideEffect(GenerateBatch::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 community licence(s) activated',
                'GenerateBatch'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->refData[CommunityLic::STATUS_ACTIVE], $pendingRecord->getStatus());
        $this->assertEquals(date('Y-m-d'), $pendingRecord->getSpecifiedDate()->format('Y-m-d'));
    }

    public function testHandleCommandCantHave()
    {
        $data = [
            'id' => 111
        ];

        $command = GrantCommunityLicenceCmd::create($data);

        /** @var CommunityLic $pendingRecord */
        $pendingRecord = m::mock(CommunityLic::class)->makePartial();
        $pendingRecord->setId(123);

        $pendingRecords = new ArrayCollection();
        $pendingRecords->add($pendingRecord);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->shouldReceive('canHaveCommunityLicences')
            ->andReturn(false);

        $licence->shouldReceive('getCommunityLics->matching')
            ->with(m::type(Criteria::class))
            ->andReturn($pendingRecords);

        $licence->shouldReceive('setTotCommunityLicences')
            ->with(0)
            ->once();

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->repoMap['CommunityLic']->shouldReceive('save')
            ->once()
            ->with($pendingRecord);

        $this->repoMap['Licence']->shouldReceive('save')
            ->once()
            ->with($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Total community licence(s) count cleared',
                '1 community licence(s) voided'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame($this->refData[CommunityLic::STATUS_RETURNDED], $pendingRecord->getStatus());
        $this->assertEquals(date('Y-m-d'), $pendingRecord->getExpiredDate()->format('Y-m-d'));
    }
}
