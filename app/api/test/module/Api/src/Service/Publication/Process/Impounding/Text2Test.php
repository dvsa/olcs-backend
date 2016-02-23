<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Impounding;

use Common\Service\Data\RefData;
use Dvsa\Olcs\Api\Service\Publication\Process\Impounding\Text2;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as OrganisationPersonEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

/**
 * Class Text2Test
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Text2Test extends MockeryTestCase
{
    /**
     *
     * @group publicationFilter
     *
     * Test the hearing text1 filter
     */
    public function testProcess()
    {

        $sut = new Text2();

        $impoundingId = 99;

        $vrm = 'AB12CDE';

        $legislation1 = m::mock(RefDataEntity::class);
        $legislation2 = m::mock(RefDataEntity::class);
        $legislation1->shouldReceive('getDescription')->andReturn('leg1');
        $legislation2->shouldReceive('getDescription')->andReturn('leg2');
        $legislationTypes = new ArrayCollection([$legislation1, $legislation2]);

        $publicationLink = m::mock(PublicationLink::class)->makePartial();
        $publicationLink->shouldReceive('getImpounding->getId')->andReturn($impoundingId);
        $publicationLink->shouldReceive('getImpounding->getVrm')->andReturn($vrm);
        $publicationLink->shouldReceive('getImpounding->getImpoundingLegislationTypes')->andReturn($legislationTypes);

        $input = [];

        $expectedString = sprintf(
            'The applicants listed above have applied for the return of %s under the following; %s',
            $vrm,
            'leg1; leg2'
        );

        $output = $sut->process($publicationLink, new ImmutableArrayObject($input));
        $this->assertEquals($expectedString, $output->getText2());
    }
}
