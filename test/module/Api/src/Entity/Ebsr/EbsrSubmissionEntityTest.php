<?php

namespace Dvsa\OlcsTest\Api\Entity\Ebsr;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * EbsrSubmission Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class EbsrSubmissionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * tests entity creation
     */
    public function testCreate()
    {
        $document = m::mock(DocumentEntity::class);
        $organisation = m::mock(OrganisationEntity::class);

        //setting ids here is a way to make the objects unique
        $ebsrSubmissionStatus = m::mock(RefData::class)->makePartial();
        $ebsrSubmissionStatus->setId('some id');
        $ebsrSubmissionType = m::mock(RefData::class)->makePartial();
        $ebsrSubmissionType->setId('some other id');

        $entity = new Entity($organisation, $ebsrSubmissionStatus, $ebsrSubmissionType, $document);

        $this->assertEquals($organisation, $entity->getOrganisation());
        $this->assertEquals($document, $entity->getDocument());
        $this->assertEquals($ebsrSubmissionStatus, $entity->getEbsrSubmissionStatus());
        $this->assertEquals($ebsrSubmissionType, $entity->getEbsrSubmissionType());
    }

    /**
     * tests submit
     */
    public function testSubmit()
    {
        //setting ids here is a way to make the objects unique
        $ebsrSubmissionStatus = m::mock(RefData::class)->makePartial();
        $ebsrSubmissionStatus->setId('some id');
        $ebsrSubmissionType = m::mock(RefData::class)->makePartial();
        $ebsrSubmissionType->setId('some other id');

        $entity = $this->instantiate(Entity::class);

        $entity->submit($ebsrSubmissionStatus, $ebsrSubmissionType);

        $this->assertEquals($ebsrSubmissionStatus, $entity->getEbsrSubmissionStatus());
        $this->assertEquals($ebsrSubmissionType, $entity->getEbsrSubmissionType());
        $this->assertInstanceOf(\DateTime::class, $entity->getSubmittedDate());
    }

    /**
     * tests beginValidating
     */
    public function testBeginValidating()
    {
        $ebsrSubmissionStatus = m::mock(RefData::class)->makePartial();

        $entity = $this->instantiate(Entity::class);

        $entity->beginValidating($ebsrSubmissionStatus);

        $this->assertEquals($ebsrSubmissionStatus, $entity->getEbsrSubmissionStatus());
        $this->assertInstanceOf(\DateTime::class, $entity->getValidationStart());
    }

    /**
     * tests finishValidating
     */
    public function testFinishValidatingWithFailure()
    {
        $ebsrSubmissionStatus = m::mock(RefData::class)->makePartial();
        $ebsrSubmissionStatus->setId(Entity::FAILED_STATUS);

        $ebsrSubmissionResult = ['submission result'];

        $entity = $this->instantiate(Entity::class);

        $entity->finishValidating($ebsrSubmissionStatus, $ebsrSubmissionResult);

        $this->assertEquals($ebsrSubmissionStatus, $entity->getEbsrSubmissionStatus());
        $this->assertEquals($ebsrSubmissionResult, $entity->getEbsrSubmissionResult());
        $this->assertInstanceOf(\DateTime::class, $entity->getValidationEnd());
        $this->assertNull($entity->getProcessStart());
    }

    /**
     * tests finishValidating
     */
    public function testFinishValidatingNoFailure()
    {
        $ebsrSubmissionStatus = m::mock(RefData::class)->makePartial();
        $ebsrSubmissionResult = ['submission result'];

        $entity = $this->instantiate(Entity::class);

        $entity->finishValidating($ebsrSubmissionStatus, $ebsrSubmissionResult);

        $this->assertEquals($ebsrSubmissionStatus, $entity->getEbsrSubmissionStatus());
        $this->assertEquals($ebsrSubmissionResult, $entity->getEbsrSubmissionResult());
        $this->assertInstanceOf(\DateTime::class, $entity->getValidationEnd());
        $this->assertEquals($entity->getProcessStart(), $entity->getValidationEnd());
    }

    /**
     * tests finishProcessing
     */
    public function testFinishProcessing()
    {
        $ebsrSubmissionStatus = m::mock(RefData::class)->makePartial();

        $entity = $this->instantiate(Entity::class);

        $entity->finishProcessing($ebsrSubmissionStatus);

        $this->assertEquals($ebsrSubmissionStatus, $entity->getEbsrSubmissionStatus());
        $this->assertInstanceOf(\DateTime::class, $entity->getProcessEnd());
    }

    /**
     * @dataProvider isFailureProvider
     *
     * @param $submissionStatusString
     * @param $expectedResult
     */
    public function testIsFailure($submissionStatusString, $expectedResult)
    {
        $ebsrSubmissionStatus = m::mock(RefData::class);
        $ebsrSubmissionStatus->shouldReceive('getId')->once()->andReturn($submissionStatusString);

        $entity = $this->instantiate(Entity::class);
        $entity->setEbsrSubmissionStatus($ebsrSubmissionStatus);

        $this->assertEquals($expectedResult, $entity->isFailure());
    }

    /**
     * Date provider for isFailure
     *
     * @return array
     */
    public function isFailureProvider()
    {
        return [
            [Entity::SUBMITTING_STATUS, false],
            [Entity::SUBMITTED_STATUS, false],
            [Entity::VALIDATING_STATUS, false],
            [Entity::PROCESSING_STATUS, false],
            [Entity::PROCESSED_STATUS, false],
            [Entity::FAILED_STATUS, true]
        ];
    }

    /**
     * @dataProvider isDataRefreshProvider
     *
     * @param string $status
     * @param bool $result
     */
    public function testIsDataRefresh($status, $result)
    {
        $entity = m::mock(Entity::class)->makePartial();
        $submissionType = new RefData($status);
        $entity->setEbsrSubmissionType($submissionType);
        $this->assertEquals($result, $entity->isDataRefresh());
    }

    /**
     * Data provider for testIsDataRefresh
     *
     * @return array
     */
    public function isDataRefreshProvider()
    {
        return [
            [Entity::DATA_REFRESH_SUBMISSION_TYPE, true],
            [Entity::NEW_SUBMISSION_TYPE, false]
        ];
    }
}
