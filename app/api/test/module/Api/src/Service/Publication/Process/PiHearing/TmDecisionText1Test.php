<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\PiHearing;

use Dvsa\Olcs\Api\Service\Publication\Process\PiHearing\TmDecisionText1;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

/**
 * Class TmDecisionText1Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TmDecisionText1Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the TmDecisionText1 filter
     */
    public function testProcess()
    {
        $sut = new TmDecisionText1();

        $pi = 99;
        $piVenueOther = 'Pi Venue Information';
        $hearingDate = '12 May 2014';
        $hearingTime = '14:30';
        $caseId = 84;
        $transportManagerName = 'transport manager name';

        $input = [
            'id' => $pi,
            'piVenueOther' => $piVenueOther,
            'formattedHearingDate' => $hearingDate,
            'formattedHearingTime' => $hearingTime,
            'transportManagerName' => $transportManagerName
        ];

        $publicationLink = m::mock(PublicationLink::class)->makePartial();
        $publicationLink->shouldReceive('getPi->getCase->getId')->andReturn($caseId);

        $expectedString = sprintf(
            'TM Public Inquiry (Case ID: %s, Public Inquiry ID: %s) for %s held at %s,
    on %s at %s',
            $caseId,
            $pi,
            $transportManagerName,
            $piVenueOther,
            $hearingDate,
            $hearingTime
        );

        $output = $sut->process($publicationLink, new ImmutableArrayObject($input));
        $this->assertEquals($expectedString, $output->getText1());
    }
}
