<?php

namespace Dvsa\OlcsTest\Api\Domain\Validation\Validators;

use Dvsa\Olcs\Api\Domain\Validation\Validators\CanAssignSubmission;

class CanAssignSubmissionTest extends \PHPUnit_Framework_TestCase
{

    private $sut;

    public function setUp()
    {
        $this->sut = new CanAssignSubmission();
    }

    /**
     * testIsValid
     * @param $firstAssignedDate
     * @param $informationCompleteDate
     * @param $expects
     * @dataProvider dateDataProvider
     */
    public function testIsValid($firstAssignedDate, $informationCompleteDate, $expects)
    {
        $this->assertEquals($this->sut->isValid($firstAssignedDate, $informationCompleteDate),$expects);
    }

    protected function dateDataProvider()
    {
        return [

            ['2018-01-01', '2018-12-31', true],
            ['2018-01-01', '2017-12-31', false],
            ['2018-01-01', '2018-01-01', true],
        ];
    }
}
