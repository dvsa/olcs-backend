<?php

namespace Dvsa\OlcsTest\Api\Entity\Submission;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Submission\Submission as Entity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Mockery as m;

/**
 * Submission Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class SubmissionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = $this->instantiate($this->entityClass);
    }

    public function testConstructor()
    {
        $case = m::mock(CaseEntity::class)->makePartial();

        $submissionType = m::mock(RefDataEntity::class)->makePartial();

        $submission = new Entity($case, $submissionType);

        $this->assertSame($submissionType, $submission->getSubmissionType());
        $this->assertSame($case, $submission->getCase());
    }

    public function testClose()
    {
        $case = m::mock(CaseEntity::class)->makePartial();

        $submissionType = m::mock(RefDataEntity::class)->makePartial();

        $submission = new Entity($case, $submissionType);

        $this->assertFalse($submission->isClosed());
        $this->assertTrue($submission->canClose());

        $submission->setClosedDate(new \DateTime('now'));
        $this->assertFalse($submission->canClose());
        $this->assertTrue($submission->isClosed());
    }

    public function testReopen()
    {
        $case = m::mock(CaseEntity::class)->makePartial();
        $submissionType = m::mock(RefDataEntity::class)->makePartial();
        $submission = new Entity($case, $submissionType);

        $this->assertFalse($submission->canReopen());

        $submission->setClosedDate(new \DateTime('now'));
        $this->assertTrue($submission->canReopen());
    }
}
