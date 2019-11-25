<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval;

use DateTime;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtRemoval\PermitStartDate;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date as DateElement;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PermitStartDateTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitStartDateTest extends MockeryTestCase
{
    public function testGetRepresentation()
    {
        $dateRepresentation = [
            'value' => '2020-05-03',
        ];

        $formattedDateMustBeBeforeDateTime = '2020-03-02';

        $date = m::mock(DateElement::class);
        $date->shouldReceive('getRepresentation')
            ->andReturn($dateRepresentation);

        $dateMustBeBeforeDateTime = m::mock(DateTime::class);
        $dateMustBeBeforeDateTime->shouldReceive('format')
            ->with('Y-m-d')
            ->andReturn($formattedDateMustBeBeforeDateTime);

        $permitStartDate = new PermitStartDate($dateMustBeBeforeDateTime, $date);

        $expectedRepresentation = [
            'dateMustBeBefore' => $formattedDateMustBeBeforeDateTime,
            'date' => $dateRepresentation,
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $permitStartDate->getRepresentation()
        );
    }
}
