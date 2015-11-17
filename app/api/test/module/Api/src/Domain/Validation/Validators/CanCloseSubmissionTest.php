<?php

/**
 * Can Close Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanCloseSubmission;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Mockery as m;

/**
 * Can Close Submission Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CanCloseSubmissionTest extends AbstractValidatorsTestCase
{
    /**
     * @var CanCloseSubmission
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CanCloseSubmission();

        parent::setUp();
    }

    /**
     * @dataProvider provider
     */
    public function testIsValid($canClose, $expected)
    {
        $entity = m::mock(Submission::class);

        $repo = $this->mockRepo('Submission');
        $repo->shouldReceive('fetchById')->with(99)->andReturn($entity);

        $this->setIsValid('canClose', [$entity], $canClose);

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
