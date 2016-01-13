<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Variation;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Text2Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Text2Test extends MockeryTestCase
{
    /**
     * @var \Dvsa\Olcs\Api\Service\Publication\Process\Variation\Text2
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new \Dvsa\Olcs\Api\Service\Publication\Process\Variation\Text2();

        parent::setUp();
    }

    /**
     * @param string $organisationType
     *
     * @return PublicationLink
     */
    private function getPublicationLink($organisationType)
    {
        $publicationLink = new PublicationLink();

        $organisation = new Organisation();
        $organisation->setName('ORG_NAME');
        $organisation->setType(new RefData($organisationType));

        $licence = new Licence($organisation, new RefData());
        $publicationLink->setLicence($licence);

        return $publicationLink;
    }

    private function addTradingName(PublicationLink $publicationLink, $id, $name)
    {
        $organisation = $publicationLink->getLicence()->getOrganisation();
        $tradingName = new \Dvsa\Olcs\Api\Entity\Organisation\TradingName($name, $organisation);
        $tradingName->setId($id);
        $organisation->addTradingNames($tradingName);
    }

    public function testSoleTrader()
    {
        $publicationLink = $this->getPublicationLink(Organisation::ORG_TYPE_SOLE_TRADER);
        $context = new ImmutableArrayObject([]);

        $this->sut->process($publicationLink, $context);

        $expectedText2 = "ORG_NAME";

        $this->assertSame($expectedText2, $publicationLink->getText2());
    }

    public function testTradingName()
    {
        $publicationLink = $this->getPublicationLink(Organisation::ORG_TYPE_SOLE_TRADER);
        $this->addTradingName($publicationLink, 12, 'TRADING_NAME_12');
        $this->addTradingName($publicationLink, 120, 'TRADING_NAME_120');
        $this->addTradingName($publicationLink, 4, 'TRADING_NAME_5');
        $context = new ImmutableArrayObject([]);

        $this->sut->process($publicationLink, $context);

        $expectedText2 = "ORG_NAME T/A TRADING_NAME_5";

        $this->assertSame($expectedText2, $publicationLink->getText2());
    }

    /**
     * @dataProvider dataProviderTestPeople
     */
    public function testPeople($organisationTypeId, $peoplePrefix)
    {
        $publicationLink = $this->getPublicationLink($organisationTypeId);
        $context = new ImmutableArrayObject(
            [
                'applicationPeople' => [
                    (new \Dvsa\Olcs\Api\Entity\Person\Person())->setForename('Randy')->setFamilyName('Couture'),
                    (new \Dvsa\Olcs\Api\Entity\Person\Person())->setForename('Rachel')->setFamilyName('Jones'),
                ]
            ]
        );

        $this->sut->process($publicationLink, $context);

        $expectedText2 = "ORG_NAME\n{$peoplePrefix}Randy Couture, Rachel Jones";

        $this->assertSame($expectedText2, $publicationLink->getText2());
    }

    public function dataProviderTestPeople()
    {
        return [
            [Organisation::ORG_TYPE_LLP, 'Partner(s): '],
            [Organisation::ORG_TYPE_OTHER, ''],
            [Organisation::ORG_TYPE_PARTNERSHIP, 'Partner(s): '],
            [Organisation::ORG_TYPE_REGISTERED_COMPANY, 'Director(s): '],
        ];
    }

    public function testPeopleMissingContext()
    {
        $publicationLink = $this->getPublicationLink(Organisation::ORG_TYPE_OTHER);
        $context = new ImmutableArrayObject(
            [
                'applicationPeople' => 'Foo'
            ]
        );

        $this->sut->process($publicationLink, $context);

        $expectedText2 = "ORG_NAME";

        $this->assertSame($expectedText2, $publicationLink->getText2());
    }
}
