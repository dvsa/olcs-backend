<?php

namespace Dvsa\OlcsTest\Api\Entity\OperatingCentre;

use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as Entity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition as OppositionEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Mockery as m;

/**
 * OperatingCentre Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class OperatingCentreEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetHasEnvironmentalComplaint()
    {
        $complaint = $this->createPartialMock(
            ComplaintEntity::class,
            ['getClosedDate', 'isEnvironmentalComplaint']
        );

        $complaint->expects($this->once())
            ->method('getClosedDate')
            ->will(
                $this->returnValue(null)
            );

        $complaint->expects($this->once())
            ->method('isEnvironmentalComplaint')
            ->will($this->returnValue(true));

        $complaints = [];
        $complaints[] = $complaint;

        $entity = new Entity();
        $entity->setComplaints(new ArrayCollection($complaints));

        $this->assertEquals('Y', $entity->getHasEnvironmentalComplaint());
    }

    public function testGetHasEnvironmentalComplaintNot()
    {
        $complaint = $this->createPartialMock(
            ComplaintEntity::class,
            ['getClosedDate', 'isEnvironmentalComplaint']
        );

        $complaint->expects($this->once())
                  ->method('getClosedDate')
                  ->will($this->returnValue(date('d-m-Y')));

        $complaint->expects($this->never())
            ->method('isEnvironmentalComplaint')
            ->will($this->returnValue(true));

        $complaints = [];
        $complaints[] = $complaint;

        $entity = new Entity();
        $entity->setComplaints(new ArrayCollection($complaints));

        $this->assertEquals('N', $entity->getHasEnvironmentalComplaint());
    }

    public function testGetHasOpposition()
    {
        $status = 'apsts_granted_1'; // allowed

        $application = $this->createPartialMock(
            ApplicationEntity::class,
            ['getStatus', 'getId']
        );
        $application->expects($this->once())->method('getStatus')->will($this->returnSelf());
        $application->expects($this->once())->method('getId')->will($this->returnValue($status));

        $case = $this->createMock(Cases::class);
        $case->expects($this->once())->method('getApplication')->will($this->returnValue($application));

        $opposition = $this->createPartialMock(
            OppositionEntity::class,
            ['getIsWithdrawn', 'getCase']
        );

        $opposition->expects($this->once())->method('getIsWithdrawn')->will($this->returnValue(false));
        $opposition->expects($this->once())->method('getCase')->will($this->returnValue($case));

        $oppositions = [];
        $oppositions[] = $opposition;

        $entity = new Entity();
        $entity->setOppositions(new ArrayCollection($oppositions));

        $this->assertEquals('Y', $entity->getHasOpposition());
    }

    public function testGetHasOppositionNot()
    {
        $status = 'apsts_granted'; // not allowed

        $application = $this->createPartialMock(
            ApplicationEntity::class,
            ['getStatus', 'getId']
        );
        $application->expects($this->once())->method('getStatus')->will($this->returnSelf());
        $application->expects($this->once())->method('getId')->will($this->returnValue($status));

        $case = $this->createMock(Cases::class);
        $case->expects($this->once())->method('getApplication')->will($this->returnValue($application));

        $opposition = $this->createPartialMock(
            OppositionEntity::class,
            ['getIsWithdrawn', 'getCase']
        );

        $opposition->expects($this->once())->method('getIsWithdrawn')->will($this->returnValue(false));
        $opposition->expects($this->once())->method('getCase')->will($this->returnValue($case));

        $oppositions = [];
        $oppositions[] = $opposition;

        $entity = new Entity();
        $entity->setOppositions(new ArrayCollection($oppositions));

        $this->assertEquals('N', $entity->getHasOpposition());
    }

    public function testGetRelatedOrganisationNull()
    {
        $sut = (new Entity())
            ->setApplications(new ArrayCollection([]));

        static::assertNull($sut->getRelatedOrganisation());
    }

    public function testGetRelatedOrganisation()
    {
        $expectOrg = new Organisation();
        $mockApp = m::mock(ApplicationEntity\Application::class)
            ->shouldReceive('getRelatedOrganisation')->never()
            ->getMock();

        $mockApp2 = m::mock(ApplicationEntity\Application::class)
            ->shouldReceive('getRelatedOrganisation')->once()->andReturn($expectOrg)
            ->getMock();

        $sut = (new Entity())
         ->setApplications(new ArrayCollection([$mockApp, $mockApp2]));

        static::assertSame($expectOrg, $sut->getRelatedOrganisation());
    }
}
