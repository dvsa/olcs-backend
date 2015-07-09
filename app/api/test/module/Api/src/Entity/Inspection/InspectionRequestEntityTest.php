<?php

namespace Dvsa\OlcsTest\Api\Entity\Inspection;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Inspection\InspectionRequest as Entity;
use Mockery as m;
use \Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * InspectionRequest Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class InspectionRequestEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateInspectionRequest()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateInspectionRequest(
            'req_type',
            null,
            null,
            3,
            'res_type',
            'requestor_notes',
            'rep_type',
            1,
            2,
            3,
            4,
            'insp_name',
            '01/01/2015',
            '01/01/2016',
            '01/01/2017',
            5,
            6,
            'inspector_notes'
        );
        $this->assertEquals('req_type', $sut->getRequestType());
        $this->assertEquals(new \DateTime(), $sut->getRequestDate());

        $this->assertEquals(
            (new DateTime())->add(new \DateInterval('P3M')),
            $sut->getDueDate()
        );
        $this->assertEquals('res_type', $sut->getResultType());
        $this->assertEquals('requestor_notes', $sut->getRequestorNotes());
        $this->assertEquals('rep_type', $sut->getReportType());
        $this->assertEquals(1, $sut->getApplication());
        $this->assertEquals(2, $sut->getLicence());
        $this->assertEquals(3, $sut->getRequestorUser());
        $this->assertEquals(4, $sut->getOperatingCentre());
        $this->assertEquals('insp_name', $sut->getInspectorName());
        $this->assertEquals(new \DateTime('01/01/2015'), $sut->getReturnDate());
        $this->assertEquals(new \DateTime('01/01/2016'), $sut->getFromDate());
        $this->assertEquals(new \DateTime('01/01/2017'), $sut->getToDate());
        $this->assertEquals(5, $sut->getVehiclesExaminedNo());
        $this->assertEquals(6, $sut->getTrailersExaminedNo());
        $this->assertEquals('inspector_notes', $sut->getInspectorNotes());
    }

    public function testUpdateInspectionRequestNotValid()
    {
        $this->setExpectedException(
            ValidationException::class,
            [
                'reportType' => [Entity::ERROR_FIELD_IS_REQUIRED => 'Field is required'],
                'resultType' => [Entity::ERROR_FIELD_IS_REQUIRED => 'Field is required'],
                'requestorUser' => [Entity::ERROR_FIELD_IS_REQUIRED => 'Field is required'],
                'dueDate' => [
                    Entity::ERROR_FIELD_IS_REQUIRED => 'Field is required',
                    Entity::ERROR_DUE_DATE => 'Due date should be the same or after date requested'
                ]
            ]
        );
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateInspectionRequest(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }

    public function testUpdateInspectionRequestDueDateNotInRange()
    {
        $this->setExpectedException(
            ValidationException::class,
            [
                'reportType' => [Entity::ERROR_FIELD_IS_REQUIRED => 'Field is required'],
                'resultType' => [Entity::ERROR_FIELD_IS_REQUIRED => 'Field is required'],
                'requestorUser' => [Entity::ERROR_FIELD_IS_REQUIRED => 'Field is required'],
                'dueDate' => [
                    Entity::ERROR_DUE_DATE_NOT_IN_RANGE => 'Due date not in range'
                ]
            ]
        );
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateInspectionRequest(
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }
}
