<?php

/**
 * Update Trading Names Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\TradingName;
use Dvsa\Olcs\Api\Domain\CommandHandler\Organisation\UpdateTradingNames;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Organisation\UpdateTradingNames as Cmd;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\TradingName as TradingNameEntity;

/**
 * Update Trading Names Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTradingNamesTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateTradingNames();
        $this->mockRepo('Organisation', Organisation::class);
        $this->mockRepo('Licence', Licence::class);
        $this->mockRepo('TradingName', TradingName::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [

        ];

        $this->references = [
            TradingNameEntity::class => [
                1 => m::mock(TradingNameEntity::class),
                2 => m::mock(TradingNameEntity::class)
            ]
        ];

        parent::initReferences();

        $this->references[TradingNameEntity::class][1]->setName('Foo');
        $this->references[TradingNameEntity::class][2]->setName('Bar');
    }

    public function testHandleCommandWithLicence()
    {
        $data = [
            'licence' => 111,
            'tradingNames' => [
                'Foo',
                'Cake'
            ]
        ];
        $command = Cmd::create($data);

        $tradingNames = new ArrayCollection();
        $tradingNames->add($this->references[TradingNameEntity::class][1]);// Foo
        $tradingNames->add($this->references[TradingNameEntity::class][2]);// Bar

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setOrganisation($organisation);
        $licence->setTradingNames($tradingNames);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        /** @var TradingNameEntity $createdTradingName */
        $createdTradingName = null;

        $this->repoMap['TradingName']->shouldReceive('delete')
            ->once()
            ->with($this->references[TradingNameEntity::class][2])
            ->shouldReceive('save')
            ->with(m::type(TradingNameEntity::class))
            ->andReturnUsing(
                function (TradingNameEntity $tradingName) use (&$createdTradingName) {
                    $createdTradingName = $tradingName;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '1 new trading name(s)',
                '1 unchanged trading name(s)',
                '1 trading name(s) removed'
            ],
            'flags' => ['hasChanged' => 1]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertTrue($result->getFlag('hasChanged'));

        $this->assertCount(2, $tradingNames);
        $this->assertTrue($tradingNames->contains($this->references[TradingNameEntity::class][1]));

        $this->assertSame($licence, $createdTradingName->getLicence());
    }

    public function testHandleCommandWithoutLicenceWithoutChange()
    {
        $data = [
            'organisation' => 111,
            'tradingNames' => [
                'Foo',
                'Bar'
            ]
        ];
        $command = Cmd::create($data);

        $tradingNames = m::mock(ArrayCollection::class)->makePartial();
        $tradingNames->add($this->references[TradingNameEntity::class][1]);// Foo
        $tradingNames->add($this->references[TradingNameEntity::class][2]);// Bar
        $tradingNames->shouldReceive('matching')
            ->with(m::type(Criteria::class))
            ->andReturnSelf();

        /** @var OrganisationEntity $organisation */
        $organisation = m::mock(OrganisationEntity::class)->makePartial();
        $organisation->setTradingNames($tradingNames);

        $this->repoMap['Organisation']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($organisation);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Trading names are unchanged'
            ],
            'flags' => ['hasChanged' => false]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertFalse($result->getFlag('hasChanged'));
    }
}
