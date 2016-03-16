<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\TransportManager;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Service\Publication\Process\TransportManager\Police;
use Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData;
use Dvsa\Olcs\Api\Entity\Person\Person;

/**
 * Class PoliceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PoliceTest extends MockeryTestCase
{
    public function testProcess()
    {
        $sut = new Police();

        $person1 = m::mock(Person::class)->makePartial();
        $person2 = m::mock(Person::class)->makePartial();
        $person3 = m::mock(Person::class)->makePartial();

        $publicationLink = new PublicationLink();
        $publicationLink->addPoliceDatas(
            new PublicationPoliceData($publicationLink, $person1)
        );

        $input = [
            'tmPeople' => [$person2, $person3],
        ];
        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame($publicationLink->getPoliceDatas()->count(), 2);
        $this->assertSame($publicationLink->getPoliceDatas()[0]->getPerson(), $person2);
        $this->assertSame($publicationLink->getPoliceDatas()[1]->getPerson(), $person3);
    }
}
