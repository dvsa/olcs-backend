<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;


/**
 * IrhpPermitApplication Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Entity(m::mock(\Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication::class));

        parent::setUp();
    }

    public function testGetCalculatedBundleValues()
    {
        $this->assertSame(
            [
                'permitsAwarded' => 0
            ],
            $this->sut->getCalculatedBundleValues()
        );
    }

    public function testGetDeviationData()
    {
        $licence1 = m::mock(Licence::class);
        $licence1->shouldReceive('getLicNo')->andReturn('OB1234568');
        $licence2 = m::mock(Licence::class);
        $licence2->shouldReceive('getLicNo')->andReturn('OB1111111');

        $irhpPermitApp1 = $this->createApplication($licence1);
        $irhpPermitApp1->setPermitsRequired(5);
        $irhpPermitApp2 = $this->createApplication($licence1);
        $irhpPermitApp2->setPermitsRequired(2);
        $irhpPermitApp3 = $this->createApplication($licence2);
        $irhpPermitApp3->setPermitsRequired(4);

        $deviationData = Entity::getDeviationData([$irhpPermitApp1, $irhpPermitApp2, $irhpPermitApp3]);

        $this->assertEquals($deviationData['meanDeviation'], 0.18181818181818);
        $this->assertEquals(count($deviationData['licenceData']), 2); //2 licences
        $this->assertEquals(count($deviationData['licenceData']['OB1234568']), 2); //first licence 2 apps
        $this->assertEquals(count($deviationData['licenceData']['OB1111111']), 1); //second licence 1 apps
        $this->assertEquals($deviationData['licenceData']['OB1234568'][1], 2); //second app for first licence had 2 permitsRequired

        $expectedFormat = [
            'licenceData' => [
                'OB1234568' => [
                    0 => 5,
                    1 => 2
                ],
                'OB1111111' => [
                    0 => 4
                ]
            ],
            'meanDeviation' => 0.18181818181818
        ];
    }


    private function createApplication($licence)
    {
        $entity = Entity::createNew(
            m::mock(IrhpPermitWindow::class),
            $licence,
            m::mock(EcmtPermitApplication::class)
        );

        return $entity;
    }
}
