<?php

namespace Dvsa\OlcsTest\Api\Entity\OperatingCentre;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre as Entity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Dvsa\Olcs\Api\Entity\Opposition\Opposition as OppositionEntity;
use Dvsa\Olcs\Api\Entity\Application as ApplicationEntity;

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
        $complaint = $this->getMock(
            ComplaintEntity::class,
            ['getClosedDate', 'isEnvironmentalComplaint'], // methods
            [], // constructor arguments
            '', // mock class name
            false // call constuctor
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
        $complaint = $this->getMock(
            ComplaintEntity::class,
            ['getClosedDate', 'isEnvironmentalComplaint'], // methods
            [], // constructor arguments
            '', // mock class name
            false // call constuctor
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

        $application = $this->getMock(
            ApplicationEntity::class,
            ['getIsWithdrawn', 'getStatus', 'getId'], // methods
            [], // constructor arguments
            '', // mock class name
            false // call constuctor
        );
        $application->expects($this->once())->method('getStatus')->will($this->returnSelf());
        $application->expects($this->once())->method('getId')->will($this->returnValue($status));

        $opposition = $this->getMock(
            OppositionEntity::class,
            ['getIsWithdrawn', 'getCase', 'getApplication'], // methods
            [], // constructor arguments
            '', // mock class name
            false // call constuctor
        );

        $opposition->expects($this->once())->method('getIsWithdrawn')->will($this->returnValue(false));
        $opposition->expects($this->once())->method('getCase')->will($this->returnSelf());
        $opposition->expects($this->once())
                   ->method('getApplication')->will($this->returnValue($application));

        $oppositions = [];
        $oppositions[] = $opposition;

        $entity = new Entity();
        $entity->setOppositions(new ArrayCollection($oppositions));

        $this->assertEquals('Y', $entity->getHasOpposition());
    }

    public function testGetHasOppositionNot()
    {
        $status = 'apsts_granted'; // not allowed

        $application = $this->getMock(
            ApplicationEntity::class,
            ['getIsWithdrawn', 'getStatus', 'getId'], // methods
            [], // constructor arguments
            '', // mock class name
            false // call constuctor
        );
        $application->expects($this->once())->method('getStatus')->will($this->returnSelf());
        $application->expects($this->once())->method('getId')->will($this->returnValue($status));

        $opposition = $this->getMock(
            OppositionEntity::class,
            ['getIsWithdrawn', 'getCase', 'getApplication'], // methods
            [], // constructor arguments
            '', // mock class name
            false // call constuctor
        );

        $opposition->expects($this->once())->method('getIsWithdrawn')->will($this->returnValue(false));
        $opposition->expects($this->once())->method('getCase')->will($this->returnSelf());
        $opposition->expects($this->once())
            ->method('getApplication')->will($this->returnValue($application));

        $oppositions = [];
        $oppositions[] = $opposition;

        $entity = new Entity();
        $entity->setOppositions(new ArrayCollection($oppositions));

        $this->assertEquals('N', $entity->getHasOpposition());
    }
}
