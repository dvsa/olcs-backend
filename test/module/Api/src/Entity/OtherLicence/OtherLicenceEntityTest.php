<?php

namespace Dvsa\OlcsTest\Api\Entity\OtherLicence;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence as Entity;
use Mockery as m;

/**
 * OtherLicence Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class OtherLicenceEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Test update other licence with valid data
     *
     * @dataProvider validDataProvider
     */
    public function testUpdateOtherLicenceValid($previousLicenceType)
    {
        $sut = m::mock(Entity::class)->makePartial()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn($previousLicenceType)
                ->getMock()
            )
            ->getMock();

        $result = $sut->updateOtherLicence(
            'licNo',
            'holderName',
            'Y',
            '2015-01-01',
            '2',
            '2014-01-01'
        );
        $this->assertTrue($result);
    }

    /**
     * Test update other licence with invalid data
     *
     * @dataProvider invalidDataProvider
     */
    public function testUpdateOtherLicenceInvalid(
        $previousLicenceType,
        $licNo,
        $holderName,
        $willSurrender,
        $disqualificationDate,
        $disqualificationLength,
        $purchaseDate
    ) {
        $this->setExpectedException('\Dvsa\Olcs\Api\Domain\Exception\ValidationException');

        if (!$previousLicenceType) {
            $mockPreviousLicenceType = null;
        } else {
            $mockPreviousLicenceType = m::mock()
                ->shouldReceive('getId')
                ->andReturn($previousLicenceType)
                ->getMock();
        }
        $sut = m::mock(Entity::class)->makePartial()
            ->shouldReceive('getPreviousLicenceType')
            ->andReturn($mockPreviousLicenceType)
            ->getMock();

        $sut->updateOtherLicence(
            $licNo,
            $holderName,
            $willSurrender,
            $disqualificationDate,
            $disqualificationLength,
            $purchaseDate
        );
    }

    public function validDataProvider()
    {
        return [
            [Entity::TYPE_CURRENT],
            [Entity::TYPE_APPLIED],
            [Entity::TYPE_REFUSED],
            [Entity::TYPE_REVOKED],
            [Entity::TYPE_PUBLIC_INQUIRY],
            [Entity::TYPE_DISQUALIFIED],
            [Entity::TYPE_HELD]
        ];
    }

    public function invalidDataProvider()
    {
        return [
            // field is required
            [Entity::TYPE_CURRENT, null, 'holderName', 'Y', '2015-01-01', 2, '2014-01-01'],
            // field is required
            [Entity::TYPE_DISQUALIFIED, '123', 'holderName', 'Y', '', 2, '2014-01-01'],
            // date is in future
            [Entity::TYPE_DISQUALIFIED, '123', 'holderName', 'Y', '2020-01-01', 2, '2014-01-01'],
            // empty previous licence type
            [null, '123', 'holderName', 'Y', '2020-01-01', 2, '2014-01-01'],
            // wrong previous licence type
            ['foo', '123', 'holderName', 'Y', '2020-01-01', 2, '2014-01-01'],
        ];
    }

    public function testUpdateOtherLicenceForTml()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateOtherLicenceForTml('role', 'tml', 'hpw', 'ln', 'oc', 'tav');
        $this->assertEquals($sut->getRole(), 'role');
        $this->assertEquals($sut->getTransportManagerLicence(), 'tml');
        $this->assertEquals($sut->getHoursPerWeek(), 'hpw');
        $this->assertEquals($sut->getLicNo(), 'ln');
        $this->assertEquals($sut->getOperatingCentres(), 'oc');
        $this->assertEquals($sut->getTotalAuthVehicles(), 'tav');
    }

    public function testGetRelatedOrganisationWithNoApplication()
    {
        $sut = new Entity();

        $this->assertSame(null, $sut->getRelatedOrganisation());
    }

    public function testGetRelatedOrganisationWithApplication()
    {
        $sut = new Entity();

        $mockApplication = m::mock();
        $mockApplication->shouldReceive('getLicence')
            ->once()
            ->andReturn(
                m::mock()
                ->shouldReceive('getOrganisation')
                ->andReturn('ORG1')
                ->once()
                ->getMock()
            )
            ->getMock();
        $sut->setApplication($mockApplication);

        $this->assertSame('ORG1', $sut->getRelatedOrganisation());
    }

    public function testGetRelatedOrganisationWithTmLicence()
    {
        $sut = new Entity();

        $mockTmLicence = m::mock()
            ->shouldReceive('getLicence')
            ->once()
            ->andReturn(
                m::mock()
                ->shouldReceive('getOrganisation')
                ->once()
                ->andReturn('ORG1')
                ->getMock()
            )
            ->getMock();
        $sut->setTransportManagerLicence($mockTmLicence);

        $this->assertSame('ORG1', $sut->getRelatedOrganisation());
    }

    public function testGetRelatedOrganisationWithTmApplication()
    {
        $sut = new Entity();

        $mockTmApplication = m::mock()
            ->shouldReceive('getApplication')
            ->once()
            ->andReturn(
                m::mock()
                ->shouldReceive('getLicence')
                ->once()
                ->andReturn(
                    m::mock()
                        ->shouldReceive('getOrganisation')
                        ->once()
                        ->andReturn('ORG1')
                        ->getMock()
                )
                ->getMock()
            )
            ->getMock();
        $sut->setTransportManagerApplication($mockTmApplication);

        $this->assertSame('ORG1', $sut->getRelatedOrganisation());
    }

    public function testGetRelatedOrganisationWithTransportManager()
    {
        $sut = new Entity();

        $mockTma1 = m::mock()
            ->shouldReceive('getApplication')
            ->once()
            ->andReturn(
                m::mock()
                    ->shouldReceive('getLicence')
                    ->once()
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getOrganisation')
                            ->once()
                            ->andReturn('ORG1')
                            ->getMock()
                    )
                    ->getMock()
            )
            ->getMock();

        $mockTma2 = m::mock()
            ->shouldReceive('getApplication')
            ->once()
            ->andReturn(
                m::mock()
                    ->shouldReceive('getLicence')
                    ->once()
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getOrganisation')
                            ->once()
                            ->andReturn('ORG2')
                            ->getMock()
                    )
                    ->getMock()
            )
            ->getMock();

        $mockTransportManager = m::mock()
            ->shouldReceive('getTmApplications')
            ->once()
            ->andReturn([$mockTma1, $mockTma2])
            ->getMock();

        $sut->setTransportManager($mockTransportManager);

        $this->assertSame(['ORG1', 'ORG2'], $sut->getRelatedOrganisation());
    }
}
