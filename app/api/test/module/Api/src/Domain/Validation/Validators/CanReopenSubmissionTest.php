<?php

/**
 * Can Reopen Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanReopenSubmission;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Mockery as m;

/**
 * Can Reopen Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CanReopenSubmissionTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanReopenSubmission
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanReopenSubmission();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($canReopen, $expected)
    {
        $entity = m::mock(Submission::class);

        $repo = $this->mockRepo('Submission');
        $repo->shouldReceive('fetchById')->with(99)->andReturn($entity);

        $this->setIsValid('canReopen', [$entity], $canReopen);

        $this->assertEquals($expected, $this->sut->isValid(99));
    }

    public function provider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }
}
