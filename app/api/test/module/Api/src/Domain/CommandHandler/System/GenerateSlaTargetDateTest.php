<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\System;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\System\GenerateSlaTargetDate as Command;
use Dvsa\Olcs\Api\Domain\CommandHandler\System\GenerateSlaTargetDate as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Pi as PiRepo;
use Dvsa\Olcs\Api\Domain\Repository\Sla as SlaRepo;
use Dvsa\Olcs\Api\Domain\Repository\SlaTargetDate as Repo;
use Dvsa\Olcs\Api\Domain\Repository\Submission as SubmissionRepo;
use Dvsa\Olcs\Api\Domain\Util\SlaCalculatorInterface;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Entity\System\Sla as SlaEntity;
use Dvsa\Olcs\Api\Entity\System\SlaTargetDate as SlaTargetDateEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * GenerateSlaTargetDate command handler test
 */
class GenerateSlaTargetDateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Pi', PiRepo::class);
        $this->mockRepo('Sla', SlaRepo::class);
        $this->mockRepo('SlaTargetDate', Repo::class);
        $this->mockRepo('Submission', SubmissionRepo::class);

        $this->mockedSmServices = [
            SlaCalculatorInterface::class => m::mock(SlaCalculatorInterface::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            TrafficAreaEntity::class => [
                TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE => m::mock(TrafficAreaEntity::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommandForPi()
    {
        $params = [
            'pi' => 111,
        ];
        $command = Command::create($params);

        $v1 = new \DateTime('2015-01-01');
        $v2 = new \DateTime('2015-02-02');

        /** @var PiEntity $piEntity */
        $piEntity = m::mock(PiEntity::class)->makePartial();
        $piEntity->setId($params['pi']);
        $piEntity->setSlaTargetDates(new ArrayCollection());
        $piEntity->shouldReceive('getCase->isTm')->once()->andReturn(true);
        $piEntity->shouldReceive('getField1')->andReturn($v1);
        $piEntity->shouldReceive('getField2')->andReturn($v2);
        $piEntity->shouldReceive('getField3')->andReturn(null);

        $this->repoMap['Pi']->shouldReceive('fetchById')
            ->once()
            ->with($params['pi'])
            ->andReturn($piEntity)
            ->shouldReceive('save')
            ->once();

        $sla1 = m::mock(SlaEntity::class)->makePartial();
        $sla1->setId(1);
        $sla1->setCompareTo('field1');
        $sla1->shouldReceive('appliesTo')->with($v1)->once()->andReturn(true);

        $sla2 = m::mock(SlaEntity::class)->makePartial();
        $sla2->setId(2);
        $sla2->setCompareTo('field2');
        $sla2->shouldReceive('appliesTo')->with($v2)->once()->andReturn(true);

        $sla3 = m::mock(SlaEntity::class)->makePartial();
        $sla3->setId(3);
        $sla3->setCompareTo('field3');
        $sla3->shouldReceive('appliesTo')->never();

        $slaTargetDate2 = m::mock(SlaTargetDateEntity::class)->makePartial();
        $slaTargetDate2->setSla($sla2);
        $slaTargetDate2->setAgreedDate(new \DateTime('2014-02-02'));
        $piEntity->getSlaTargetDates()->set($sla2->getId(), $slaTargetDate2);

        $slaTargetDate3 = m::mock(SlaTargetDateEntity::class)->makePartial();
        $slaTargetDate3->setSla($sla3);
        $slaTargetDate3->setAgreedDate(new \DateTime('2014-03-03'));
        $piEntity->getSlaTargetDates()->set($sla3->getId(), $slaTargetDate3);

        $v1Sla = new \DateTime('2016-01-01');
        $v2Sla = new \DateTime('2016-02-02');
        $this->mockedSmServices[SlaCalculatorInterface::class]
            ->shouldReceive('applySla')
            ->once()
            ->with($v1, $sla1, $this->references[TrafficAreaEntity::class][TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE])
            ->andReturn($v1Sla)
            ->shouldReceive('applySla')
            ->once()
            ->with($v2, $sla2, $this->references[TrafficAreaEntity::class][TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE])
            ->andReturn($v2Sla);

        $this->repoMap['Sla']->shouldReceive('fetchByCategories')
            ->once()
            ->with(['pi', 'pi_hearing'], Query::HYDRATE_OBJECT)
            ->andReturn([$sla1, $sla2, $sla3]);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['SLA Target Dates successfully saved'], $result->getMessages());

        $this->assertEquals(2, $piEntity->getSlaTargetDates()->count());

        $std1 = $piEntity->getSlaTargetDates()->get($sla1->getId());
        $this->assertEquals($sla1, $std1->getSla());
        $this->assertEquals($v1, $std1->getAgreedDate());
        $this->assertEquals($v1Sla, $std1->getTargetDate());

        $std2 = $piEntity->getSlaTargetDates()->get($sla2->getId());
        $this->assertEquals($sla2, $std2->getSla());
        $this->assertEquals($v2, $std2->getAgreedDate());
        $this->assertEquals($v2Sla, $std2->getTargetDate());
    }

    public function testHandleCommandForSubmission()
    {
        $params = [
            'submission' => 111,
        ];
        $command = Command::create($params);

        $v1 = new \DateTime('2015-01-01');
        $v2 = new \DateTime('2015-02-02');

        /** @var SubmissionEntity $submissionEntity */
        $submissionEntity = m::mock(SubmissionEntity::class)->makePartial();
        $submissionEntity->setId($params['submission']);
        $submissionEntity->setSlaTargetDates(new ArrayCollection());
        $submissionEntity->shouldReceive('getCase->isTm')->once()->andReturn(false);
        $submissionEntity->shouldReceive('getCase->getLicence->getTrafficArea')->once()
            ->andReturn($this->references[TrafficAreaEntity::class][TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE]);
        $submissionEntity->shouldReceive('getField1')->andReturn($v1);
        $submissionEntity->shouldReceive('getField2')->andReturn($v2);
        $submissionEntity->shouldReceive('getField3')->andReturn(null);

        $this->repoMap['Submission']->shouldReceive('fetchById')
            ->once()
            ->with($params['submission'])
            ->andReturn($submissionEntity)
            ->shouldReceive('save')
            ->once();

        $sla1 = m::mock(SlaEntity::class)->makePartial();
        $sla1->setId(1);
        $sla1->setCompareTo('field1');
        $sla1->shouldReceive('appliesTo')->with($v1)->once()->andReturn(true);

        $sla2 = m::mock(SlaEntity::class)->makePartial();
        $sla2->setId(2);
        $sla2->setCompareTo('field2');
        $sla2->shouldReceive('appliesTo')->with($v2)->once()->andReturn(true);

        $sla3 = m::mock(SlaEntity::class)->makePartial();
        $sla3->setId(3);
        $sla3->setCompareTo('field3');
        $sla3->shouldReceive('appliesTo')->never();

        $slaTargetDate2 = m::mock(SlaTargetDateEntity::class)->makePartial();
        $slaTargetDate2->setSla($sla2);
        $slaTargetDate2->setAgreedDate(new \DateTime('2014-02-02'));
        $submissionEntity->getSlaTargetDates()->set($sla2->getId(), $slaTargetDate2);

        $slaTargetDate3 = m::mock(SlaTargetDateEntity::class)->makePartial();
        $slaTargetDate3->setSla($sla3);
        $slaTargetDate3->setAgreedDate(new \DateTime('2014-03-03'));
        $submissionEntity->getSlaTargetDates()->set($sla3->getId(), $slaTargetDate3);

        $v1Sla = new \DateTime('2016-01-01');
        $v2Sla = new \DateTime('2016-02-02');
        $this->mockedSmServices[SlaCalculatorInterface::class]
            ->shouldReceive('applySla')
            ->once()
            ->with($v1, $sla1, $this->references[TrafficAreaEntity::class][TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE])
            ->andReturn($v1Sla)
            ->shouldReceive('applySla')
            ->once()
            ->with($v2, $sla2, $this->references[TrafficAreaEntity::class][TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE])
            ->andReturn($v2Sla);

        $this->repoMap['Sla']->shouldReceive('fetchByCategories')
            ->once()
            ->with(['submission'], Query::HYDRATE_OBJECT)
            ->andReturn([$sla1, $sla2, $sla3]);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(['SLA Target Dates successfully saved'], $result->getMessages());

        $this->assertEquals(2, $submissionEntity->getSlaTargetDates()->count());

        $std1 = $submissionEntity->getSlaTargetDates()->get($sla1->getId());
        $this->assertEquals($sla1, $std1->getSla());
        $this->assertEquals($v1, $std1->getAgreedDate());
        $this->assertEquals($v1Sla, $std1->getTargetDate());

        $std2 = $submissionEntity->getSlaTargetDates()->get($sla2->getId());
        $this->assertEquals($sla2, $std2->getSla());
        $this->assertEquals($v2, $std2->getAgreedDate());
        $this->assertEquals($v2Sla, $std2->getTargetDate());
    }
}
