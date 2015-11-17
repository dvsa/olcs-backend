<?php

/**
 * SubmissionBelongsToCase Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\SubmissionBelongsToCase;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Mockery as m;

/**
 * SubmissionBelongsToCase Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SubmissionBelongsToCaseTest extends AbstractValidatorsTestCase
{
    /**
     * @var SubmissionBelongsToCase
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new SubmissionBelongsToCase();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($belongsToCase, $expected)
    {
        $entity = m::mock(Submission::class);
        $entityId = 99;
        $caseId = 24;

        $repo = $this->mockRepo('Submission');
        $repo->shouldReceive('fetchById')->with($entityId)->andReturn($entity);

        $this->setIsValid('belongsToCase', [$entity, $caseId], $belongsToCase);

        $this->assertEquals($expected, $this->sut->isValid($entityId, $caseId));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }
}
