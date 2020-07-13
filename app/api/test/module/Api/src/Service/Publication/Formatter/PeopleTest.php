<?php

namespace Dvsa\OlcsTest\Api\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Date;
use Dvsa\Olcs\Api\Service\Publication\Formatter\People as Formatter;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Person\Person;

/**
 * PeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PeopleTest extends MockeryTestCase
{
    protected $sut;

    public function setUp(): void
    {
    }

    public function testSoletrader()
    {
        $organisation = new Organisation();
        $organisation->setType(new RefData(Organisation::ORG_TYPE_SOLE_TRADER));

        $this->assertNull(Formatter::format($organisation, []));
    }

    public function testNotPersons()
    {
        $organisation = new Organisation();
        $organisation->setType(new RefData(Organisation::ORG_TYPE_LLP));

        $this->assertNull(Formatter::format($organisation, []));
    }

    public function testLtd()
    {
        $organisation = new Organisation();
        $organisation->setType(new RefData(Organisation::ORG_TYPE_REGISTERED_COMPANY));

        $person1 = (new Person())->setForename('Reggie')->setFamilyName('Kray');
        $person2 = (new Person())->setForename('Ronnie')->setFamilyName('May');

        $this->assertSame(
            'Director(s): Reggie Kray, Ronnie May',
            Formatter::format($organisation, [$person1, $person2])
        );
    }

    public function testLlp()
    {
        $organisation = new Organisation();
        $organisation->setType(new RefData(Organisation::ORG_TYPE_LLP));

        $person1 = (new Person())->setForename('Reggie')->setFamilyName('Kray');
        $person2 = (new Person())->setForename('Ronnie')->setFamilyName('May');

        $this->assertSame(
            'Partner(s): Reggie Kray, Ronnie May',
            Formatter::format($organisation, [$person1, $person2])
        );
    }

    public function testPartnership()
    {
        $organisation = new Organisation();
        $organisation->setType(new RefData(Organisation::ORG_TYPE_PARTNERSHIP));

        $person1 = (new Person())->setForename('Reggie')->setFamilyName('Kray');
        $person2 = (new Person())->setForename('Ronnie')->setFamilyName('May');

        $this->assertSame(
            'Partner(s): Reggie Kray, Ronnie May',
            Formatter::format($organisation, [$person1, $person2])
        );
    }

    public function testOther()
    {
        $organisation = new Organisation();
        $organisation->setType(new RefData(Organisation::ORG_TYPE_OTHER));

        $person1 = (new Person())->setForename('Reggie')->setFamilyName('Kray');
        $person2 = (new Person())->setForename('Ronnie')->setFamilyName('May');

        $this->assertSame(
            'Reggie Kray, Ronnie May',
            Formatter::format($organisation, [$person1, $person2])
        );
    }
}
