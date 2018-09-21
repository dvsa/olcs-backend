<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;


/**
 * IrhpCandidatePermit Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpCandidatePermitEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetDeviationData()
    {
        $licence1 = m::mock(Licence::class);
        $licence1->shouldReceive('getLicNo')->andReturn('OB1234568');
        $licence2 = m::mock(Licence::class);
        $licence2->shouldReceive('getLicNo')->andReturn('OB1111111');

        $irhpPermitApp1 = $this->createApplication($licence1);
        $irhpPermitApp1->setId(1);
        $irhpPermitApp1->setPermitsRequired(5);
        $irhpPermitApp2 = $this->createApplication($licence1);
        $irhpPermitApp2->setId(2);
        $irhpPermitApp2->setPermitsRequired(2);
        $irhpPermitApp3 = $this->createApplication($licence2);
        $irhpPermitApp3->setId(3);
        $irhpPermitApp3->setPermitsRequired(4);

        $irhpCandidatePerm1 = $this->createCandidatePermit();
        $irhpCandidatePerm1->setIrhpPermitApplication($irhpPermitApp1);
        $irhpCandidatePerm2 = $this->createCandidatePermit();
        $irhpCandidatePerm2->setIrhpPermitApplication($irhpPermitApp2);
        $irhpCandidatePerm3 = $this->createCandidatePermit();
        $irhpCandidatePerm3->setIrhpPermitApplication($irhpPermitApp3);

        $deviationData = Entity::getDeviationData([$irhpCandidatePerm1, $irhpCandidatePerm2, $irhpCandidatePerm3]);

        $this->assertEquals($deviationData['meanDeviation'], 0.6666666666666666);
        $this->assertEquals(count($deviationData['licenceData']), 2); //2 licences
        $this->assertEquals(count($deviationData['licenceData']['OB1234568']), 2); //first licence 2 apps
        $this->assertEquals(count($deviationData['licenceData']['OB1111111']), 1); //second licence 1 apps
        $this->assertEquals($deviationData['licenceData']['OB1234568'][2], 2); //second app for first licence had 2 permitsRequired

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

    private function createCandidatePermit()
    {
        return Entity::createNew(
            m::mock(IrhpPermitApplication::class),
            m::mock(IrhpPermitRange::class)
        );
    }

    private function createApplication($licence)
    {
        $entity = IrhpPermitApplication::createNew(
            m::mock(IrhpPermitWindow::class),
            $licence,
            m::mock(EcmtPermitApplication::class)
        );

        return $entity;
    }
}
