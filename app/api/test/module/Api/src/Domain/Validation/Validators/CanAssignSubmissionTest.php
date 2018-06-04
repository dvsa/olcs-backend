<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAssignSubmission;
use Dvsa\Olcs\Transfer\Command\Submission\AssignSubmission;

class CanAssignSubmissionTest extends \PHPUnit_Framework_TestCase
{

    private $sut;

    public function setUp()
    {
        $this->sut = new CanAssignSubmission();
    }

    /**
     * testIsValid
     *
     * @dataProvider dateDataProvider
     */
    public function testIsValid($firstAssignedDate, $informationCompleteDate, $expects)
    {
        $this->assertEquals($this->sut->isValid($firstAssignedDate, $informationCompleteDate), $expects);
    }

    public function dateDataProvider()
    {
        $cmd = new class('Anonymous') extends AssignSubmission
        {
            protected $dateFirstAssigned = '2018-01-01';
        };


        return [

            [$cmd, '2018-12-31', true],
            [$cmd, '2017-12-31', false],
            [$cmd, '2018-01-01', false],
        ];
    }
}
