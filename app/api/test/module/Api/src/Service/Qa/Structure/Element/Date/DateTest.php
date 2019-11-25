<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * DateTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class DateTest extends MockeryTestCase
{
    public function testGetRepresentation()
    {
        $value = '2020-05-01';

        $date = new Date($value);

        $expectedRepresentation = [
            'value' => $value
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $date->getRepresentation()
        );
    }
}
