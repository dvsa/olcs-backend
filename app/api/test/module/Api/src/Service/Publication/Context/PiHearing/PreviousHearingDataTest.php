<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Service\Publication\Context\PiHearing\PreviousHearingData;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

/**
 * Class PreviousHearingDataTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousHearingDataTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the previous hearing date filter
     */
    public function testProvide()
    {
        $pi = 99;
        $hearingDate = '2014-03-16 14:30:00';
        $adjournedDate = '2014-03-18 12:45:00';
        $formattedAdjournedDate = '18 March 2014';

        $input = [
            'hearingDate' => $hearingDate,
        ];

        $output = [
            'hearingDate' => $hearingDate,
            'previousHearing' => $formattedAdjournedDate
        ];

        $expectedOutput = new \ArrayObject($output);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getPi->getId')->once()->andReturn($pi);

        $piHearingResult = m::mock();
        $piHearingResult->shouldReceive('serialize')
            ->andReturn(['adjournedDate' => $adjournedDate])
            ->once()
            ->getMock();

        $mockQueryHandler = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class);
        $mockQueryHandler->shouldReceive('handleQuery')->once()->andReturn($piHearingResult);

        $sut = new PreviousHearingData($mockQueryHandler);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject($input)));
    }
}
